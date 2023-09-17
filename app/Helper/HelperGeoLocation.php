<?php

namespace App\Helper;

use RealRashid\SweetAlert\Facades\Alert;

class HelperGeoLocation
{

    // prevent out site ksa to login


    public static function geoProcess()
    {
        try {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                // Use the first IP address in the list (if multiple IPs are present)
                $userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $userIP = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $userIP = $_SERVER['REMOTE_ADDR'];
            }

            $apiUrl = "http://ip-api.com/php/{$userIP}";

            $response = file_get_contents($apiUrl);

            if ($response) {
                $geolocationData = unserialize($response);
                if ($geolocationData && isset($geolocationData['countryCode']) and $geolocationData['countryCode']!=="SA") {
                    Alert::error("message",__('messages.message_outside_saudia'));
                    return false;
                }
            }
            return true;
        }catch (\Exception $exception){
            Alert::error("message",__('messages.message_outside_saudia'));
        }
    }
    function generateRandomIV() {
        return openssl_random_pseudo_bytes(16);
    }

    function encryptData($data, $key) {
        $iv = generateRandomIV();
        $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($iv . $encryptedData);
    }

    function decryptData($encryptedData, $key) {
        $encryptedData = base64_decode($encryptedData);
        $iv = substr($encryptedData, 0, 16);
        $encryptedData = substr($encryptedData, 16);
        $data = openssl_decrypt($encryptedData, 'aes-256-cbc', $key, 0, $iv);
        return $data;
    }
}
