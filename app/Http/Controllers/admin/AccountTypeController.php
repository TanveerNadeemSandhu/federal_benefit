<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentHistory;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Stripe;
use Exception;

class AccountTypeController extends Controller
{
    public function processAccountType()
    {
        return view('admin.profile.type');
    }

    public function accountType(Request $request)
    {
        try {
            if ($request->account_type == "agency" || $request->account_type == "personal") {
                Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $customer = Stripe\Customer::create(array(
                    "address" => [
                        "line1"       => "Virani Chowk",
                        "postal_code" => "360001",
                        "city"        => "Rajkot",
                        "state"       => "GJ",
                        "country"     => "IN",
                    ],
                    "email"  => "demo@gmail.com",
                    "name"   => "Hardik Savani",
                    "source" => $request->stripeToken
                ));

                $stripePayment = Stripe\Charge::create([
                    "amount"      => $request->price,
                    "currency"    => "usd",
                    "customer"    => $customer->id,
                    "description" => "Test payment from Fed.",
                    "shipping" => [
                        "name"    => $request->card_name,
                        "address" => [
                            "line1"       => "510 Townsend St",
                            "postal_code" => "98140",
                            "city"        => "San Francisco",
                            "state"       => "CA",
                            "country"     => "US",
                        ],
                    ]
                ]);



                $user = Auth::user();
                $user = User::where('id', $user->id)->first();
                $user->update([
                    'role' => $request->account_type
                ]);

                $subscription = Subscription::create([
                    'user_id'      => $user->id,
                    'price'        => $request->price,
                    'account_type' => $request->account_type,
                    'case'         => $request->case,
                    'revision'     => $request->revision,
                    'card_name'    => $request->card_name,
                    'card_number'  => $request->card_number,
                    'month'        => $request->month,
                    'year'         => $request->year,
                    'csv'          => $request->csv,
                    'date'         => today(), // Save the current date (without time)
                ]);

                PaymentHistory::create([
                    'user_id'         => $user->id,
                    "subscription_id" => $subscription->id,
                    'card_name'       => $request->card_name,
                    'card_number'     => $request->card_number,
                    'month'           => $request->month,
                    'year'            => $request->year,
                    'csv'             => $request->csv,
                    'price'           => $request->price,
                    'payment_date'    => Carbon::now(),
                ]);
            } else {
                $user = Auth::user();
                $user = User::where('id', $user->id)->first();
                $user->update([
                    'role' => $request->account_type
                ]);
            }
            return redirect()->route('profile.edit');
        } catch (Exception $e) {
            // Handle the error and redirect back with an error message
            return redirect()->back()->with('error', "Payment method failed. Please re-enter the card details.");
        }
    }
}
