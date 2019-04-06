<?php
namespace BigDataCloud\Api;

class Client {
	public $includeTimer=0;
	public $apiKey;
	public $nameSpace;
	public $server='api.bigdatacloud.net';
	public $code;
	public $timeout=120;

	public function __construct($apiKey,$nameSpace='data',$server=null) {
		if ($server) $this->server=$server;
		$this->apiKey=$apiKey;
		$this->nameSpace=$nameSpace;
	}

	public function setIncludeTimer($includeTimer=1) {
		$this->includeTimer=$includeTimer;
		return $this;
	}

	public function getIncludeTimer() {
		return $this->includeTimer;
	}

	public function __call(string $name, Array $args=[]) {
		$key=$name;
		$key[0] = strtolower($key[0]);
		$key = preg_replace_callback('/([A-Z])/', function($c) {
			return '-'.strtolower($c[1]);
		}, $key);
		$key=explode('-',$key);
		$type=strtoupper(array_shift($key));
		switch($type) {
			case 'GET':
			case 'POST':
			case 'PUT':
			case 'PATCH':
			case 'DELETE':
			case 'HEAD':
			return $this->communicate(
				implode('-',$key),
				$type,
				count($args) ? $args[0] : null
			);
			break;
		}

		throw new \BadMethodCallException('Method '.$name.' does not exist');
	}

	public function communicate(string $endpoint,string $type,$payload=null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
		$qs='';
		$headers=[];
		if (count($payload)) {
			switch($type) {
				case 'GET':
				case 'HEAD':
				case 'DELETE':
				$qs='?'.http_build_query($payload);
				break;
				case 'POST':
				case 'PUT':
				case 'PATCH':
				$headers[]='Content-Type:application/json';
				curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($payload));
				break;
			}
		}
		curl_setopt($ch, CURLOPT_URL,"https://".$this->server."/".$this->nameSpace.'/'.$endpoint.$qs);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if (!$payload || ($payload && !array_key_exists('key',$payload))) $headers[]='X-BDC-Key: '.$this->apiKey;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$timerStart=microtime(true);
		$response = curl_exec ($ch);
		$timerStop=microtime(true);
		$httpcode = $this->code=curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		switch ($httpcode) {
			case 200:
			if (!$response) {
				throw new \Exception('Invalid or empty response returned from BDC API: '.print_r($response,1));
			}
			try {
				if ($response=json_decode($response,true)) {
					if ($this->getIncludeTimer()) {
						$response['__apiResponseTime']=round(($timerStop-$timerStart)*1000,2);
					}
					return $response;
				} else {
					throw new \Exception('Invalid JSON returned from BDC API: '.print_r($response,1));
				}
			} catch (\Exception $e) {
				throw new \Exception('Invalid JSON returned from BDC API: '.print_r($response,1));
			}
			break;
			case 404:
			return false;
			break;
			case 403:
				throw new \Exception('Permission denied accessing BDC API'."\n".print_r($response,1),$httpcode);
			break;
			default:
				throw new \Exception('Error communicating with the BDC API: '.print_r($response,1),$httpcode);
			break;
		}

	}
}