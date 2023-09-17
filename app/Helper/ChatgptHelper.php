<?php

namespace App\Helper;

use GuzzleHttp\Client;

class ChatgptHelper
{
    public function getWhatINeed($message){
        return $message;
//        try {
//            $search = $message;
//
//            $ch = curl_init();
//
//            $headers = [
//                'Content-Type: application/json',
//                'Authorization: Bearer ' . env('OPENAI_API_KEY'),
//            ];
//
//            $data = [
//                "model" => "gpt-3.5-turbo",
//                'messages' => [
//                    [
//                        "role" => "user",
//                        "content" => 'translate that word to arabic ('.$search.')'
//                    ]
//                ],
//                'temperature' => 0.5,
//                "max_tokens" => 200,
//                "top_p" => 1.0,
//                "frequency_penalty" => 0.52,
//                "presence_penalty" => 0.5,
//                "stop" => ["11."],
//            ];
//
//            curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
//            curl_setopt($ch, CURLOPT_POST, true);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
//            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//// Disable SSL verification
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//
//            $result = curl_exec($ch);
//            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//
//            curl_close($ch);
//
//            $data = json_decode($result, true);
//
//            return $data['choices'][0]['message']['content'];
//        }catch (\Exception $ex){
//            return $message;
//        }

    }
}
