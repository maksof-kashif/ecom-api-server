<?php 
    
    class CommonController {

        function sendEmail($to, $subject, $message){
            
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
              "Content-Type: application/json"
            ));
            curl_setopt($curl, CURLOPT_URL,
              "https://api.smtp2go.com/v3/email/send"
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
              "sender" => "no-reply@openprofit.net",
              "to" => array(
                0 => $to
              ),
              "subject" => $subject,
              "html_body" => $message,
              "text_body" => $message,
              "api_key" => "api-BE2DF7601AEE11E8801FF23C91C88F4E"
            )));
            $result = curl_exec($curl);
        }

        function sendResponseWithCode($code, $status, $message, $response){
          return $response->withStatus($code)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode((object) array(
                "status"   => $status,
                "message"    => $message
            )));
        }

        function sendBodyResponseWithCode($code, $status, $message, $body, $response) {
          return $response->withStatus($code)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode((object) array(
                "status"   => $status,
                "message"    => $message,
                "data"    => $body
            )));
        }

        public function generateToken() {
            
            $characters = 'ZYXWVUTSRQPONMLKJIHGFEDCBAabcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ9876543210zyxwvutsrqponmlkjihgfedcba';
            $string = '';
            $max = strlen($characters) - 1;
            for ($i = 0; $i < 268; $i++) {
                $string .= $characters[mt_rand(0, $max)];
            }
            return $string;
        }

        public function generateBluffCode() {
            
            $characters = 'ZYXWVUTSRQPONMLKJIHGFEDCBAabcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ9876543210zyxwvutsrqponmlkjihgfedcba';
            $string = '';
            $max = strlen($characters) - 1;
            for ($i = 0; $i < 32; $i++) {
                $string .= $characters[mt_rand(0, $max)];
            }
            return $string;
        }

        function getBaseUrl() {
          //return 'http://www.openprofit.maksof.com/';
          return 'localhost/';
        }

        function getApiBaseUrl() {
          //return self::getBaseUrl().'api/';
          return self::getBaseUrl().'slim-api-server/';
        }

    }
?>