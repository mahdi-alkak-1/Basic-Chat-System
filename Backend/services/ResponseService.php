<?php 

class ResponseService{

    public static function response(int $status_code,string $message, array $data = null){
        $response = [];
        $response["status"] = $status_code;
        $response["message"] = $message;
        if($data !== null){
             $response["data"] = $data;
        }

        return json_encode($response);
    }
}
?>