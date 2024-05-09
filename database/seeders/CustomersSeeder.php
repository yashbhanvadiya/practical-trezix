<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customers')->truncate();

        $customers = [];
        for ($i = 0; $i < 10; $i++) {
            $customers[] = [
                'name' => 'Customer ' . ($i + 1),
                'email' => 'customer' . ($i + 1) . '@gmail.com',
                'phone_number' => '1234567890' . $i,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('customers')->insert($customers);
    }
}
