<?php
/**
 * Short.ly bootstrap
 *
 * @author rezwana
 */
require_once './App.php';
require_once './Api.php';
require_once './Generator.php';
require_once './Utils.php';
require_once './MyDB.php';

$api = new Shortly\App();
$api->start();