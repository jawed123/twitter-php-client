<?php
/**
*  Simple PHP Class to fetch twitter tweets
*
*
*/


require_once("transport.php");


/***
*  SK_Tweets Class : Twitter api
*
***********/
class SK_Tweets{
	
	
	private $config = null;
	const SEARCH_ENDPOINT="https://api.twitter.com/1.1/search/tweets.json";
	const OAUTH_ENDPOINT="https://api.twitter.com/oauth2/token";
	private $app_token  = array(); // token_type: bearer, acces_token

	private $transport = null;

/**
* Initialized class with provided config file
*
* @param  file  $config_file optional paramater if file is not in current directory
* @return new object of class
*
********/
	public function __construct($config_file="config.ini"){
		$this->config = parse_ini_file( $config_file, 1);
		$this->transport = new Transport();
		$this->init();

	}

/**
* initalization
*
*********/

	private function init(){
		if( empty($this->config["CONSUMER_KEY"]) 
				|| empty( $this->config["CONSUMER_SECRET"] ) ){
			echo $this->help_text();
			throw new Exception("API Credentials missing");
		}

		//Retrieve Acess token

		$this->retrieve_token();
	}


/**
*  Print help text
*
* @return string help string containing how-to instructions
*
********/
	public function help_text(){
		$str ='
		HOW TO USE
		=======================
		-> Create config.ini file
		-> Get app Crdentials from https://apps.twitter.com. Ignore if you already have them.
		-> Put CONSUMER_KEY and CONSUMER_SECRET value in `config.ini` file.

		';
		return $str;
	}

/**
*   Retrieve access token from twitter oauth url
* 
********/
	public function retrieve_token(){

		$options = array(
				'headers' => array(
						'Authorization'=> "Basic ".$this->encode_credential(),
						'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
					),
				'data' =>array(
						'grant_type' => 'client_credentials'
					)
			);

		$response = $this->transport->http_post( self::OAUTH_ENDPOINT, $options, $token);
		if( $response){
			if(  empty($token->errors ) ){
				$this->app_token = $token->access_token;
			}else{
				throw new Exception( $token);
			}
		}

	}

/**
*  encode credentials and create string
*
********/
	private function encode_credential(){

		return base64_encode( urlencode( $this->config["CONSUMER_KEY"] ). ":" . urlencode($this->config["CONSUMER_SECRET"]) ) ;
	}

/**
* Fire twitter search query
*
*  @param  array containing twitter search params
*  @return array tweets array
* 
********/
	public function search( $data=array()){

		if( !$this->is_authorized()){
			throw new Exception("UNAUTHORIZED: Authenticatin Failed");
		};

		$options = array(
				'headers' => $this->get_BearerHeader(),
				'data' =>  $data
			);
		$response = $this->transport
						->http_get( self::SEARCH_ENDPOINT, $options, $tweets);

		if( $response['http_code'] == 200 ){
			if( array_key_exists( "errors", $tweets) ){
				throw new Exception("UNAUTHORIZED: Authenticatin Failed");
			}else{
				return $tweets;
			}
		}
		return array();

	}

/**
* Simple check to see if token is retrieved and set or not
*
* @return boolean
********/
	private function is_authorized(){
		return !empty($this->app_token) ;

	}
/**
*  get authorization header 
*
* @return array authorization header array
*
********/
	private  function get_BearerHeader(){
		return array(
			'Authorization' => 'Bearer '.$this->app_token
			);
	}



}

?>