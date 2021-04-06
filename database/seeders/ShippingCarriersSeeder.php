<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingCarriersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipping_carrier')->insert(
        [
            [
                'shipping_carrier' => 'FedEx',
                'shipping_method' => 'FedEx International Priority',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'shipping_carrier' => 'SMSA Express',
                'shipping_method' => 'SMSA Express (4-10 working days)',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'shipping_carrier' => 'TNT Express',
                'shipping_method' => 'TNT Express2 - 10 working days',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'shipping_carrier' => 'Shalooh Delivery',
                'shipping_method' => 'Local Delivery (1 - 2 days)',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'shipping_carrier' => 'Key Arabia',
                'shipping_method' => 'VIP Delivery ( within 3 Hrs)',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]
    );
    }
}
