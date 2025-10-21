<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $samples = [
            [
                'title' => 'Low Stock Alert',
                'message' => 'Product XYZ-001 is running low on stock',
                'level' => 'warning',
                'type' => 'inventory.low_stock',
                'link' => url('/inventory/thresholds/alerts'),
            ],
            [
                'title' => 'Purchase Order Pending',
                'message' => 'PO-2024-001 requires approval',
                'level' => 'info',
                'type' => 'purchases.order_pending',
                'link' => url('/purchases/orders'),
            ],
            [
                'title' => 'Goods Receipt Completed',
                'message' => 'GR-2024-015 has been received',
                'level' => 'success',
                'type' => 'purchases.grn_completed',
                'link' => url('/purchases/receives'),
            ],
            [
                'title' => 'Critical Stock Level',
                'message' => '3 items have reached critical stock levels',
                'level' => 'warning',
                'type' => 'inventory.critical',
                'link' => url('/inventory/thresholds/alerts'),
            ],
        ];

        foreach ($samples as $s) {
            Notification::create(array_merge($s, [
                'user_id' => $user?->id,
            ]));
        }
    }
}
