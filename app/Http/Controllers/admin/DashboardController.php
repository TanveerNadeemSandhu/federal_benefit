<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function view()
    {
        $user = Auth::user();
        if (empty($user->role)) 
        {
            return redirect()->route('admin.processAccountType');
        } 
        else
        {
            $user = Auth::user();
            $profile = Profile::where('user_id',$user->id)->first();
            return view('admin.profile.edit', compact('user', 'profile'));
        }
    }

    public function editProfile()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id',$user->id)->first();
        return view('admin.profile.edit', compact('user', 'profile'));
    }

    public function updateProfile(Request $request)
    {
        
        $user = Auth::user();
        $user = User::where('id',$user->id)->first();
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
        return redirect()->route('profile.edit', compact('user', 'profile'));
    }

    
}
