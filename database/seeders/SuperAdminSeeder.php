<?php

namespace Database\Seeders;

use App\Models\PaymentHistory;
use App\Models\Profile;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin =  User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('123456789'),
            'status' => 1,
            'role' => "super admin",
            'email_verified_at' => Carbon::now(),
        ]);
        $profile = Profile::create([
            'user_id' => $superAdmin->id,
        ]);
        
        $agency =  User::create([
            'first_name' => 'Agency',
            'last_name' => 'User',
            'email' => 'agency@gmail.com',
            'password' => Hash::make('123456789'),
            'status' => 1,
            'role' => "agency",
            'email_verified_at' => Carbon::now(),
        ]);
        $profile = Profile::create([
            'user_id' => $agency->id,
        ]);

        $agencySubscription = Subscription::create([
            'user_id'      => $agency->id,
            'price'        => '8400',
            'account_type' => 'agency',
            'case'         => 'unlimited',
            'revision'     => 'unlimited',
            'card_name'    => 'Test',
            'card_number'  => '4242 4242 4242 4242',
            'month'        => '12',
            'year'         => '2028',
            'csv'          => '123',
            'date'         => today(),
        ]);

        PaymentHistory::create([
            'user_id'         => $agency->id,
            "subscription_id" => $agencySubscription->id,
            'card_name'       => 'Test',
            'card_number'     => '4242 4242 4242 4242',
            'month'           => '12',
            'year'            => '2028',
            'csv'             => '123',
            'price'           => '8400',
            'payment_date'    => Carbon::now(),
        ]);

        $personal =  User::create([
            'first_name' => 'Personal',
            'last_name' => 'User',
            'email' => 'personal@gmail.com',
            'password' => Hash::make('123456789'),
            'status' => 1,
            'role' => "personal",
            'email_verified_at' => Carbon::now(),
        ]);
        $profile = Profile::create([
            'user_id' => $personal->id,
        ]);
        $personalSubscription = Subscription::create([
            'user_id'      => $personal->id,
            'price'        => '10000',
            'account_type' => 'personal',
            'case'         => 'one case',
            'revision'     => '30 days',
            'card_name'    => 'Test',
            'card_number'  => '4242 4242 4242 4242',
            'month'        => '12',
            'year'         => '2028',
            'csv'          => '123',
            'date'         => today(),
        ]);

        PaymentHistory::create([
            'user_id'         => $personal->id,
            "subscription_id" => $personalSubscription->id,
            'card_name'       => 'Test',
            'card_number'     => '4242 4242 4242 4242',
            'month'           => '12',
            'year'            => '2028',
            'csv'             => '123',
            'price'           => '10000',
            'payment_date'    => Carbon::now(),
        ]);

        $adminSupport =  User::create([
            'first_name' => 'Admin',
            'last_name' => 'Support',
            'email' => 'adminsupport@gmail.com',
            'password' => Hash::make('123456789'),
            'status' => 1,
            'role' => "admin",
            'email_verified_at' => Carbon::now(),
        ]);
        $profile = Profile::create([
            'user_id' => $adminSupport->id,
        ]);

        $backOffice =  User::create([
            'first_name' => 'Back',
            'last_name' => 'Office',
            'email' => 'backoffice@gmail.com',
            'password' => Hash::make('123456789'),
            'status' => 1,
            'role' => "office",
            'email_verified_at' => Carbon::now(),
        ]);
        $profile = Profile::create([
            'user_id' => $backOffice->id,
        ]);

    }
}
