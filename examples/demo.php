<?php

require_once('http.php');
require_once('client.php');
use baas\Core\BaseClient;

$uri = "http://ip:port";
$cli = new BaseClient($uri);
$acct = "";
$pwd = "";

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

// 用密钥登录
try {
	$ret = $cli->GetToken($cli->addr, $cli->prik, $cli->pubk, $cli->mnem);
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
	// 读取错误原因
	var_dump($retI["msg"]);
	return;
}
// 响应结果
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
	// 读取错误原因
	var_dump($retQ["msg"]);
	return;
}
// 响应结果
var_dump($retQ["data"][0]);
echo "query success\n";
?>