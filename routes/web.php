<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackEndControllers\LoginController;
use App\Http\Controllers\FrontControllers\HomeController;
use App\Http\Controllers\FrontControllers\ProfileEmployeeController;
use App\Http\Controllers\FrontControllers\ServiceDetailController;
use App\Http\Controllers\BackEndControllers\MangerController;
use Illuminate\Support\Facades\Redirect;
use App\Helper\HelperGeoLocation;
use RealRashid\SweetAlert\Facades\Alert;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// that api make user outside KSA allowed to use That websites using A middleware  to prevent that

Route::get("run_request_sms_service",function (){
    \App\Jobs\SendSmsJob::dispatch();
})->name('not_allowed');

Route::get("not-allowed",function (){
    return view('frontend.NotAllowedLocation');
})->name('not_allowed');

Route::group(['middleware' => 'check.allowed.location'], function () {
    // that start of Application is routes will redirect to websites login
    Route::get('/', function () {
        return Redirect::route('login-page');
    });

    Route::get('/dashboard/report',[LoginController::class,'generateReportDisplayData']);
    Route::get('/edit-template/{transition_id}',[LoginController::class,'EditOnTemplate']);


    // to view the page of report
    Route::get('generate-pdf-version', function (){
        return view('frontend.report-pdf');
    });

    // that used to generate a report to print the requests of employees if want
    Route::get('generate-pdf/{transaction_id}', [LoginController::class,'generatePdf'])->name('generate-pdf');
   // that to configuring the translation
    Route::get('/set-locale/{locale}', function ($locale) {
        if (in_array($locale, config('app.available_locales'))) {
            session(['locale' => $locale]);
        }
        return redirect()->back();
    })->name('setLocale');
    // that to view the login page
    Route::post("send_otp_for_check_before",[LoginController::class,'send_otp_for_check_before'])->name('send_otp_for_check_before');
    Route::post("send_otp_email_for_check_otp",[LoginController::class,'send_otp_email_for_check_otp'])->name('send_otp_email_for_check_otp');
    Route::post("send_otp_for_check_otp",[LoginController::class,'send_otp_for_check_otp'])->name('send_otp_for_check_otp');
    Route::post("send_otp_for_register",[LoginController::class,'send_otp_for_register'])->name('send_otp_for_register');
    Route::get("send_email_for_register",[LoginController::class,'send_email_for_register'])->name('send_email_for_register');
    Route::post("send_email_for_check_before",[LoginController::class,'send_email_for_check_before'])->name('send_email_for_check_before');
    Route::get("login",[LoginController::class,'index'])->name('login-page');
    // update user data
    Route::get("update-user-view",[LoginController::class,'UpdateUser_view'])->name('update-user-view');
    Route::post("emp_quama_verified",[LoginController::class,'emp_quama_verified'])->name('emp_quama_verified');
    Route::post("update-user",[LoginController::class,'UpdateUser'])->name('update-user');
    Route::get("delete-service/{transaction_id}",[LoginController::class,'DeleteService'])->name('delete-service');
    // that to do the logic of login that have more conditions to login  websites
    Route::post("login-auth",[LoginController::class,'Login'])->name('auth-login');
    // that to send otp to user in your phone and email also
    Route::post("send-otp",[LoginController::class,'SendOtp'])->name('send-otp');

    Route::group(['middleware' => 'employee.session'], function () {
        // that to get logout from system and forget all sessions
        Route::get("logout",[LoginController::class,'logout'])->name('logout');
        // that to get employee details only to see your own information
        Route::get("get-employee-information",[LoginController::class,'GetEmployeeInformation'])->name('get-employee-information');
        // that to get the Requests and know tracking the requests and know the status
        Route::get('home',[HomeController::class,'index'])->name('home');
        Route::get('clearance',[HomeController::class,'clearance'])->name('clearance');
        // that to get the tracking Requests using Transaction ID
        Route::get('get-details/{transaction_id}',[HomeController::class,'getDetails'])->name('get-details');
       //  that routes  show  the services available in system and also the Pending Requests for Manger and delegated person
        Route::get('profile-employee',[ProfileEmployeeController::class,'index'])->name('profile-employee');

        Route::get('services-category',[ProfileEmployeeController::class,'servicesCategory'])->name('services-category');
        Route::get('certificate-service',[ProfileEmployeeController::class,'certificateService'])->name('certificate-service');
        Route::get('loan-service',[ProfileEmployeeController::class,'loanService'])->name('loan-service');
        Route::get('get-attribute-special-service/{flex_id}/{service_type}/{main_service_request_sub}',[ProfileEmployeeController::class,'getAttributeSpecialService'])->name('get-attribute-special-service');

        Route::get('insurance-service',[ProfileEmployeeController::class,'InsuranceService'])->name('insurance-service');
        Route::get('letter-service',[ProfileEmployeeController::class,'LetterService'])->name('letter-service');
        Route::get('other-service',[ProfileEmployeeController::class,'OtherService'])->name('other-service');
        Route::get('services-sub_category_attribute',[ProfileEmployeeController::class,'servicesSubCategoryAttribute'])->name('services-sub_category_attribute');


        // that to show the form of each service
        Route::get('service-details/{service_type}/{absence_attendance_type_id}/{name}',[ServiceDetailController::class,'index'])->name("service-details");

        //special service for future feature if want to add more fields
        Route::get('special-case-unauthorized/{service_type}/{absence_attendance_type_id}/{name}',[ServiceDetailController::class,'specialcaseunauthorized'])->name("special-case-unauthorized");
        Route::get('special-service-details/{id_flex_num}/{name}',[ServiceDetailController::class,'SpecialService'])->name("special-service-details");

        // that for add the details of each service and call the workflow of launch insert in table
        Route::post('add-service-detail',[ServiceDetailController::class,'AddServiceDetail'])->name("add-service-detail");
        Route::post('add-special-service-detail',[ServiceDetailController::class,'AddSpecialServiceDetail'])->name("add-special-service-detail");
        // that used to get the Accruals based on start date of each employee
        Route::post('get-accruals',[ServiceDetailController::class,'CalculateAccruals'])->name("get-accruals");
        // that used to approve the request specific manger or admin_mgr or top_mgr
        Route::post('approve-request',[MangerController::class,'ApproveRequest'])->name("approve-request");
        // that used to reject the request specific manger or admin_mgr or top_mgr
        Route::post('reject-request',[MangerController::class,'RejectRequest'])->name("reject-request");
       // that 7 routes is responsible for delegation
        Route::get('delegation',[MangerController::class,'Delegation'])->name("delegation-view");
        Route::get('delegation-history-employee',[MangerController::class,'Delegation'])->name("delegation-history-employee");
        Route::get('create-delegate',[MangerController::class,'createDelegate'])->name("create-delegate");
        Route::post('store-delegate',[MangerController::class,'storeDelegate'])->name("store-delegate");
        Route::get('update-view-delegate/{delegate_id}',[MangerController::class,'updateDelegateView'])->name("update-view-delegate");
        Route::post('update-delegate',[MangerController::class,'updateDelegate'])->name("update-delegate");
        Route::get('delete-delegate/{delegate_id}',[MangerController::class,'deleteDelegate'])->name("delete-delegate");

        // that show instruction of use website
        Route::get('taswaya',[HomeController::class,'taswaya'])->name("taswaya");
        Route::post('reject-request-taswaya',[HomeController::class,'Rejecttaswaya'])->name("reject-request-taswaya");
        Route::post('approve-request-taswaya',[HomeController::class,'Approvetaswaya'])->name("approve-request-taswaya");
        Route::get('help',[HomeController::class,'help'])->name("help");

    });

});



