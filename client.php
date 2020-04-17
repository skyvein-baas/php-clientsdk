<?php

namespace baas\Core;

use baas\Core\HttpHelper;

class BaseClient
{
	public $node;
	public $logged = false; 
	public $token;
    public $addr;
    public $prik;
    public $pubk;
    public $mnem;

	function  __construct($nodeuri)
    {
        $this->node = $nodeuri;
    }

    public function Login($acct, $pwd, $ismd5=false)
    {
    	if (!$ismd5) {
    		$pwd=md5($pwd);
    	}
    	$requestUrl = $this->node."/v1/login";
    	$jsonStr = json_encode(array('account' => $acct, 'pwd' => $pwd));
    	// throw
    	$ret = HttpHelper::curl($requestUrl, "POST", $jsonStr);
    	$retArr=json_decode($ret->getBody(), true);
    	if ($retArr["status"]==1) {
    		$this->addr = $retArr["data"][0]["address"];
            $this->prik = $retArr["data"][0]["prikey"];
            $this->pubk = $retArr["data"][0]["pubkey"];
            $this->mnem = $retArr["data"][0]["mnemonic"];
    	}
    	return $retArr;
    }

    public function GetToken($addr, $prik, $pubk, $mnem)
    {
        $requestUrl = $this->node."/v1/gettoken";
        $jsonStr = json_encode(array('address' => $addr, 'prikey' => $prik, 'pubkey' => $pubk, 'mnemonic' => $mnem));
        // throw
        $ret = HttpHelper::curl($requestUrl, "POST", $jsonStr);
        $retArr=json_decode($ret->getBody(), true);
        if ($retArr["status"]==1) {
            if ($retArr["data"][0]["token"]) {
                $this->logged = true;
                $this->token = $retArr["data"][0]["token"];
            }
        }
        return $retArr;
    }

    public function Invoke($cont, $method, $args=null)
    {
    	if (!$this->logged) {
    		return false;
    	}
    	$requestUrl = $this->node."/v1/continvoke";
    	$header = array('Token' => $this->token);
    	$jsonStr = json_encode(array('contract' => $cont, 'method' => $method, 'args' => $args));
    	$ret = HttpHelper::curl($requestUrl, "POST", $jsonStr, $header);
    	$retArr=json_decode($ret->getBody(), true);
    	return $retArr;
    }

    public function Query($cont, $method, $args=null)
    {
    	if (!$this->logged) {
    		return false;
    	}
    	$requestUrl = $this->node."/v1/contquery";
    	$header = array('Token' => $this->token);
    	$jsonStr = json_encode(array('contract' => $cont, 'method' => $method, 'args' => $args));
    	$ret = HttpHelper::curl($requestUrl, "POST", $jsonStr, $header);
    	$retArr=json_decode($ret->getBody(), true);
    	return $retArr;
    }
}