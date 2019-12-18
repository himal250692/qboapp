<?php 

require __DIR__.'/vendor/autoload.php';


use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Data\IPPVendor;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;



$quickbooks_consumer_key = "ABsjOTag8st1BgOmLZ6LOBLqKcUtYbt4CYO5gTkOfy50It2PXS";
$quickbooks_consumer_secret = "3wTaA9zqkCEv2JT9RX0r4s310xBMBcgnv6C04bdM";



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
