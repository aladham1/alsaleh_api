<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['admin','manager','visitor','super_manager','general_manager','financial_manager','media_manager'];
        foreach ($roles as $role){
            Role::create([
                'name' => $role
            ]);
        }
    }
}
