<?php
namespace Shortly;

/**
 * Utility functionalities
 *
 * @author rezwana
 */
class Utils {
    
    private function __construct() {    
    }
    
    /**
     * Return device type of user based on http_user_agent
     * @return string
     */
    public static function getDeviceType() {
        if(empty($_SERVER["HTTP_USER_AGENT"])){
            return "Desktop";
        }
        if (preg_match("/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i", strtolower($_SERVER["HTTP_USER_AGENT"]))) {
            return "Tablet";
        }
        else if (preg_match("/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i", strtolower($_SERVER["HTTP_USER_AGENT"]))) {
            return "Mobile";
        }
        else {
            return "Desktop";
        }
    }
}
