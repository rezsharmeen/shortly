<?php
namespace Shortly;

/**
 * Hash algorithm for Short.Ly key 
 *
 * @author rezwana
 */

/**
 * Generate 62 number based unique key
 */
class Generator {
    
    function __construct() {
        
    }
    /**
     * Using the timestamp, generate 62 base unique id
     * Check it against the database for uniqueness
     * Return the unique key as string
     * @param TIMESTAMP $ts
     * @return String
     */
    public static function getUniqueID($ts){
        $base = 62;
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $convNumber = array();
        $decimalNumber = $ts * 100000 + rand(100000,999999);
        $quotient = $decimalNumber;
        while($quotient > $base) {
            $temp = $quotient % $base;
            $convNumber[] = $chars[$temp];
            $quotient = (int)($quotient / $base);
        }
        $convNumber[] = $chars[$quotient];
        $convNumber = strrev(implode($convNumber));
        
        return self::isUniqueID($ts, $convNumber) ? $convNumber : self::getUniqueID();
    }
    
    /**
     * Check in the database if the key already exists
     * @param TIMESTAMP $ts
     * @param String $convNumber
     * @return Boolean
     */
    private static function isUniqueID($ts, $convNumber){
        $result = MyDB::getInstance()->getUrl($ts, $convNumber);
        return empty($result) ? true : false;
    }
}
