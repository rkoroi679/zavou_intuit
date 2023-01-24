<?php
require 		"vendor/autoload.php";

use QuickBooksOnline\Payments\OAuth\OAuth2Authenticator;
use QuickBooksOnline\Payments\PaymentClient;
use QuickBooksOnline\Payments\Operations\ChargeOperations;

$client_id 		= "ABWKSetUmr39YxrkUNqiNWXlxy1uMv5duJP5vYjPY1iADex4Ym";
$client_secret 	= "Rr4Zdi3BOlm81SCl4sTe7LXx5a7gbvPbSxY6JRYY";

function create_get_access_token() {

	$client 		= new PaymentClient();
	$oauth2Helper 	= OAuth2Authenticator::create([
	  'client_id' 		=> $client_id,
	  'client_secret' 	=> $client_secret,
	  'redirect_uri' 	=> 'https://developer.intuit.com/v2/OAuth2Playground/RedirectUrl',
	  'environment' 	=> 'development'
	]);

	$scope 					= "com.intuit.quickbooks.accounting openid profile email phone address";
	$authorizationCodeURL 	= $oauth2Helper->generateAuthCodeURL($scope);
	
	//Redirect User to the $authorizationCodeURL, and a code will be sent to your redirect_uri as query paramter;
	$code = "SomeVeryUniqueString";
	$request = $oauth2Helper->createRequestToExchange($code);
	$response = $client->send($request);
	if ($response->failed()) {
	  $code = $response->getStatusCode();
	  $errorMessage = $response->getBody();
	  echo "code is $code \n";
	  echo "body is $errorMessage \n";
	} else {
	  //Get the keys
	  $array = json_decode($response->getBody(), true);
	  $refreshToken = $array["refresh_token"];
	  //AB11570127472xkApQcZmbTMGfzzEOgMWl2Br5h8IEgxRULUbO
	}
}

function create_intuit_payment() {

	$client = new PaymentClient([
	  'access_token' => "your access token",
	  'environment' => "sandbox" //  or 'environment' => "production"
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
	      "expYear" => "2021",
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
	  //Get the Id of the charge request
	  $id = $responseCharge->id;
	  //Get the Status of the charge request
	  $status = $responseCharge->status;

	  echo "Id is " . $id . "\n";
	  echo "status is " . $status . "\n";
	}
}

?>