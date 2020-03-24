<?php

require_once('http.php');
require_once('client.php');
use baas\Core\BaseClient;

$uri = "http://122.224.183.34:31374";
// $uri = "http://localhost:8080";
$cli = new BaseClient($uri);
$acct = "15188889999";
$pwd = "xiaobai233";
try {
	$ret = $cli->Login($acct, $pwd);
} catch (Exception $e) {
	var_dump($e);
	return;
}
if (!$ret) {
	var_dump($ret);
	return;
}
echo "loggin success\n";

$cont = "mycounter";
$method = "increase";
$args = array("key"=>"sq");
try {
	$retI = $cli->Invoke($cont, $method, $args);
} catch (Exception $e) {
	var_dump($e);
	return;
}
if ($retI["status"] != 1) {
	var_dump($retI["msg"]);
	return;
}

var_dump($retI["data"][0]);
echo "invoke success\n";

$method = "get";
$args = array("key"=>"sq");
try {
	$retQ = $cli->Query($cont, $method, $args);
} catch (Exception $e) {
	var_dump($e);
	return;
}
if ($retQ["status"] != 1) {
	var_dump($retQ["msg"]);
	return;
}
var_dump($retQ["data"][0]);
echo "query success\n";
?>