<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InvoiceService
{
    /**
     * Get paginated invoices with filters and search.
     */
    public function getPaginatedInvoices(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = Invoice::with(['customer', 'items']);

        // Apply search
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Apply filters
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($paymentStatus = $request->get('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($customerId = $request->get('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        // Date range filters
        if ($startDate = $request->get('start_date')) {
            $query->where('invoice_date', '>=', $startDate);
        }

        if ($endDate = $request->get('end_date')) {
            $query->where('invoice_date', '<=', $endDate);
        }

        // Due date filters
        if ($dueDateFrom = $request->get('due_date_from')) {
            $query->where('due_date', '>=', $dueDateFrom);
        }

        if ($dueDateTo = $request->get('due_date_to')) {
            $query->where('due_date', '<=', $dueDateTo);
        }

        // Overdue filter
        if ($request->get('overdue') === '1') {
            $query->whereNotNull('due_date')
                  ->where('due_date', '<', Carbon::today())
                  ->whereNotIn('status', ['paid', 'cancelled'])
                  ->whereNotIn('payment_status', ['paid']);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if (in_array($sortField, ['invoice_number', 'invoice_date', 'due_date', 'total_amount', 'status', 'created_at'])) {
            $query->orderBy($sortField, $sortOrder);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get filter options for invoice listing.
     */
    public function getFilterOptions(): array
    {
        return [
            'customers' => Customer::active()
                                 ->orderBy('first_name')
                                 ->get()
                                 ->map(function ($customer) {
                                     return [
                                         'id' => $customer->customer_id,
                                         'name' => $customer->display_name,
                                     ];
                                 }),
            'products' => Product::orderBy('product_name')
                               ->get()
                               ->map(function ($product) {
                                   return [
                                       'id' => $product->product_id,
                                       'name' => $product->product_name . ' - ' . $product->product_brand,
                                       'price' => $product->price,
                                       'stock' => $product->quantity,
                                   ];
                               }),
        ];
    }

    /**
     * Create a new invoice.
     */
    public function createInvoice(array $data): Invoice
    {
        // Set defaults
        $data['invoice_date'] = $data['invoice_date'] ?? Carbon::today();
        $data['status'] = $data['status'] ?? 'draft';
        $data['payment_status'] = $data['payment_status'] ?? 'pending';
        $data['tax_amount'] = $data['tax_amount'] ?? 0;
        $data['discount_amount'] = $data['discount_amount'] ?? 0;
        $data['paid_amount'] = $data['paid_amount'] ?? 0;

        // Calculate due date from payment terms if not provided
        if (!isset($data['due_date']) && isset($data['payment_terms'])) {
            $data['due_date'] = Carbon::parse($data['invoice_date'])->addDays($data['payment_terms']);
        }

        // Create the invoice
        $invoice = Invoice::create($data);

        // Add items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            $this->addItemsToInvoice($invoice, $data['items']);
        }

        return $invoice->fresh(['customer', 'items.product']);
    }

    /**
     * Update an invoice.
     */
    public function updateInvoice(Invoice $invoice, array $data): Invoice
    {
        // Calculate due date from payment terms if not provided
        if (!isset($data['due_date']) && isset($data['payment_terms'])) {
            $data['due_date'] = Carbon::parse($data['invoice_date'])->addDays($data['payment_terms']);
        }

        // Update invoice details
        $invoice->update($data);

        // Update items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            $this->updateInvoiceItems($invoice, $data['items']);
        }

        return $invoice->fresh(['customer', 'items.product']);
    }

    /**
     * Add items to an invoice.
     */
    public function addItemsToInvoice(Invoice $invoice, array $items): void
    {
        foreach ($items as $itemData) {
            $product = Product::find($itemData['product_id']);
            
            if (!$product) {
                continue;
            }

            // Use product price if unit price not provided
            $unitPrice = $itemData['unit_price'] ?? $product->price;
            $quantity = $itemData['quantity'];
            $discountAmount = $itemData['discount_amount'] ?? 0;

            InvoiceItem::create([
                'invoice_id' => $invoice->invoice_id,
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount_amount' => $discountAmount,
                'line_total' => ($quantity * $unitPrice) - $discountAmount,
                'notes' => $itemData['notes'] ?? null,
            ]);
        }

        // Recalculate invoice totals
        $invoice->calculateTotals();
    }

    /**
     * Update invoice items.
     */
    public function updateInvoiceItems(Invoice $invoice, array $items): void
    {
        // Delete existing items
        $invoice->items()->delete();

        // Add new items
        $this->addItemsToInvoice($invoice, $items);
    }

    /**
     * Delete an invoice.
     */
    public function deleteInvoice(Invoice $invoice): bool
    {
        // Check if invoice can be deleted
        if (!$invoice->canBeEdited()) {
            throw new \Exception('Cannot delete invoice that is already sent or paid.');
        }

        // Delete invoice items first (cascade should handle this, but being explicit)
        $invoice->items()->delete();

        return $invoice->delete();
    }

    /**
     * Change invoice status.
     */
    public function changeInvoiceStatus(Invoice $invoice, string $status): Invoice
    {
        $validTransitions = $this->getValidStatusTransitions($invoice->status);
        
        if (!in_array($status, $validTransitions)) {
            throw new \Exception("Cannot change status from {$invoice->status} to {$status}");
        }

        $invoice->update(['status' => $status]);

        return $invoice->fresh();
    }

    /**
     * Get valid status transitions for current status.
     */
    public function getValidStatusTransitions(string $currentStatus): array
    {
        return match($currentStatus) {
            'draft' => ['sent', 'cancelled'],
            'sent' => ['paid', 'overdue', 'cancelled'],
            'overdue' => ['paid', 'cancelled'],
            'paid' => [],
            'cancelled' => [],
            default => [],
        };
    }

    /**
     * Mark invoice as sent.
     */
    public function markAsSent(Invoice $invoice): Invoice
    {
        if (!$invoice->canBeSent()) {
            throw new \Exception('Invoice cannot be sent in its current state.');
        }

        $invoice->markAsSent();
        return $invoice->fresh();
    }

    /**
     * Record payment for invoice.
     */
    public function recordPayment(Invoice $invoice, float $amount, string $method, ?string $notes = null): Invoice
    {
        if (!$invoice->canRecordPayment()) {
            throw new \Exception('Cannot record payment for this invoice.');
        }

        if ($amount > $invoice->remaining_balance) {
            throw new \Exception('Payment amount cannot exceed remaining balance.');
        }

        $invoice->recordPayment($amount, $method, $notes);
        return $invoice->fresh();
    }

    /**
     * Create invoice from sales order.
     */
    public function createFromSalesOrder(SalesOrder $salesOrder): Invoice
    {
        $salesOrder->load(['customer', 'items.product']);

        $invoiceData = [
            'customer_id' => $salesOrder->customer_id,
            'sales_order_id' => $salesOrder->order_id,
            'invoice_date' => Carbon::today(),
            'billing_address' => $salesOrder->billing_address,
            'notes' => "Invoice for Sales Order: {$salesOrder->order_number}",
            'subtotal' => $salesOrder->subtotal,
            'tax_amount' => $salesOrder->tax_amount,
            'discount_amount' => $salesOrder->discount_amount,
            'total_amount' => $salesOrder->total_amount,
        ];

        $invoice = Invoice::create($invoiceData);

        // Copy items from sales order
        foreach ($salesOrder->items as $orderItem) {
            InvoiceItem::create([
                'invoice_id' => $invoice->invoice_id,
                'product_id' => $orderItem->product_id,
                'quantity' => $orderItem->quantity,
                'unit_price' => $orderItem->unit_price,
                'discount_amount' => $orderItem->discount_amount,
                'line_total' => $orderItem->line_total,
                'notes' => $orderItem->notes,
            ]);
        }

        return $invoice->fresh(['customer', 'items.product']);
    }

    /**
     * Duplicate an invoice.
     */
    public function duplicateInvoice(Invoice $invoice): Invoice
    {
        $originalInvoice = $invoice->load(['items']);

        $newInvoiceData = $invoice->toArray();
        unset($newInvoiceData['invoice_id']);
        unset($newInvoiceData['invoice_number']);
        unset($newInvoiceData['created_at']);
        unset($newInvoiceData['updated_at']);

        // Reset status and payment info
        $newInvoiceData['status'] = 'draft';
        $newInvoiceData['payment_status'] = 'pending';
        $newInvoiceData['paid_amount'] = 0;
        $newInvoiceData['payment_date'] = null;
        $newInvoiceData['invoice_date'] = Carbon::today();
        
        // Update due date if payment terms exist
        if ($invoice->payment_terms) {
            $newInvoiceData['due_date'] = Carbon::today()->addDays($invoice->payment_terms);
        }

        $newInvoice = Invoice::create($newInvoiceData);

        // Duplicate items
        foreach ($originalInvoice->items as $item) {
            $newItemData = $item->toArray();
            unset($newItemData['item_id']);
            unset($newItemData['created_at']);
            unset($newItemData['updated_at']);
            $newItemData['invoice_id'] = $newInvoice->invoice_id;

            InvoiceItem::create($newItemData);
        }

        return $newInvoice->fresh(['customer', 'items.product']);
    }

    /**
     * Get invoice analytics data.
     */
    public function getInvoiceAnalytics(): array
    {
        $totalInvoices = Invoice::count();
        $draftInvoices = Invoice::draft()->count();
        $sentInvoices = Invoice::sent()->count();
        $paidInvoices = Invoice::paid()->count();
        $overdueInvoices = Invoice::overdue()->count();
        $cancelledInvoices = Invoice::cancelled()->count();

        // Recent invoices (last 30 days)
        $recentInvoices = Invoice::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // Revenue analytics
        $totalRevenue = Invoice::where('payment_status', 'paid')->sum('paid_amount');
        $pendingRevenue = Invoice::whereNotIn('payment_status', ['paid', 'cancelled'])->sum('total_amount');
        $monthlyRevenue = Invoice::where('payment_status', 'paid')
                                ->thisMonth()
                                ->sum('paid_amount');
        $todayRevenue = Invoice::where('payment_status', 'paid')
                             ->today()
                             ->sum('paid_amount');

        // Outstanding amounts
        $totalOutstanding = Invoice::whereNotIn('payment_status', ['paid'])
                                 ->whereNotIn('status', ['cancelled'])
                                 ->sum('total_amount') - Invoice::whereNotIn('payment_status', ['paid'])
                                                              ->whereNotIn('status', ['cancelled'])
                                                              ->sum('paid_amount');

        // Invoice trend (last 12 months)
        $invoiceTrend = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Invoice::whereYear('created_at', $date->year)
                          ->whereMonth('created_at', $date->month)
                          ->count();
            $revenue = Invoice::whereYear('created_at', $date->year)
                            ->whereMonth('created_at', $date->month)
                            ->where('payment_status', 'paid')
                            ->sum('paid_amount');
            
            $invoiceTrend->push([
                'month' => $date->format('M Y'),
                'invoices' => $count,
                'revenue' => $revenue
            ]);
        }

        // Top customers by invoice value
        $topCustomers = Invoice::with(['customer'])
                             ->selectRaw('customer_id, COUNT(*) as invoice_count, SUM(total_amount) as total_invoiced, SUM(paid_amount) as total_paid')
                             ->groupBy('customer_id')
                             ->orderByDesc('total_invoiced')
                             ->limit(10)
                             ->get();

        // Average invoice value
        $avgInvoiceValue = Invoice::avg('total_amount') ?? 0;

        // Payment method breakdown
        $paymentMethods = Invoice::where('payment_status', 'paid')
                               ->selectRaw('payment_method, COUNT(*) as count, SUM(paid_amount) as total')
                               ->groupBy('payment_method')
                               ->get();

        return [
            'summary' => [
                'total_invoices' => $totalInvoices,
                'draft_invoices' => $draftInvoices,
                'sent_invoices' => $sentInvoices,
                'paid_invoices' => $paidInvoices,
                'overdue_invoices' => $overdueInvoices,
                'cancelled_invoices' => $cancelledInvoices,
                'recent_invoices' => $recentInvoices,
                'total_revenue' => $totalRevenue,
                'pending_revenue' => $pendingRevenue,
                'monthly_revenue' => $monthlyRevenue,
                'today_revenue' => $todayRevenue,
                'total_outstanding' => $totalOutstanding,
                'avg_invoice_value' => round($avgInvoiceValue, 2),
            ],
            'invoice_trend' => $invoiceTrend,
            'top_customers' => $topCustomers,
            'payment_methods' => $paymentMethods,
        ];
    }

    /**
     * Get invoice history for a customer.
     */
    public function getCustomerInvoiceHistory(Customer $customer, int $perPage = 15): LengthAwarePaginator
    {
        return $customer->invoices()
                       ->with(['items.product'])
                       ->latest('created_at')
                       ->paginate($perPage);
    }

    /**
     * Get overdue invoices.
     */
    public function getOverdueInvoices(): Collection
    {
        return Invoice::getOverdue()->sortBy('due_date');
    }

    /**
     * Update overdue statuses for invoices.
     */
    public function updateOverdueStatuses(): int
    {
        $overdueInvoices = Invoice::whereNotNull('due_date')
                                ->where('due_date', '<', Carbon::today())
                                ->where('status', 'sent')
                                ->whereNotIn('payment_status', ['paid'])
                                ->get();

        $updated = 0;
        foreach ($overdueInvoices as $invoice) {
            if ($invoice->updateOverdueStatus()) {
                $updated++;
            }
        }

        return $updated;
    }

    /**
     * Generate PDF for invoice.
     */
    public function generatePdf(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $invoice->load(['customer', 'items.product']);
        
        // Here you would use a PDF library like DomPDF or similar
        // This is a placeholder implementation
        throw new \Exception('PDF generation not yet implemented');
    }

    /**
     * Send invoice via email.
     */
    public function sendEmail(Invoice $invoice, string $email, ?string $subject = null, ?string $message = null): bool
    {
        // Here you would implement email sending logic
        // This is a placeholder implementation
        throw new \Exception('Email sending not yet implemented');
    }

    /**
     * Get invoices requiring attention.
     */
    public function getInvoicesRequiringAttention(): Collection
    {
        return Invoice::requiresAttention()
                     ->with(['customer'])
                     ->get();
    }

    /**
     * Calculate invoice summary.
     */
    public function calculateInvoiceSummary(array $items, float $taxRate = 0, float $discountAmount = 0): array
    {
        $subtotal = 0;
        
        foreach ($items as $item) {
            $lineTotal = ($item['quantity'] * $item['unit_price']) - ($item['discount_amount'] ?? 0);
            $subtotal += $lineTotal;
        }

        $taxAmount = $subtotal * ($taxRate / 100);
        $totalAmount = $subtotal + $taxAmount - $discountAmount;

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'discount_amount' => round($discountAmount, 2),
            'total_amount' => round($totalAmount, 2),
        ];
    }

    /**
     * Get invoice statistics for dashboard.
     */
    public function getDashboardStatistics(): array
    {
        return [
            'total_invoices' => Invoice::count(),
            'draft_invoices' => Invoice::draft()->count(),
            'overdue_invoices' => Invoice::overdue()->count(),
            'paid_this_month' => Invoice::thisMonth()->paid()->sum('paid_amount'),
            'outstanding_amount' => Invoice::whereNotIn('payment_status', ['paid'])
                                         ->whereNotIn('status', ['cancelled'])
                                         ->sum('total_amount') - Invoice::whereNotIn('payment_status', ['paid'])
                                                                      ->whereNotIn('status', ['cancelled'])
                                                                      ->sum('paid_amount'),
        ];
    }
}
