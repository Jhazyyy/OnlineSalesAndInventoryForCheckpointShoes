<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Imports\CustomersImport;
use App\Services\CustomerService;

class CustomerController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $customers = $this->customerService->getPaginatedCustomers($request);
        $filterOptions = $this->customerService->getFilterOptions();

        return view('sales.customers.index', [
            'customers' => $customers,
            'cities' => $filterOptions['cities'],
            'countries' => $filterOptions['countries']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create customers');
        
        return view('sales.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create customers');
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string|max:2000',
            // 'status' => 'required|in:active,inactive',
            // 'customer_type' => 'required|in:individual,business',
            'company_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '_' . $avatar->getClientOriginalName();
            $avatarPath = $avatar->storeAs('customers/avatars', $avatarName, 'public');
            $data['avatar'] = $avatarPath;
        }

        $customer = Customer::create($data);

        // Log to audit trail
        \App\Models\AuditLog::logAction(
            \App\Models\AuditLog::ACTION_CREATE,
            \App\Models\AuditLog::MODULE_CUSTOMERS,
            "Customer {$customer->full_name} created",
            'Customer',
            $customer->customer_id,
            $customer->full_name,
            null,
            [
                'name' => $customer->full_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
            ],
            \App\Models\AuditLog::SEVERITY_INFO
        );

        return redirect()->route('sales.customers.index')
            ->with('success', 'Customer created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        // Load relationships for detailed view
        $customer->load(['sales', 'sales.product']);
        
        // Get customer statistics
        $recentSales = $customer->sales()->latest('date')->take(10)->get();
        $monthlySpending = $customer->sales()
            ->with('product')
            ->whereYear('date', date('Y'))
            ->get()
            ->groupBy(function($sale) {
                return $sale->date->format('F');
            })
            ->map(function($sales) {
                return $sales->sum('total_amount');
            });

        return view('sales.customers.show', compact('customer', 'recentSales', 'monthlySpending'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $this->authorize('edit customers');
        
        return view('sales.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $this->authorize('edit customers');
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->customer_id . ',customer_id|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string|max:2000',
            // 'status' => 'required|in:active,inactive',
            // 'customer_type' => 'required|in:individual,business',
            'company_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($customer->avatar && Storage::disk('public')->exists($customer->avatar)) {
                Storage::disk('public')->delete($customer->avatar);
            }

            $avatar = $request->file('avatar');
            $avatarName = time() . '_' . $avatar->getClientOriginalName();
            $avatarPath = $avatar->storeAs('customers/avatars', $avatarName, 'public');
            $data['avatar'] = $avatarPath;
        }

        // Capture old values
        $oldValues = [
            'name' => $customer->full_name,
            'email' => $customer->email,
            'phone' => $customer->phone,
        ];

        $customer->update($data);

        // Capture new values
        $customer->refresh();
        $newValues = [
            'name' => $customer->full_name,
            'email' => $customer->email,
            'phone' => $customer->phone,
        ];

        // Log to audit trail
        \App\Models\AuditLog::logAction(
            \App\Models\AuditLog::ACTION_UPDATE,
            \App\Models\AuditLog::MODULE_CUSTOMERS,
            "Customer {$customer->full_name} updated",
            'Customer',
            $customer->customer_id,
            $customer->full_name,
            $oldValues,
            $newValues,
            \App\Models\AuditLog::SEVERITY_INFO
        );

        return redirect()->route('sales.customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $this->authorize('delete customers');
        
        // Check if customer has sales
        if ($customer->sales()->count() > 0) {
            return redirect()->route('sales.customers.index')
                ->with('error', 'Cannot delete customer with existing sales records.');
        }

        // Capture customer details before deletion
        $customerName = $customer->full_name;
        $customerId = $customer->customer_id;
        $customerData = [
            'name' => $customer->full_name,
            'email' => $customer->email,
            'phone' => $customer->phone,
        ];

        // Delete associated avatar
        if ($customer->avatar && Storage::disk('public')->exists($customer->avatar)) {
            Storage::disk('public')->delete($customer->avatar);
        }

        $customer->delete();

        // Log to audit trail
        \App\Models\AuditLog::logAction(
            \App\Models\AuditLog::ACTION_DELETE,
            \App\Models\AuditLog::MODULE_CUSTOMERS,
            "Customer {$customerName} deleted",
            'Customer',
            $customerId,
            $customerName,
            $customerData,
            null,
            \App\Models\AuditLog::SEVERITY_WARNING
        );

        return redirect()->route('sales.customers.index')
            ->with('success', 'Customer deleted successfully!');
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('sales.customers.import');
    }

    /**
     * Import customers from Excel/CSV file
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // Max 5MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $import = new CustomersImport();
            $import->import($request->file('excel_file'));

            $importedCount = $import->getRowCount();
            
            return redirect()->route('sales.customers.index')
                ->with('success', "Successfully imported {$importedCount} customers!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    /**
     * Download sample Excel template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="customers_template.csv"',
        ];

        // Create sample data for template
        $sampleData = [
            ['First Name', 'Last Name', 'Email', 'Phone', 'Address', 'City', 'State', 'Postal Code', 'Country', 'Date of Birth', 'Customer Type', 'Company Name', 'Tax ID', 'Notes'],
            ['John', 'Doe', 'john.doe@example.com', '+1234567890', '123 Main St', 'Manila', 'NCR', '1000', 'Philippines', '1990-01-01', 'individual', '', '', 'Sample individual customer'],
            ['Jane', 'Smith', 'jane@company.com', '+1234567891', '456 Business Ave', 'Cebu', 'Cebu', '6000', 'Philippines', '1985-05-15', 'business', 'ABC Corporation', 'TAX123456', 'Sample business customer'],
        ];

        $callback = function() use ($sampleData) {
            $file = fopen('php://output', 'w');
            
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get customer analytics data
     */
    public function analytics()
    {
        $analytics = $this->customerService->getCustomerAnalytics();
        return response()->json($analytics);
    }

    /**
     * Toggle customer status
     */
    public function toggleStatus(Customer $customer)
    {
        $customer->status = $customer->status === 'active' ? 'inactive' : 'active';
        $customer->save();

        $status = $customer->status === 'active' ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Customer {$status} successfully!");
    }

    /**
     * Export customers to CSV
     */
    public function export(Request $request)
    {
        $query = Customer::query();

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        if ($request->has('customer_type') && $request->customer_type) {
            if ($request->customer_type === 'individual') {
                $query->individual();
            } elseif ($request->customer_type === 'business') {
                $query->business();
            }
        }

        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->inactive();
            }
        }

        $customers = $query->get();

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="customers_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Address', 
                'City', 'State', 'Postal Code', 'Country', 'Date of Birth', 
                'Customer Type', 'Company Name', 'Tax ID', 'Status', 
                'Total Orders', 'Total Spent', 'Created At'
            ]);
            
            // Data
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->customer_id,
                    $customer->first_name,
                    $customer->last_name,
                    $customer->email,
                    $customer->phone,
                    $customer->address,
                    $customer->city,
                    $customer->state,
                    $customer->postal_code,
                    $customer->country,
                    $customer->date_of_birth?->format('Y-m-d'),
                    $customer->customer_type,
                    $customer->company_name,
                    $customer->tax_id,
                    $customer->status,
                    $customer->total_orders,
                    number_format($customer->total_spent, 2),
                    $customer->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
