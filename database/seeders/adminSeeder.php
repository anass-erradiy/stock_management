<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class adminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'userName' => 'admin admin' ,
            'email' => 'admin@gmail.com' ,
            'phoneNumber' => null ,
            'password' => Hash::make('123456789') ,
        ])->assignRole(['admin','seller','buyer']) ;
    }
}
