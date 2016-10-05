<?php
namespace Shortly;
use Exception;


/**
 * Class to handle GET, POST and PUT api requests
 *
 * @author rezwana
 */
class Api {
    
    
    function __construct() {
    }
    
    /**
     * Implements API GET method 
     * Retrieve a list of all existing shortened URLs, 
     * including time since creation and target URLs (each with number of redirects)
     * @return Array
     */
    public function get() {
        return MyDB::getInstance()->getAll();
    }
    
    /**
     * Implements API POST method
     * Submit any URL, with or without device type, if no device type is submitted, 
     * it defaults to Desktop type. 
     * Save it to the database and returns a shortened URL back
     * @return String
     * @throws Exception
     */
    public function post() {
        if(empty($_POST["url"])){
            throw new Exception("Invalid parameter post.");
        }
        $ts = time();
        $key = Generator::getUniqueID($ts);
        $url = $_POST["url"];
        $device = !empty($_POST["device"]) ? $_POST["device"] : Utils::getDeviceType();
        try {
            MyDB::getInstance()->post($ts, $key, $url, $device);
        } catch (Exception $exc) {
            throw new Exception("Operation failed.");
        }

        return $_SERVER["HTTP_HOST"] . "/" . $key;
    }
    
    /**
     * Implements API PUT method
     * Configure a shortened URL to redirect to different targets based on the 
     * device type
     * @return String
     * @throws Exception
     */
    public function put() {
        $_PUT = json_decode(file_get_contents("php://input"), true);
        if(empty($_PUT["key"]) || empty($_PUT["device"]) || empty($_PUT["url"])){
            throw new Exception("Invalid parameter put.");
        }
        $key = $_PUT["key"];
        $url = $_PUT["url"];
        $device = !empty($_POST["device"]) ? $_POST["device"] : Utils::getDeviceType();
        try {
            $boolStatus = MyDB::getInstance()->put($key, $url, $device);
        } catch (Exception $exc) {
            throw new Exception("Operation failed.");
        }

        return $boolStatus ? "Updated" : "Failed";
    }
}
