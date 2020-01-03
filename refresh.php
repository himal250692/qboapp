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





// $quickbooks_consumer_key = "Q0FBTcGw546HxBRryeTU2MiipGj6eZFtHmhH79Q4TH9R0hzx7K";
// $quickbooks_consumer_secret = "xIZ3hpXs2B5pobzqb9MFQr6vgZ56D6LrYYTVUXC4";


// $QBORealmID = 123145710433487;
// $refreshTokenKey = 'AB11585332073TAk8UjyqLX5VCQIrKJS2Fce8ypeoKczBx2gG0';
// $accessTokenKey = 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..A2ZSp9LgJGJr_t-mwPfTPQ.HlJF6ZdyMO0lRlyC-LN25_6sY8rydQX22xYc5A085gK8mmdCSaN9sAWukOG4xLcADcXdQa3TALQoWegPXmOhD3s742gX3TCfSqCHXcJv2zBLm0ha1j1Tqqmv7IKNyFnv0GBWwpS4ngAoPo_WWTNLsqDHM5s8DyzhqpcxCqge8O50Pdw23lv1AQJdNpSJtLHRufTig42GOpfz1sLU7IzETzumow63E5iDFnz2NmZtk64Kb3JM0DRk4XE9vAhhqpexJqnAHc2p_oVeuijtFZgLqc09OkVXLiZPF4GbqUGC4m-zuQuBVIcHucJ4cMO8h_GJ-s1l2iVo78x9hNnz6PAy18zBTRidpbA3WKJ6WJmO_ArduKy471xCSkdh32PVRxblNBVIDZGYi3YjHCt-VSggybtPvatIdmad5VoG35OTt7darK0PWye7F7mFXGFseKk3VLMn-7HEGzEuQQXRCppHAr0wliS8Oyi4bQqBq6fZS9VBwf6yAmxEsm-XjID0V8LhXHDJT3H3wxq_JdYvQ_h-lo7qWRk25gXBikz08tXOlco7yKlYNm1I3pPVFSDwzX-gvw_38uiQrVFu792mEFtRT_x_JzQ_WCYrDoDM11VCEa-xmNtr1nFtQwnI3AcnHz-jPXzf7JVsGZYhU2c0lFm25Hm-dQ83smKv2TERM3J4skFvZkxdVpLkfHSY4bVlP11lCK3r8alxz_vbqAc30VOuC5sP8t05ZJXyeKQNjc30qgI.wb8m6napcs7lJpprkw5n0w';


$quickbooks_consumer_key = "AB0kSK3a7vKRBHpTvKOUR8Qi5nI0yW4mLcDNEtJeJg5ZxIA12A";
$quickbooks_consumer_secret = "6mHhzcv9UALzzsAytuEJG6MAGDLdgRgmUDln3kWR";


$QBORealmID = 4620816365026052790;
$refreshTokenKey = 'AB11585389898R3KMJ8DEjXvQkSC425fjoU1wz9oiDuLXZgQCl';
$accessTokenKey = 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..TOCrefxN6Sxv0hihd7zPXg.lyejPhzpggH3_j3mS98Eo7keUZuF-5UZK5s1Y-yBVRU-9SPX7eC98pGQ3CcbVn-tD2NeFdyTmYtloKQ_jCsQ1Z0NE2bmG8KIAOdYXl32_hyE2LE8oxGhYuTW5RPVAxt2kQxnatnovPl0A-lJGHv0yC8w0vySH0rmifPoBXCu-fCpxRzvtg6Un31TmKLrwe2O5a_F0p0CUHMyVXq9F401HIqYXKNAlcS61RtKSNDz8uMS6Alg64e1b9OJ5d1K794wxRl_PJ2-RSKZ2cW8NyOSoR5GGiNttkE1OgbyES4T9xbdzbycl7-BkTNDe-d0pPqnpOJrSwrWrU66WBvyPEcogeMqqW5jzozpA5QeZ5hCbutAcppkaE6MEHSipcDB6AIg-KSyOxf0tb3dL_34IViAf0GhF5QZSgphqcrgsdF5AGrt4QcQH9jeYiTB-syjHpaOlZZdUuL4AxS3QpbgFMvZ-khT4wd4w4tGIirdUK7mnTJOltXfP5GSeJ1rU8I4zCp7n5F04jzaPbGoeNV2Hv0uUfUcKJowDdviMI_6jIha5RixnQBz6Gm6g7K37eaDLjc9vRPTSMLFYuBgQLBn-AJ0eHHTqwu2DnD1l_9UZnNLDLAxZfgKLoYzx4FzGpAfwydnZw7LQG8XE2dO54M6_4VTwu-8NxDJBAMzCMOMM9eYIddD7X1GnTMVm3nVY9AmK8G4NRS32mY-qTrAw1B56tyznYY4lw2iPMz5DdHekUVY75Gw79wSKXMYHHwDvltxdD0NR_yB_-GW6srM5BDhgSFX04dcnUCB-HCUVpFJX4jFrnpZclKZOQPxGIsY35kX8ffpsFxaRAqQbDWvMaT-mcf4jnHrNdLZPxuAqAXjoXzzIBU.Y_P7DEPKWeN3XtbFNLM3JQ';

$dataService = DataService::Configure(array(
				'auth_mode' => 'oauth2',
				'ClientID' => $quickbooks_consumer_key,
				'ClientSecret' => $quickbooks_consumer_secret,
				'baseUrl' => 'development',
				'accessTokenKey' => $accessTokenKey,
				'refreshTokenKey' => $refreshTokenKey,
				'QBORealmID' => $QBORealmID
			));
$dataService->setMinorVersion(23);
$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

echo  "<pre/>ww"; 
print_r($OAuth2LoginHelper);

$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
$refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();

echo  "<pre/>33d"; 
print_r($refreshedAccessTokenObj); 
$error = $OAuth2LoginHelper->getLastError();

if($error){
    echo "<pre/>error"; print_r($error);
} else {
    $dataService->updateOAuth2Token($refreshedAccessTokenObj);
    $accessTokenValue = $refreshedAccessTokenObj->getAccessToken();
    $refreshTokenValue  = $refreshedAccessTokenObj->getRefreshToken();
    $accessTokenExpiresAt  = $refreshedAccessTokenObj->getAccessTokenExpiresAt();
    $accessTokenExpiresAtStr = strtotime($accessTokenExpiresAt);
    $accessTokenKey = $accessTokenValue;
    echo "UPDATE `connections` SET `access_token_value` = '" . $accessTokenKey . "', `refresh_token_value` = '" . $refreshTokenValue . "', `accessTokenExpiresAt` = '" . $accessTokenExpiresAt . "' WHERE `realm_id`='{$QBORealmID}' AND client_id='$client_id'";
}




