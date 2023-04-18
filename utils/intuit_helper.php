<?php
require 		"vendor/autoload.php";

use QuickBooksOnline\Payments\OAuth\OAuth2Authenticator;
use QuickBooksOnline\Payments\PaymentClient;
use QuickBooksOnline\Payments\Operations\ChargeOperations;



function create_get_access_code() {

	$client_id 		= "ABWKSetUmr39YxrkUNqiNWXlxy1uMv5duJP5vYjPY1iADex4Ym"; //ISA KEY : ABQCG7SnlfmVp0xLpEe2ILzp0PTizfJlkpRWrESt2SvTlSKTuE
	$client_secret 	= "Rr4Zdi3BOlm81SCl4sTe7LXx5a7gbvPbSxY6JRYY"; // ISA Secret : mj4k7vFdhZK7NZBVWuezUQjc5mtXOKSLoBgguPVf

	$client 		= new PaymentClient();
	$oauth2Helper 	= OAuth2Authenticator::create([
	  'client_id' 		=> $client_id,
	  'client_secret' 	=> $client_secret,
	  'redirect_uri' 	=> 'http://www.paymentintuit/receive_code.php',
	  'environment' 	=> 'development'
	]);

	
	$scope 					= "com.intuit.quickbooks.accounting com.intuit.quickbooks.payment openid profile email phone address";
	$authorizationCodeURL 	= $oauth2Helper->generateAuthCodeURL($scope);

	header("Location: " . $authorizationCodeURL);
	die;
}

function getAccessTokens($code)
{
	$client_id 		= "ABWKSetUmr39YxrkUNqiNWXlxy1uMv5duJP5vYjPY1iADex4Ym";
	$client_secret 	= "Rr4Zdi3BOlm81SCl4sTe7LXx5a7gbvPbSxY6JRYY";

	$oauth2Helper 	= OAuth2Authenticator::create([
		'client_id' 		=> $client_id,
		'client_secret' 	=> $client_secret,
		'redirect_uri' 	=> 'http://www.paymentintuit/receive_code.php',
		'environment' 	=> 'development'
	]);
	$request 	= $oauth2Helper->createRequestToExchange($code);
	$client 	= new PaymentClient();
	$response 	= $client->send($request);
	
	if ($response->failed()) {
		$code 				= $response->getStatusCode();
		$errorMessage 		= $response->getBody();
	} else {
		//Get the keys
		$array = json_decode($response->getBody(), true);
	    
		$_SESSION['quick_refresh_token'] 	= $array["refresh_token"];
		$_SESSION['quick_real_token'] 		= $array["access_token"];
		$_SESSION['quick_id_token']         = $array["id_token"];
		$refreshToken = $array["refresh_token"];
		
	}

	

}

function create_intuit_payment() {
	
	$client = new PaymentClient([
	  'access_token' => $_SESSION['quick_real_token'],
	  'environment' => "sandbox", //  or 'environment' => "production"
	   'realmId'   => $_SESSION['quick_realmId']
	]);

	$array = [
	  "amount" => "10.55",
	  "currency" => "USD",
	  "card" => [
	      "name" => "emulate=0",
	      "number" => "4111111111111111",
	      "address" => [
	        "streetAddress" => "1130 Kifer Rd",
	        "city" => "Sunnyvale",
	        "region" => "CA",
	        "country" => "US",
	        "postalCode" => "94086"
	      ],
	      "expMonth" => "12",
	      "expYear" => "2024",
	      "cvc" => "123"
	  ],
	  "context" => [
	    "mobile" => "false",
	    "isEcommerce" => "true"
	  ]
	];

	$charge 	= ChargeOperations::buildFrom($array);
	$response 	= $client->charge($charge);
	if ($response->failed()) {

	    $code = $response->getStatusCode();
	    $errorMessage = $response->getBody();
	    echo "code is $code \n";
	    echo "body is $errorMessage \n";
	} else {

	  $responseCharge = $response->getBody();
	  var_dump( $responseCharge);
	  exit;
	  //Get the Id of the charge request
	  $id = $responseCharge->id;
	  //Get the Status of the charge request
	  $status = $responseCharge->status;

	  echo "Id is " . $id . "\n";
	  echo "status is " . $status . "\n";
	}
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>