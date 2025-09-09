<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $invoices = $this->invoiceService->getPaginatedInvoices($request);
        $filterOptions = $this->invoiceService->getFilterOptions();

        return view('sales.invoices.index', [
            'invoices' => $invoices,
            'customers' => $filterOptions['customers'],
            'products' => $filterOptions['products'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $filterOptions = $this->invoiceService->getFilterOptions();
        $salesOrder = null;
        
        // Check if creating from a sales order
        if ($request->has('from_order')) {
            $salesOrder = SalesOrder::with(['customer', 'items.product'])->find($request->get('from_order'));
        }
        
        return view('sales.invoices.create', [
            'customers' => $filterOptions['customers'],
            'products' => $filterOptions['products'],
            'salesOrder' => $salesOrder,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,customer_id',
            'sales_order_id' => 'nullable|exists:sales_orders,order_id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'status' => 'nullable|in:draft,sent,paid,overdue,cancelled',
            'payment_status' => 'nullable|in:pending,partial,paid,refunded',
            'payment_method' => 'nullable|in:cash,card,bank_transfer,check',
            'payment_terms' => 'nullable|integer|min:0|max:365',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:1000',

            'billing_address' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'terms_conditions' => 'nullable|string|max:5000',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        $invoice = $this->invoiceService->createInvoice($data);

        return redirect()->route('sales.invoices.show', $invoice->invoice_id)
            ->with('success', 'Invoice created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'salesOrder']);
        return view('sales.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'salesOrder']);
        $filterOptions = $this->invoiceService->getFilterOptions();
        return view('sales.invoices.edit', [
            'invoice' => $invoice,
            'customers' => $filterOptions['customers'],
            'products' => $filterOptions['products'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,customer_id',
            'sales_order_id' => 'nullable|exists:sales_orders,order_id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'status' => 'nullable|in:draft,sent,paid,overdue,cancelled',
            'payment_status' => 'nullable|in:pending,partial,paid,refunded',
            'payment_method' => 'nullable|in:cash,card,bank_transfer,check',
            'payment_terms' => 'nullable|integer|min:0|max:365',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:1000',

            'billing_address' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'terms_conditions' => 'nullable|string|max:5000',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        $this->invoiceService->updateInvoice($invoice, $data);

        return redirect()->route('sales.invoices.show', $invoice->invoice_id)
            ->with('success', 'Invoice updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        try {
            $this->invoiceService->deleteInvoice($invoice);
            return redirect()->route('sales.invoices.index')
                ->with('success', 'Invoice deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('sales.invoices.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Change invoice status
     */
    public function changeStatus(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            $this->invoiceService->changeInvoiceStatus($invoice, $request->get('status'));
            return redirect()->back()->with('success', 'Invoice status updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mark invoice as sent
     */
    public function markAsSent(Invoice $invoice)
    {
        try {
            $this->invoiceService->markAsSent($invoice);
            return redirect()->back()->with('success', 'Invoice marked as sent successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Record payment for invoice
     */
    public function recordPayment(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->remaining_balance,
            'payment_method' => 'required|in:cash,card,bank_transfer,check',
            'payment_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            $this->invoiceService->recordPayment(
                $invoice,
                $request->get('amount'),
                $request->get('payment_method'),
                $request->get('payment_notes')
            );
            return redirect()->back()->with('success', 'Payment recorded successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Generate PDF for invoice
     */
    public function generatePdf(Invoice $invoice)
    {
        try {
            $pdf = $this->invoiceService->generatePdf($invoice);
            return $pdf->download($invoice->invoice_number . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Send invoice via email
     */
    public function sendEmail(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            $this->invoiceService->sendEmail(
                $invoice,
                $request->get('email'),
                $request->get('subject'),
                $request->get('message')
            );
            return redirect()->back()->with('success', 'Invoice sent via email successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Create invoice from sales order
     */
    public function createFromOrder(Request $request, SalesOrder $salesOrder)
    {
        try {
            $invoice = $this->invoiceService->createFromSalesOrder($salesOrder);
            return redirect()->route('sales.invoices.show', $invoice->invoice_id)
                ->with('success', 'Invoice created from sales order successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Duplicate an existing invoice
     */
    public function duplicate(Invoice $invoice)
    {
        try {
            $newInvoice = $this->invoiceService->duplicateInvoice($invoice);
            return redirect()->route('sales.invoices.edit', $newInvoice->invoice_id)
                ->with('success', 'Invoice duplicated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Analytics
     */
    public function analytics()
    {
        $analytics = $this->invoiceService->getInvoiceAnalytics();
        return response()->json($analytics);
    }

    /**
     * Get overdue invoices
     */
    public function overdue()
    {
        $overdueInvoices = $this->invoiceService->getOverdueInvoices();
        return view('sales.invoices.overdue', compact('overdueInvoices'));
    }

    /**
     * Update overdue statuses (can be run as a scheduled task)
     */
    public function updateOverdueStatuses()
    {
        try {
            $updated = $this->invoiceService->updateOverdueStatuses();
            return redirect()->back()->with('success', "Updated {$updated} overdue invoices.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
