<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $model = new User();
        $model->name = 'admin';
        $model->display_name = '管理者';
        $model->email = 'admin@example.com';
        $model->password = Hash::make('passwordpassword');
        $model->save();
    }
}
