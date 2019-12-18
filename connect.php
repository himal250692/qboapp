<?php 

require __DIR__.'/QuickBooks-V3-PHP-SDK/src/config.php';


use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Data\IPPVendor;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;



$quickbooks_consumer_key = "ABsjOTag8st1BgOmLZ6LOBLqKcUtYbt4CYO5gTkOfy50It2PXS";
$quickbooks_consumer_secret = "3wTaA9zqkCEv2JT9RX0r4s310xBMBcgnv6C04bdM";

if($_GET['action'] == 'refresh'){
    $QBORealmID = $_GET['id'];
    $refreshTokenKey = $_GET['key']; 


    //The first parameter of OAuth2LoginHelper is the ClientID, second parameter is the client Secret
    $oauth2LoginHelper = new OAuth2LoginHelper($quickbooks_consumer_key,$quickbooks_consumer_secret);
    $accessTokenObj = $oauth2LoginHelper->
                        refreshAccessTokenWithRefreshToken($refreshTokenKey);
    $accessTokenValue = $accessTokenObj->getAccessToken();
    $refreshTokenValue = $accessTokenObj->getRefreshToken();
    echo "Access Token is:";
    print_r($accessTokenValue);
    echo "RefreshToken Token is:";
    print_r($refreshTokenValue);
    die;
}

$dataService = DataService::Configure(array(
				'auth_mode' => 'oauth2',
				'ClientID' => $quickbooks_consumer_key,
				'ClientSecret' => $quickbooks_consumer_secret,
				'baseUrl' => "development",
				'refreshTokenKey' => $refreshTokenKey,
				'QBORealmID' => $QBORealmID,
                 'scope' => "com.intuit.quickbooks.accounting",
			));
               
$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
$error = $OAuth2LoginHelper->getLastError();

if($error){
    echo "<pre/>error"; print_r($error);
}
$refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
$error = $OAuth2LoginHelper->getLastError();

if($error){
    echo "<pre/>error"; print_r($error);
}
            

echo "<pre/>"; print_r($accessTokenObj); die;


$dataService = DataService::Configure(array(
  'auth_mode' => 'oauth2',
  'ClientID' => $quickbooks_consumer_key,
  'ClientSecret' => $quickbooks_consumer_secret,
  'RedirectURI' => "http://localhost/qbo/connect.php",
  'scope' => "com.intuit.quickbooks.accounting",
  'baseUrl' => "development"
));

		$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();


		if(@$_GET['realmId'] == ''){
			$authorizationCodeUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
			header("Location: ".$authorizationCodeUrl); exit;
		} else {
			$authorizationCode = @$_GET['code'];
			$RealmID = @$_GET['realmId'];
			$accessTokenObj = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($authorizationCode, $RealmID);
			$dataService->updateOAuth2Token($accessTokenObj);
			echo "<pre/>"; print_r($accessTokenObj); 
			 $accessTokenValue = $accessTokenObj->getAccessToken();
			
			$refreshTokenValue  = $accessTokenObj->getRefreshToken();
			$accessTokenExpiresAt = $accessTokenObj->getAccessTokenExpiresAt();
			$refreshTokenExpiresAt = $accessTokenObj->getRefreshTokenExpiresAt();
			
			
			
		
		$quickbooks_access_token = $accessTokenValue;
		echo "<br/>";
		$quickbooks_access_refresh_token = $refreshTokenValue;
		echo "<br/>";
		$quickbooks_realm_id = $RealmID;
		echo "<br/>";
	}
		



?>

