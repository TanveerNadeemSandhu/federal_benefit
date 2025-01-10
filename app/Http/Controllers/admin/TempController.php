<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DentalAndVision;
use App\Models\FedCase;
use App\Models\FEGLI;
use App\Models\FEHBVP;
use App\Models\FLTCIP;
use App\Models\HighThree;
use App\Models\InsuranceCost;
use App\Models\Pension;
use App\Models\SRS;
use App\Models\SurvivorBenefit;
use App\Models\YosDollar;
use App\Models\YosE;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TempController extends Controller
{
    public function calculationShow($id)
    {
        $fedCase = FedCase::where('id', $id)->first();
        $yosDollar = YosDollar::where('fed_case_id',$id)->first();
        $yosE = YosE::where('fed_case_id',$id)->first();
        $highThree = HighThree::where('fed_case_id',$id)->first();
        $pension = Pension::where('fed_case_id',$id)->first();
        $fegliInsuranceCost = FEGLI::where('fed_case_id',$id)->first();
        $srsValue = SRS::where('fed_case_id',$id)->first();
        $dentalAndVisionValue = DentalAndVision::where('fed_case_id',$id)->first();
        $fehbVPValue = FEHBVP::where('fed_case_id',$id)->first();
        $fltcipValue = FLTCIP::where('fed_case_id',$id)->first();
        $survivorBenefit = SurvivorBenefit::where('fed_case_id',$id)->first();

        preg_match('/(\d+)\s*Y/', $fedCase->age, $matches);
        $age = (int)$matches[1];


        $todayDateYear = Carbon::now()->year;

        $data = [];
        $data['yosDollar']               = $yosDollar;
        $data['yosE']                    = $yosE;
        $data['highThree']               = $highThree;
        $data['pension']                 = $pension;
        $data['fegliInsuranceCost']      = $fegliInsuranceCost;
        $data['srsValue']                = $srsValue;
        $data['dentalAndVisionValue']    = $dentalAndVisionValue;
        $data['fehbVPValue']             = $fehbVPValue;
        $data['fltcipValue']             = $fltcipValue;
        $data['age']                     = $age;
        $data['todayDateYear']           = $todayDateYear;
        $data['survivorBenefit']           = $survivorBenefit;
        return view('admin.calculation.show',$data);
    }
}
