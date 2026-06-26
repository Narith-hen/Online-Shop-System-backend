<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator with access to the admin dashboard.']
        );

        Role::updateOrCreate(
            ['name' => 'customer'],
            ['description' => 'Customer with access to the shop homepage.']
        );
    }
}