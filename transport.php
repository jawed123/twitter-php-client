<?php

/*** 
* Transport class responsile for dispatching GET and POST request
*
*/

class  Transport{


/** 
* constructor
*
* Check if CURL is enabled in user php extension or not
* If not, throw exception
*/

	public function __construct(){
		if( !$this->cURLcheckBasicFunctions() ){
			throw new Exception("UNAVAILABLE: Curl extension not enabled");
		}

	}

/**
*	Function to check CURL extension 
*
*/
	private function cURLcheckBasicFunctions(){
	  if( !function_exists("curl_init") &&
	      !function_exists("curl_setopt") &&
	      !function_exists("curl_exec") &&
	      !function_exists("curl_close") ) return false;
	  else return true;
	}


/**
*	Make POST Request
*
* @param url $url  url to make post request to
* @param array $options options containing header and data to build payload
* @param $result $result reference varaible to store result
*
***********/
	public function http_post($url, $options, &$result){

		try{
			$ch = curl_init($url);
			
			//Set curl options
			$curl_options = array(
					CURLOPT_HTTPHEADER  => $this->get_headers($options['headers']),
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS =>  $this->get_urlEncoded($options['data'] )
				);

			$curl_opt_arr = ( $this->get_transport_defaults() + $curl_options );
			curl_setopt_array($ch, $curl_opt_arr);

			$result = curl_exec($ch);
			if( $result === false)
			{
			    echo 'Curl error: ' . curl_error($ch);
			    curl_close($ch);
			    return false;
			}else if( !empty($result)){
				$result = json_decode($result);
			}
			curl_close($ch);
			return true;

		}catch( Exception $e){
			echo "Failed to get result";
			echo $e->message();
			return false;
		}

	}

/**
*	Make GET Request
*
* @param url $url  url to make post request to
* @param array $options options containing header and data to build payload
* @param $result $result reference varaible to store result
*
***********/
	public function http_get($url, $options, &$content){

		$final_url_str = $url.'?'. $this->get_urlEncoded( $options['data'] );
		try{
			$ch = curl_init($final_url_str);
	
			$curl_opt_arr = ( $this->get_transport_defaults() + array(
					CURLOPT_HTTPHEADER => $this->get_headers($options['headers'])
				) );
			
			curl_setopt_array($ch, $curl_opt_arr);
			$content = curl_exec($ch);
			$header = curl_getinfo($ch);

			if($content === false)
			{
			    echo 'Curl error: ' . curl_error($ch);
			    curl_close($ch);
			}else if( !empty($content)){
				$content = json_decode($content);
			}
			curl_close($ch);
			return $header;

		}catch( Exception $e){
			echo "Failed to get result";
			echo $e->message();
			return false;
		}

	}

/**
* Give default options for curl requests
* 
* @return array Array of curl options with default values
*
**/
	private function get_transport_defaults(){
		return array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_SSL_VERIFYPEER => 0,
				CURLOPT_FOLLOWLOCATION => 1,
			);
	}

/**
*	convert associative array (key=>value)  into flat array( "key:value" , "key:value")
*	
* @param array $header_array associate array of headers list
* @return array $header  flat array
*
***/
	private function get_headers($header_array){
		$headers = array();
		foreach( $header_array as $name => $value) {
			array_push( $headers , $name.': '.$value);
		}
		return $headers;
	}

/**
*	url encode payload 
*
* @param  array $data 
* @return string url encoded string 
*
***/
	private function get_urlEncoded( $data){
		$encoded = '';
		foreach( $data as $name => $value) {
		  $encoded .= urlencode($name).'='.urlencode($value).'&';
		}
		// chop off last ampersand or comma
		$encoded = substr($encoded, 0, strlen($encoded)-1);
		return $encoded;
	}



}

?> 