<?php 

require __DIR__.'/vendor/autoload.php';


use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Data\IPPVendor;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;



$quickbooks_consumer_key = "AB0kSK3a7vKRBHpTvKOUR8Qi5nI0yW4mLcDNEtJeJg5ZxIA12A";
$quickbooks_consumer_secret = "6mHhzcv9UALzzsAytuEJG6MAGDLdgRgmUDln3kWR";



$dataService = DataService::Configure(array(
  'auth_mode' => 'oauth2',
  'ClientID' => $quickbooks_consumer_key,
  'ClientSecret' => $quickbooks_consumer_secret,
  'RedirectURI' => "http://localhost/qbo/qboapp/connect.php",
  'scope' => "com.intuit.quickbooks.accounting openid email address",
  'baseUrl' => "development"
));
$dataService->setMinorVersion(23);

		$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();


		if(@$_GET['realmId'] == ''){
			$authorizationCodeUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
			header("Location: ".$authorizationCodeUrl); exit;
		} else {
			echo $authorizationCode = @$_GET['code'];
			echo $RealmID = @$_GET['realmId'];
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

