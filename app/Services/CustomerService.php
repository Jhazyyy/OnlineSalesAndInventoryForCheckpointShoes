<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CustomerService
{
    /**
     * Get paginated customers with filters and search.
     */
    public function getPaginatedCustomers(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = Customer::query();

        // Apply search
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Apply filters
        if ($customerType = $request->get('customer_type')) {
            $query->where('customer_type', $customerType);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($city = $request->get('city')) {
            $query->where('city', $city);
        }

        if ($country = $request->get('country')) {
            $query->where('country', $country);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'updated_at');
        $sortOrder = $request->get('order', 'desc');
        
        if (in_array($sortField, ['first_name', 'last_name', 'email', 'customer_type', 'created_at', 'updated_at'])) {
            $query->orderBy($sortField, $sortOrder);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get filter options for customer listing.
     */
    public function getFilterOptions(): array
    {
        return [
            'cities' => Customer::whereNotNull('city')
                              ->distinct()
                              ->pluck('city')
                              ->filter()
                              ->sort()
                              ->values(),
            'countries' => Customer::whereNotNull('country')
                                 ->distinct()
                                 ->pluck('country')
                                 ->filter()
                                 ->sort()
                                 ->values(),
        ];
    }

    /**
     * Create a new customer.
     */
    public function createCustomer(array $data): Customer
    {
        // Handle avatar upload
        if (isset($data['avatar']) && $data['avatar']) {
            $data['avatar'] = $data['avatar']->store('customers/avatars', 'public');
        }

        // Set defaults
        $data['status'] = $data['status'] ?? 'active';
        $data['country'] = $data['country'] ?? 'Philippines';
        $data['customer_type'] = $data['customer_type'] ?? 'individual';

        return Customer::create($data);
    }

    /**
     * Update a customer.
     */
    public function updateCustomer(Customer $customer, array $data): Customer
    {
        // Handle avatar upload
        if (isset($data['avatar']) && $data['avatar']) {
            // Delete old avatar
            if ($customer->avatar) {
                \Storage::disk('public')->delete($customer->avatar);
            }
            $data['avatar'] = $data['avatar']->store('customers/avatars', 'public');
        }

        // Handle avatar removal
        if (isset($data['remove_avatar']) && $data['remove_avatar'] && $customer->avatar) {
            \Storage::disk('public')->delete($customer->avatar);
            $data['avatar'] = null;
        }

        $customer->update($data);
        return $customer->fresh();
    }

    /**
     * Delete a customer.
     */
    public function deleteCustomer(Customer $customer): bool
    {
        // Check if customer has sales
        if ($customer->sales()->exists()) {
            throw new \Exception('Cannot delete customer with existing sales records. Consider deactivating instead.');
        }

        // Delete avatar
        if ($customer->avatar) {
            \Storage::disk('public')->delete($customer->avatar);
        }

        return $customer->delete();
    }

    /**
     * Toggle customer status.
     */
    public function toggleCustomerStatus(Customer $customer): Customer
    {
        $customer->update([
            'status' => $customer->status === 'active' ? 'inactive' : 'active'
        ]);

        return $customer->fresh();
    }

    /**
     * Get customer analytics data.
     */
    public function getCustomerAnalytics(): array
    {
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::active()->count();
        $inactiveCustomers = Customer::inactive()->count();
        $individualCustomers = Customer::individual()->count();
        $businessCustomers = Customer::business()->count();

        // Recent customers (last 30 days)
        $recentCustomers = Customer::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // Top spending customers
        $topSpenders = Customer::with(['sales'])
                              ->get()
                              ->sortByDesc('total_spent')
                              ->take(10);

        // Customer acquisition trend (last 12 months)
        $acquisitionTrend = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Customer::whereYear('created_at', $date->year)
                           ->whereMonth('created_at', $date->month)
                           ->count();
            
            $acquisitionTrend->push([
                'month' => $date->format('M Y'),
                'count' => $count
            ]);
        }

        // Geographic distribution
        $geographicDistribution = Customer::selectRaw('country, COUNT(*) as count')
                                        ->whereNotNull('country')
                                        ->groupBy('country')
                                        ->orderByDesc('count')
                                        ->limit(10)
                                        ->get();

        // Age distribution (for customers with date_of_birth)
        $ageGroups = [
            '18-25' => 0,
            '26-35' => 0,
            '36-45' => 0,
            '46-55' => 0,
            '56-65' => 0,
            '65+' => 0
        ];

        Customer::whereNotNull('date_of_birth')->get()->each(function ($customer) use (&$ageGroups) {
            $age = $customer->age;
            if ($age >= 18 && $age <= 25) $ageGroups['18-25']++;
            elseif ($age >= 26 && $age <= 35) $ageGroups['26-35']++;
            elseif ($age >= 36 && $age <= 45) $ageGroups['36-45']++;
            elseif ($age >= 46 && $age <= 55) $ageGroups['46-55']++;
            elseif ($age >= 56 && $age <= 65) $ageGroups['56-65']++;
            elseif ($age > 65) $ageGroups['65+']++;
        });

        return [
            'summary' => [
                'total_customers' => $totalCustomers,
                'active_customers' => $activeCustomers,
                'inactive_customers' => $inactiveCustomers,
                'individual_customers' => $individualCustomers,
                'business_customers' => $businessCustomers,
                'recent_customers' => $recentCustomers,
                'conversion_rate' => $totalCustomers > 0 ? round(($activeCustomers / $totalCustomers) * 100, 2) : 0,
            ],
            'top_spenders' => $topSpenders,
            'acquisition_trend' => $acquisitionTrend,
            'geographic_distribution' => $geographicDistribution,
            'age_distribution' => $ageGroups,
        ];
    }

    /**
     * Get customer sales history with pagination.
     */
    public function getCustomerSalesHistory(Customer $customer, int $perPage = 15): LengthAwarePaginator
    {
        return $customer->sales()
                       ->with(['product'])
                       ->latest('date')
                       ->paginate($perPage);
    }

    /**
     * Calculate customer lifetime value.
     */
    public function calculateCustomerLifetimeValue(Customer $customer): array
    {
        $totalSpent = $customer->total_spent;
        $totalOrders = $customer->total_orders;
        $avgOrderValue = $customer->average_order_value;
        
        $firstOrderDate = $customer->sales()->oldest('date')->first()?->date;
        $lastOrderDate = $customer->last_order_date;
        
        $lifespanDays = 0;
        if ($firstOrderDate && $lastOrderDate) {
            $lifespanDays = $firstOrderDate->diffInDays($lastOrderDate) ?: 1;
        }

        $orderFrequency = $lifespanDays > 0 ? $totalOrders / ($lifespanDays / 30.44) : 0; // Orders per month
        
        // Simple CLV calculation: AOV * Purchase Frequency * Gross Margin * Lifespan
        // Assuming 20% gross margin and 24 month average lifespan
        $estimatedClv = $avgOrderValue * ($orderFrequency ?: 1) * 0.20 * 24;

        return [
            'total_spent' => $totalSpent,
            'total_orders' => $totalOrders,
            'average_order_value' => $avgOrderValue,
            'order_frequency_per_month' => round($orderFrequency, 2),
            'lifespan_days' => $lifespanDays,
            'estimated_clv' => round($estimatedClv, 2),
            'first_order_date' => $firstOrderDate,
            'last_order_date' => $lastOrderDate,
        ];
    }

    /**
     * Get customers with upcoming birthdays.
     */
    public function getUpcomingBirthdays(int $days = 30): Collection
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays($days);

        return Customer::whereNotNull('date_of_birth')
                      ->where(function ($query) use ($startDate, $endDate) {
                          // Handle birthdays that span across year boundary
                          if ($startDate->year !== $endDate->year) {
                              $query->where(function ($q) use ($startDate) {
                                  $q->whereRaw('MONTH(date_of_birth) = ? AND DAY(date_of_birth) >= ?', 
                                               [$startDate->month, $startDate->day]);
                              })->orWhere(function ($q) use ($endDate) {
                                  $q->whereRaw('MONTH(date_of_birth) = ? AND DAY(date_of_birth) <= ?', 
                                               [$endDate->month, $endDate->day]);
                              });
                          } else {
                              $query->whereRaw('MONTH(date_of_birth) = ?', [$startDate->month])
                                    ->whereRaw('DAY(date_of_birth) BETWEEN ? AND ?', 
                                               [$startDate->day, $endDate->day]);
                          }
                      })
                      ->active()
                      ->orderByRaw('MONTH(date_of_birth), DAY(date_of_birth)')
                      ->get();
    }

    /**
     * Search customers with advanced filters.
     */
    public function searchCustomers(array $criteria, int $perPage = 20): LengthAwarePaginator
    {
        $query = Customer::query();

        // Text search across multiple fields
        if (!empty($criteria['search'])) {
            $query->search($criteria['search']);
        }

        // Date range filters
        if (!empty($criteria['created_from'])) {
            $query->whereDate('created_at', '>=', $criteria['created_from']);
        }
        if (!empty($criteria['created_to'])) {
            $query->whereDate('created_at', '<=', $criteria['created_to']);
        }

        // Sales amount filters
        if (!empty($criteria['min_spent'])) {
            $query->whereHas('sales', function ($q) use ($criteria) {
                $q->havingRaw('SUM(total_amount) >= ?', [$criteria['min_spent']]);
            });
        }

        // VIP status
        if (!empty($criteria['vip_only'])) {
            $vipThreshold = $criteria['vip_threshold'] ?? 1000;
            $query->whereHas('sales', function ($q) use ($vipThreshold) {
                $q->havingRaw('SUM(total_amount) >= ?', [$vipThreshold]);
            });
        }

        // Location filters
        if (!empty($criteria['cities'])) {
            $query->whereIn('city', $criteria['cities']);
        }
        if (!empty($criteria['countries'])) {
            $query->whereIn('country', $criteria['countries']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Export customers data.
     */
    public function getCustomersForExport(array $filters = []): Collection
    {
        $query = Customer::with(['sales']);

        // Apply filters
        foreach ($filters as $field => $value) {
            if (!empty($value)) {
                switch ($field) {
                    case 'search':
                        $query->search($value);
                        break;
                    case 'customer_type':
                    case 'status':
                    case 'city':
                    case 'country':
                        $query->where($field, $value);
                        break;
                }
            }
        }

        return $query->get();
    }
}
