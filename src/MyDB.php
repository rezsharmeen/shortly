<?php
namespace Shortly;
use Exception;
use PDO;
/**
 * Description of Api
 *
 * @author rezwana
 */
class MyDB {
    
    private static $instance;
    private $dbConn;
    
    /**
     * Construct database connection string
     */
    private function __construct($dbPath) {
        $connectionKey = ($dbPath) ? $dbPath : './shortly.db';
        $this->dbConn  = new PDO("sqlite:".$connectionKey) or die('cannot open the database');
        $this->dbConn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    }
    
    /**
     * 
     * Get an instance of the Database
     * @return Object
     */
    public static function getInstance($dbPath = null) {
        if(empty(self::$instance)){ // If no instance then make one
            self::$instance = new MyDB($dbPath);
        }
        return self::$instance;
    }
    
    /**
     * Query the database for all shortened urls
     * @return Array
     */
    public function getAll() {
        $stmt = $this->dbConn->prepare("
            Select k.key, d.deviceType, s.targetUrl, s.redirectCount 
            from tblKeys k, tblDevices d, tblShorten s 
            Where s.keyId = k.id
            AND s.deviceId = d.id");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Query the database for original url, based on key and device type 
     * @param String $key
     * @param String $device
     * @return String
     */
    public function getUrl($key, $device){
        $stmt = $this->dbConn->prepare("
            Select s.id, s.targetUrl 
            from tblKeys k, tblDevices d, tblShorten s 
            Where k.key = :key 
            AND d.deviceType = :device
            AND s.keyId = k.id
            AND s.deviceId = d.id");

            $stmt->bindParam(":key", $key);
            $stmt->bindParam(":device", $device);
            if ($stmt->execute()) {
                $row = $stmt->fetch();
            }
            
            if(!empty($row->targetUrl)){
                $stmt = $this->dbConn->prepare("Update tblShorten Set redirectCount =  (redirectCount + 1 ) Where id = :id");
                $stmt->bindParam(":id", $row->id);
                $stmt->execute(); 
                return $row->targetUrl;
            }
            return null;
    }
    
    /**
     * Insert data in the database
     * @param Timestamp $time
     * @param String $key
     * @param String $url
     * @param String $device
     * @throws Exception
     */
    public function post($time, $key, $url, $device) {
        try{
            $stmt = $this->dbConn->prepare("INSERT INTO tblKeys (createdAt, key) VALUES (:time, :key)");
            $stmt->bindParam(":time", $time);
            $stmt->bindParam(":key", $key);
            $stmt->execute();
            $lastId = $this->dbConn->lastInsertId();

            $stmt = $this->dbConn->prepare("INSERT INTO tblShorten (keyId, targetUrl, deviceId) VALUES (:lastId, :url, :deviceId)");
            $stmt->bindParam(":lastId", $lastId);
            $stmt->bindParam(":url", $url);
            $stmt->bindParam(":deviceId", $this->getDeviceId($device));
            $stmt->execute();
            
            return true;
        } catch (Exception $exc) {
            throw new Exception("Data insert failed.");            
        }
        return false;
    }
    
    /**
     * Update device type and original url 
     * @param String $key
     * @param String $url
     * @param String $device
     * @return Boolean
     */
    public function put($key, $url, $device) {
        $stmt = $this->dbConn->prepare("
            Select k.id 
            from tblKeys k
            Where k.key = :key");
            
        $stmt->bindParam(":key", $key);
        if ($stmt->execute()) {
            $row = $stmt->fetch();
        }
        var_dump($row, $key, $this->dbConn->errorInfo());
        if(empty($row)){
            return false;
        }
            
        $stmt = $this->dbConn->prepare("UPDATE tblShorten SET deviceId = :device, targetUrl = :url WHERE keyId = :id");
        $stmt->bindParam(":id", $row->id);
        $stmt->bindParam(":device", $this->getDeviceId($device));
        $stmt->bindParam(":url", $url);
        if($stmt->execute()){
            return true;
        }
        var_dump($this->dbConn->errorInfo());
        return false;
    }
    
    
    /**
     * Get device id for a device type
     * @param String $deviceType
     * @return Integer
     */
    public function getDeviceId($deviceType) {
        $stmt = $this->dbConn->prepare("
            Select id 
            from tblDevices
            Where deviceType = :deviceType");
            
        $stmt->bindParam(":deviceType", $deviceType);
        if ($stmt->execute()) {
            $row = $stmt->fetch();
        }
        
        return (empty($row)) ? 1 : $row->id;
    }
}
