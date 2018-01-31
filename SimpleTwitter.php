<?php

class SimpleTwitter {

	var $oauth_access_token;
	var $oauth_access_token_secret;
	var $consumer_key;
	var $consumer_secret;

	public function __construct($oauth_access_token, $oauth_access_token_secret, $consumer_key, $consumer_secret)
	{
		$this->oauth_access_token = $oauth_access_token;
		$this->oauth_access_token_secret = $oauth_access_token_secret;
		$this->consumer_key = $consumer_key;
		$this->consumer_secret = $consumer_secret;
	}

	public function query($search, $lang="") 
	{
		$url = "https://api.twitter.com/1.1/search/tweets.json";
		$query = array( 'count' => 10, 'q' => urlencode($search), "result_type" => "recent", "lang" => $lang);
		
		$oauth = array(
						'oauth_consumer_key' => $this->consumer_key,
						'oauth_nonce' => time(),
						'oauth_signature_method' => 'HMAC-SHA1',
						'oauth_token' => $this->oauth_access_token,
						'oauth_timestamp' => time(),
						'oauth_version' => '1.0');

		$base_params = empty($query) ? $oauth : array_merge($query,$oauth);
		$base_info = $this->buildBaseString($url, 'GET', $base_params);
		$url = empty($query) ? $url : $url . "?" . http_build_query($query);

		$composite_key = rawurlencode($this->consumer_secret) . '&' . rawurlencode($this->oauth_access_token_secret);
		$oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
		$oauth['oauth_signature'] = $oauth_signature;

		$header = array($this->buildAuthorizationHeader($oauth), 'Expect:');
		$options = array( CURLOPT_HTTPHEADER => $header,
						  CURLOPT_HEADER => false,
						  CURLOPT_URL => $url,
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_SSL_VERIFYPEER => false);

		$feed = curl_init();
		curl_setopt_array($feed, $options);
		$json = curl_exec($feed);
		curl_close($feed);
		return  json_decode($json);
	}

	private function buildBaseString($baseURI, $method, $params)
	{
		$r = array(); 
		ksort($params);
		foreach($params as $key=>$value){
			$r[] = "$key=" . rawurlencode($value); 
		}
		return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r)); 
	}

	private function buildAuthorizationHeader($oauth)
	{
		$r = 'Authorization: OAuth '; 
		$values = array(); 
		foreach($oauth as $key=>$value)
			$values[] = "$key=\"" . rawurlencode($value) . "\""; 
		$r .= implode(', ', $values); 
		return $r; 
	}
}
?>


