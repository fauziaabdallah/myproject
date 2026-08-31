<?php

namespace Database\Seeders;

use App\Models\SystemUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $query = SystemUser::where("email","fauziaabdallah2025@gmail.com")->first();
        if(!$query){
            SystemUser::create([
                "first_name" => "FAUZIA",
                "last_name" => "SULEIMAN",
                "mobile" => "077394847",
                "email" => "fauziaabdallah2025@gmail.com",
                "password" => Hash::make("12345"),
                "role" => "admin",
                "organization_id" => 16,

            ]);
        }
    }
}
