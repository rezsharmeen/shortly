<?php
namespace Shortly;
use Exception;
/**
 * Short.ly entry point
 *
 * @author rezwana
 */
class App {
    
    public function __construct() {
        
    }
    /**
     * Based on the http request redirect to original url or route to specific API methods.
     * @return NULL
     */
    public function start() {
        if (isset($_SERVER) && isset($_SERVER["REQUEST_METHOD"])) {
            $method = strtolower($_SERVER["REQUEST_METHOD"]);
            if($method === "get" && !empty($_GET["q"])){
                $this->redirectRequest($_GET["q"]);
                return;
            }
        }
        try {
            $api = new Api();
            if (@method_exists($api,$method)){
                $response = $api->{$method}();
                $this->sendDataResponse($response);
            } else {
                $this->sendErrorResponse("Unknown operation.");
            }
        } catch (Exception $exc) {
            $this->sendErrorResponse($exc->getMessage());
        }
    }
    
    /**
     * Gets request with valid short url and redirects to the original url
     * @param string $key
     */
    private function redirectRequest($key) {
        $url = MyDB::getInstance()->getUrl($key, Utils::getDeviceType());
        if(!empty($url) && strrpos($url, "http://")> -1){
            header("Location:".$url);
        }
        else if(!empty($url)){
            header("Location:http://".$url);
        }
        else {
            $this->sendErrorResponse("Unknown Key.");
        }
    }
    
    /**
     * Sends a JSON encoded string as data response
     * @param Object $param
     */
    private function sendDataResponse($param) {
        echo json_encode(array("status"=> "success", "data" => $param));
    }
    
    /**
     * Sends a JSON encoded string as error response
     * @param Object $param
     */
    private function sendErrorResponse($param) {
        echo json_encode(array("status"=> "fail", "data" => $param));
    }
}
