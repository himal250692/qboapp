<?php 

require __DIR__.'/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errrors',2);


use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Data\IPPVendor;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;



$quickbooks_consumer_key = "ABiqJ9K5jJibfb8LzBmCggUxzc8Pdrm7C7e49FH8tcs9sw7PzA";
$quickbooks_consumer_secret = "Ru3kfFeisbOFpjnPYGR1eEqjA9j9leBwv9114LQF";

$QBORealmID = $_GET['id'];
$refreshTokenKey = $_GET['refresh'];
$accessTokenKey = $_GET['access'];

$dataService = DataService::Configure(array(
  'auth_mode' => 'oauth2',
  'ClientID' => $quickbooks_consumer_key,
  'ClientSecret' => $quickbooks_consumer_secret,
  'QBORealmID'=>$QBORealmID,
  'accessTokenKey' => $accessTokenKey,
  'refreshTokenKey' => $refreshTokenKey,
  'baseUrl' => "development"
));

$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

echo  "<pre/>"; 
print_r($OAuth2LoginHelper); die;
$refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
$error = $OAuth2LoginHelper->getLastError();

die;
