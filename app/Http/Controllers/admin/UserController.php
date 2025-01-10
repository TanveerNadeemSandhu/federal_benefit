<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\FedCase;
use App\Models\PaymentHistory;
use App\Models\Profile;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $loggedInUser = Auth::user();

        if ($loggedInUser->role == 'super admin') {
            // If the logged-in user is a super admin, fetch all users
            $users = User::withCount("FedCase")->get();
        } else {
            // If the logged-in user is not a super admin, fetch users with the same role
            $users = User::with('roles')
                ->withCount("cases")
                ->where('email', '!=', 'superadmin@gmail.com')
                ->whereHas('roles', function ($query) use ($loggedInUser) {
                    $query->whereIn('name', $loggedInUser->roles->pluck('name'));
                })
                ->where("id", '!=', $loggedInUser->id)
                ->get();
        }

        return view("admin.user.index", compact('users'));
    }

    public function statusChange($id)
    {

        $user = User::find($id);
        $newStatus = $user->status == '1' ? '0' : '1';
        $user->status = $newStatus;

        // Save the updated status to the database
        $user->save();
        return redirect()->route('admin.user-index')->with('success', 'Status change successfully');
        // Redirect to the index page or wherever you want
    }

    public function destroy($id)
    {
        // Find the user by ID
        $user = User::find($id);

        // Delete BillingHistory records
        $subscription = Subscription::where('user_id', $id)->first();
        if ($subscription) {
            $subscription->delete();
        }
        $billingHistory = PaymentHistory::where('user_id', $id)->get();
        if ($billingHistory->isNotEmpty()) {
            foreach ($billingHistory as $history) {
                $history->delete();
            }
        }

        // Delete Cases records
        $cases = FedCase::where('user_id', $id)->get();
        if ($cases->isNotEmpty()) {
            foreach ($cases as $case) {
                $case->delete();
            }
        }

        // Delete the user
        $user->delete();

        // Return a response or redirect as needed
        return redirect()->route('admin.user-index')->with('success', 'User and related records deleted successfully');
    }

    public function adminEditProfile($id)
    {
        $user = User::where('id', $id)->first();
        $profile = Profile::where('user_id',$user->id)->first();
        return view('admin.profile.edit', compact('user', 'profile'));
    }

    public function adminUpdateProfile(Request $request)
    {
        $user = User::where('id', $request->userId)->first();
        $user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
        ]);
        $profile = Profile::where('user_id',$user->id)->first();
        if($request->hasfile('image'))
        {
            if($request->hasfile('bg_image'))
            {
                $imageFile = $request->file('image');
                $ext = $imageFile->getClientOriginalExtension();
                $imageFileName = rand(0,9989955).'.'.$ext;
                $imageFile->move('upload/profile/',$imageFileName);
                $bgImageFile = $request->file('bg_image');
                $ext = $bgImageFile->getClientOriginalExtension();
                $bgImageFileName = rand(0,9989955).'.'.$ext;
                $bgImageFile->move('upload/background/',$bgImageFileName);
                $profile->update([
                    'title'        => $request->title,
                    'company_name' => $request->company_name,
                    'phone_1'      => $request->phone_1,
                    'phone_1_type' => $request->phone_1_type,
                    'phone_2'      => $request->phone_2,
                    'phone_2_type' => $request->phone_2_type,
                    'address'      => $request->address,
                    'city'         => $request->city,
                    'statement'    => $request->statement,
                    'image'        => $imageFileName,
                    'bg_image'     => $bgImageFileName
                ]);
            }
            else
            {
                $imageFile = $request->file('image');
                $ext = $imageFile->getClientOriginalExtension();
                $imageFileName = rand(0,9989955).'.'.$ext;
                $imageFile->move('upload/profile/',$imageFileName);
                $profile->update([
                    'title'        => $request->title,
                    'company_name' => $request->company_name,
                    'phone_1'      => $request->phone_1,
                    'phone_1_type' => $request->phone_1_type,
                    'phone_2'      => $request->phone_2,
                    'phone_2_type' => $request->phone_2_type,
                    'address'      => $request->address,
                    'city'         => $request->city,
                    'statement'    => $request->statement,
                    'image'        => $imageFileName,
                ]);
            }
        }
        else if($request->hasfile('bg_image'))
        {
            if($request->hasfile('image'))
            {
                $imageFile = $request->file('image');
                $ext = $imageFile->getClientOriginalExtension();
                $imageFileName = rand(0,9989955).'.'.$ext;
                $imageFile->move('upload/profile/',$imageFileName);
                $bgImageFile = $request->file('bg_image');
                $ext = $bgImageFile->getClientOriginalExtension();
                $bgImageFileName = rand(0,9989955).'.'.$ext;
                $bgImageFile->move('upload/background/',$bgImageFileName);
                $profile->update([
                    'title'        => $request->title,
                    'company_name' => $request->company_name,
                    'phone_1'      => $request->phone_1,
                    'phone_1_type' => $request->phone_1_type,
                    'phone_2'      => $request->phone_2,
                    'phone_2_type' => $request->phone_2_type,
                    'address'      => $request->address,
                    'city'         => $request->city,
                    'statement'    => $request->statement,
                    'image'        => $imageFileName,
                    'bg_image'     => $bgImageFileName
                ]);
            }
            else
            {
                $bgImageFile = $request->file('bg_image');
                $ext = $bgImageFile->getClientOriginalExtension();
                $bgImageFileName = rand(0,9989955).'.'.$ext;
                $bgImageFile->move('upload/background/',$bgImageFileName);
                $profile->update([
                    'title'        => $request->title,
                    'company_name' => $request->company_name,
                    'phone_1'      => $request->phone_1,
                    'phone_1_type' => $request->phone_1_type,
                    'phone_2'      => $request->phone_2,
                    'phone_2_type' => $request->phone_2_type,
                    'address'      => $request->address,
                    'city'         => $request->city,
                    'statement'    => $request->statement,
                    'bg_image'     => $bgImageFileName
                ]); 
            }
        }
        else
        {
            $profile->update([
                'title'        => $request->title,
                'company_name' => $request->company_name,
                'phone_1'      => $request->phone_1,
                'phone_1_type' => $request->phone_1_type,
                'phone_2'      => $request->phone_2,
                'phone_2_type' => $request->phone_2_type,
                'address'      => $request->address,
                'city'         => $request->city,
                'statement'    => $request->statement,
            ]);
        }
        return view('admin.profile.edit', compact('user', 'profile'));
    }
}
