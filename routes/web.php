<?php

use App\Http\Controllers\admin\AccountTypeController;
use App\Http\Controllers\admin\ASIController;
use App\Http\Controllers\admin\BillingController;
use App\Http\Controllers\admin\ColaController;
use App\Http\Controllers\admin\CPIWController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\FedCaseController;
use App\Http\Controllers\admin\PercentageValueController;
use App\Http\Controllers\admin\ShareController;
use App\Http\Controllers\admin\TempController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;



// Route::get('/', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified']);




Route::middleware('auth','verified')->group(function () {
    Route::get('/',  [DashboardController::class, 'view'])->name('dashboard');
    Route::get('/dashboard',  [DashboardController::class, 'view'])->name('dashboard.view');
    
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/edit/profile', [DashboardController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');

    Route::get('admin/edit/profile/{id}', [UserController::class, 'adminEditProfile'])->name('admin.profileEdit');
    Route::put('admin/update/profile', [UserController::class, 'adminUpdateProfile'])->name('admin.profileUpdate');

    Route::get('/edit/billing', [BillingController::class, 'editBilling'])->name('billing.edit');
    Route::put('/billing', [BillingController::class, 'updateBilling'])->name('billing.update');

    Route::get('/process/account-type', [AccountTypeController::class, 'processAccountType'])->name('admin.processAccountType');
    Route::post('/account-type', [AccountTypeController::class, 'accountType'])->name('admin.accountType');

    Route::resource('fed-case', FedCaseController::class);
    Route::get('fed-case/print/{fedCase}', [FedCaseController::class, 'print'])->name('fed-case.print');
    Route::get('fed-case/downloadPDF/{fedCase}', [FedCaseController::class, 'downloadPDF'])->name('fed-case.downloadPDF');
    Route::post('fed-case/sideBar/{fedCase}', [FedCaseController::class, 'sideBar'])->name('fed-case.sideBar');


    Route::get('/share/process', [ShareController::class, 'processShare'])->name('process.share');
    Route::post('/share', [ShareController::class, 'share'])->name('share');
    Route::get('/share/list', [ShareController::class, 'shareList'])->name('share.list');
    Route::delete('/share/delete/{id}', [ShareController::class, 'shareDelete'])->name('share.destroy');
    Route::get('share/status-change/{id}', [ShareController::class, 'statusChange'])->name('share.statusChange');
    Route::get('share/agencies/list', [ShareController::class, 'shareAgencyList'])->name('share.agenciesList');
    Route::get('share/case/list/{userId}/{shareId}', [ShareController::class, 'shareCaseList'])->name('share.caseList');
    Route::post('share/case/add', [ShareController::class, 'shareCaseAdd'])->name('share.caseAdd');
    Route::post('share/case/store', [ShareController::class, 'shareCaseStore'])->name('share.caseStore');
    Route::get('share/case/edit/{id}', [ShareController::class, 'shareCaseEdit'])->name('share.caseEdit');
    Route::put('share/case/update/{id}', [ShareController::class, 'shareCaseUpdate'])->name('share.caseUpdate');

    Route::get('users-list', [UserController::class, 'index'])->name('admin.user-index');
    Route::get('users-statusChange/{id}', [UserController::class, 'statusChange'])->name('admin.user-statusChange');
    Route::get('users-destroy/{id}', [UserController::class, 'Destroy'])->name('admin.user-destroy');

    Route::get('calculation/show/{id}', [TempController::class, 'calculationShow'])->name('calculation.show');

    Route::get('edit/percentage-values', [PercentageValueController::class, 'edit'])->name('admin.percentageValues.edit');
    Route::put('update/percentage-values', [PercentageValueController::class, 'update'])->name('admin.percentageValues.update');
    
});

Route::get('/cleareverything', function () {
        $clearcache = Artisan::call('cache:clear');
        echo "Cache cleared<br>";
    
        $clearview = Artisan::call('view:clear');
        echo "View cleared<br>";
    
        $clearconfig = Artisan::call('config:cache');
        echo "Config cleared<br>";
    
    });


require __DIR__.'/auth.php';
