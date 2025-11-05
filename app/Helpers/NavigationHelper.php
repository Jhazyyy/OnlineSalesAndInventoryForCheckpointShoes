<?php

namespace App\Helpers;

class NavigationHelper
{
    /**
     * Get SVG icon for navigation items
     *
     * @param  string  $iconName
     * @param  string  $size
     * @return string
     */
    public static function getIcon($iconName, $size = 'w-5 h-5 mr-3')
    {
        $icons = [
            'dashboard' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>',

            'inventory' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                </path>
                            </svg>',

            'products' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                </path>
                            </svg>',

            'sales' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z">
                            </path>
                        </svg>',

            'customers' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                </path>
                            </svg>',

            'purchases' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8.5m-8.5 0a2 2 0 11-4 0 2 2 0 014 0zm8.5 0a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>',

            'reports' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>',

            'returns' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6">
                            </path>
                        </svg>',

            'integration' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0">
                                </path>
                            </svg>',

            'stock-adjustment' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>',

            'composite-products' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>',

            'orders' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>',

            'exchange' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4">
                              </path>
                        </svg>',
            'master_data' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M12 4c4.418 0 8 1.343 8 3s-3.582 3-8 3-8-1.343-8-3 3.582-3 8-3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M4 7v10c0 1.657 3.582 3 8 3s8-1.343 8-3V7" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M4 12c0 1.657 3.582 3 8 3s8-1.343 8-3" />
                        </svg>',

            'brands' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4l7.586-.003a2 2 0 011.414.586l7.414 7.414a2 2 0 010 2.828l-7.586 7.586a2 2 0 01-2.828 0L4 13.414A2 2 0 014 10.586L10.586 4a2 2 0 012.828 0l7.586 7.586" />
                        </svg>',

            'categories' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M4 4h6v6H4V4zM14 4h6v6h-6V4zM4 14h6v6H4v-6zM14 14h6v6h-6v-6z" />
                            </svg>',

            'suppliers' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17H7a2 2 0 01-2-2V5a2 2 0 012-2h8a2 2 0 012 2v2h2a2 2 0 012 2v6a2 2 0 01-2 2h-1" />
                                <circle cx="7" cy="17" r="2" stroke="currentColor" stroke-width="2" />
                                <circle cx="17" cy="17" r="2" stroke="currentColor" stroke-width="2" />
                            </svg>',

            'warehouse' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>',

            'sales_order_master' => '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m4 0V7a2 2 0 00-2-2h-4.586a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 008.586 2H6a2 2 0 00-2 2v13a2 2 0 002 2h1m2 0h6" />
                                    </svg>',

           'purchase_order_master' => '<svg class="' . $size . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 2h6a1 1 0 011 1v1h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2V3a1 1 0 011-1zm0 0h6v2H9V2z" />
                                    </svg>',

            'inventory_report' => '<svg class="' . $size . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 2h6a1 1 0 011 1v1h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2V3a1 1 0 011-1zm3 7h4m-4 4h4m-8-4h.01M8 13h.01" />
                                    </svg>',


            'reorder_items' => '<svg class="' . $size . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h6l2 3h10a1 1 0 011 1v3M16 21H6a1 1 0 01-1-1V9m13 6l3 3m0 0l-3 3m3-3h-6" />
                                </svg>',

            'critical_level_items' => '<svg class="' . $size . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 3h-4a2 2 0 00-2 2v2h8V5a2 2 0 00-2-2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v4m0 4h.01" />
                                        </svg>',

            'supplier_cost' => '<svg class="' . $size . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 21v-2a4 4 0 014-4h6a4 4 0 014 4v2" />
                                    <text x="16" y="18" font-size="6" fill="currentColor" font-weight="bold" font-family="Arial, sans-serif">$</text>
                                </svg>',

            'block_items' => '<svg class="' . $size . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <!-- Box -->
                                <rect x="3" y="7" width="18" height="10" rx="2" ry="2" stroke-width="2" stroke="currentColor" />
                                <!-- X mark -->
                                <line x1="7" y1="11" x2="17" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <line x1="17" y1="11" x2="7" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>',

                                'delivery' => '<svg class="' . $size . '" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2.25 7.5V17.25A1.5 1.5 0 003.75 18.75H5.25M2.25 7.5L6.75 3H17.25A1.5 1.5 0 0118.75 4.5V6.75M2.25 7.5H18.75M18.75 6.75H20.25A1.5 1.5 0 0121.75 8.25V14.25A1.5 1.5 0 0120.25 15.75H19.5M5.25 18.75A1.5 1.5 0 006.75 20.25H8.25A1.5 1.5 0 009.75 18.75H5.25ZM14.25 18.75A1.5 1.5 0 0015.75 20.25H17.25A1.5 1.5 0 0018.75 18.75H14.25Z" />
                                </svg>',

            'user_accounts_control' => '<svg class="' . $size . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 14c1.656 0 3 1.344 3 3v3H5v-3c0-1.656 1.344-3 3-3h8z" />
                                            <circle cx="12" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                        </svg>',
                                        
            'stock_adjustment' => '<svg class="' . $size . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- Box / Inventory base -->
                                    <rect x="3" y="7" width="18" height="13" rx="2" ry="2" stroke-width="2"/>
                                    
                                    <!-- Up arrow (Add stock) -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11l2-2 2 2M10 9v6" />
                                    
                                    <!-- Down arrow (Reduce stock) -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 13l2 2 2-2M16 9v6" />
                                </svg>',

            'tax_and_discount' => '<svg class="' . $size . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- Document shape -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 3h8a2 2 0 0 1 2 2v14l-4-2-4 2-4-2V5a2 2 0 0 1 2-2z" />
                                    
                                    <!-- Percentage symbol -->
                                    <line x1="9" y1="9" x2="15" y2="15" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    <circle cx="9" cy="15" r="1.5" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    <circle cx="15" cy="9" r="1.5" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>',

        ];

        return $icons[$iconName] ?? '<svg class="'.$size.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>';
    }

    /**
     * Check if a route pattern is active
     *
     * @param  string|array  $routePatterns
     * @return bool
     */
    public static function isActiveRoute($routePatterns)
    {
        if (is_string($routePatterns)) {
            return request()->routeIs($routePatterns);
        }

        if (is_array($routePatterns)) {
            foreach ($routePatterns as $pattern) {
                if (request()->routeIs($pattern)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get active class if route is active
     *
     * @param  string|array  $routePatterns
     * @param  string  $activeClass
     * @return string
     */
    public static function getActiveClass($routePatterns, $activeClass = 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300 border-r-2 border-blue-500')
    {
        return self::isActiveRoute($routePatterns) ? $activeClass : '';
    }
}
