<?php

namespace App\Http\Middleware;

use Closure;
use App\Helper\HelperGeoLocation; // Make sure to import the HelperGeoLocation class
use Illuminate\Support\Facades\Redirect;
use RealRashid\SweetAlert\Facades\Alert;

class CheckAllowedLocation
{
    public function handle($request, Closure $next)
    {
//        $checkAvailableLocation = HelperGeoLocation::geoProcess();
//        session()->put("not_allowed_location", $checkAvailableLocation);
//        session()->save();
//
//        if ($checkAvailableLocation) {
//            return $next($request);
//        } else {
//            Alert::error("message", __('messages.message_outside_saudia'));
//            return Redirect::route('not_allowed');
//        }
        return $next($request);
    }
}
