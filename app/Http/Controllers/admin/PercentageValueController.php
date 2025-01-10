<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\PercentageValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PercentageValueController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $percentageValues = PercentageValue::where('user_id',$user->id)->first();
        return view('admin.percentage_values.edit', compact('user', 'percentageValues'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $percentageValues = PercentageValue::updateOrCreate(
            ['user_id' => $user->id], 
            [
                'user_id' => $user->id,
                'annual_salary_increment'   => $request->annual_salary_increment,
                'cpiw'                      => $request->cpiw,
                'csrs_cola'                 => $request->csrs_cola,
                'fers_cola'                 => $request->fers_cola,
                'tsp_increment'             => $request->tsp_increment,
                'fehb_increment'            => $request->fehb_increment,
            ]
        );
        return redirect()->route('admin.percentageValues.edit', compact('user', 'percentageValues'));
    }
}
