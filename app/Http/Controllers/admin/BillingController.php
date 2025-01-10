<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentHistory;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function editBilling()
    {
        $user = Auth::user();
        $subscription = Subscription::where('user_id',$user->id)->first();
        if ($subscription) 
        {
            $paymentHistory = PaymentHistory::where('subscription_id',$subscription->id)->get();
        }
        return view('admin.billing.edit', compact('subscription', 'paymentHistory'));
    }

    public function updateBilling(Request $request)
    {
         // Find the user's subscription
         $subscription = Subscription::where('user_id', Auth::user()->id)->first();
         if ($subscription) {
             // // Validate the request data
             // $validatedData = $request->validate([
             //     'card_name' => 'required|string',
             //     'card_number' => 'required',
             //     'month' => 'required',
             //     'year' => 'required',
             //     'csv' => 'required',
             // ]);
 
             // Update the user's subscription with the validated request data
           // Update the user's profile with the request data
            $subscription->update([
                'card_name'   => $request->card_name,
                'card_number' => $request->card_number,
                'month'       => $request->month,
                'year'        => $request->year,
                'csv'         => $request->csv,
            ]);
 
 
 
             // Optionally, you can redirect the user to a success page or back to the billing page
             return redirect()->route('billing.edit')->with('success', 'Card details updated successfully');
         }
 
         // Handle the case where the user's subscription is not found
         return redirect()->route('billing.edit')->with('error', 'Subscription not found');
    }
}
