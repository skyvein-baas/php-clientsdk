<?php

namespace baas\Core;

use Exception;

class HttpHelper
{
	public static $connectTimeout = 30;//30 second
	public static $readTimeout = 80;//80 second

	public static function curl($url, $httpMethod = "POST", $postFields = null,$headers = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpMethod); 
		// if(ENABLE_HTTP_PROXY) {
		// 	curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); 
		// 	curl_setopt($ch, CURLOPT_PROXY, HTTP_PROXY_IP); 
		// 	curl_setopt($ch, CURLOPT_PROXYPORT, HTTP_PROXY_PORT);
		// 	curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); 
		// }
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

		if (self::$readTimeout) {
			curl_setopt($ch, CURLOPT_TIMEOUT, self::$readTimeout);
		}
		if (self::$connectTimeout) {
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connectTimeout);
		}
		//https request
		if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}
		if (!is_array($headers) || count($headers) == 0)
		{
			$headers=array("Content-Type"=>"application/json; charset=utf-8");
		} else {
			$headers["Content-Type"] = "application/json; charset=utf-8";
		}
		$httpHeaders =self::getHttpHearders($headers);
		curl_setopt($ch,CURLOPT_HTTPHEADER,$httpHeaders);
		$httpResponse = new HttpResponse();
		$httpResponse->setBody(curl_exec($ch));
		$httpResponse->setStatus(curl_getinfo($ch, CURLINFO_HTTP_CODE));
		if (curl_errno($ch))
		{
			throw new Exception(curl_error($ch));
		}
		curl_close($ch);
		return $httpResponse;
	}

	static function getHttpHearders($headers)
	{
		$httpHeader = array();
		foreach ($headers as $key => $value)
		{
			array_push($httpHeader, $key.":".$value);
		}
		return $httpHeader;
	}
}

class HttpResponse
{
	private $body;
	private $status;
	
	public function getBody()
	{
		return $this->body;
	}
	
	public function setBody($body)
	{
		$this->body = $body;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
	
	public function setStatus($status)
	{
		$this->status  = $status;
	}
	
	public function isSuccess()
	{
		if(200 <= $this->status && 300 > $this->status)
		{
			return true;
		}
		return false;
	}
}