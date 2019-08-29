<?php
/*


$guzzle = new \GuzzleHttp\Client();
$url = 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/token?api-version=1.0';
echo $url;
//$url = 'https://login.microsoftonline.com/common/oauth2/token?api-version=1.0';
$token = json_decode($guzzle->post($url, [
    'form_params' => [
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'resource' => 'https://graph.microsoft.com/',
        'grant_type' => 'client_credentials',
    ],
])->getBody()->getContents());
$accessToken = $token->access_token;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

$graph = new Graph();
$graph->setAccessToken($accessToken);
echo "<pre>",print_r($graph),"</pre>";
$user = $graph->createRequest("GET", "/me")
              ->setReturnType(Model\User::class)
              ->execute();

echo "Hello, I am $user->getGivenName() ";
*/
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;


class TestController extends Controller
{
    public function tests() 
    {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
    $clientId = "803d716b-736d-49f6-893d-5f2ee83e60e0";
$clientSecret = "hpdW1FFf4Xnvqq3KaFbdHvz";
$tenantId = "cac8c10c-4aa9-43d0-bd13-2a291864ccf0";
$adminEmail = "p2@spimarineusa.com";

$clientId = "0ab38d1c-0c95-4003-be3d-84b60252b033";
$clientSecret = "E5sqea4nBs8HfUcdkxBuQ8n";


//$guzzle = new \GuzzleHttp\Client();
$guzzle = new \GuzzleHttp\Client(
        array(
                "defaults" => array(
                        "allow_redirects" => true, "exceptions" => true,
                        "decode_content" => true,
                ),
                'cookies' => true,
                'verify' => false,
                // For testing with Fiddler
                //'proxy' => "localhost:8899",
                //'debug' => true,
        )
);
$url = 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/token?api-version=1.0';
echo $url;
//$url = 'https://login.microsoftonline.com/common/oauth2/token?api-version=1.0';
$token = json_decode($guzzle->post($url, [
    'form_params' => [
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'resource' => 'https://graph.microsoft.com/',
        'grant_type' => 'client_credentials',
    ],
])->getBody()->getContents());
$accessToken = $token->access_token;

$graph = new Graph();
$graph->setAccessToken($accessToken);
echo "<pre>",print_r($graph),"</pre>";
$user = $graph->createRequest("GET", "/me")
              ->setReturnType(Model\User::class)
              ->execute();

    return view('test', array(
      'accessToken' => $accessToken,
      'usermail' => "Billy",
      'messages' => "help me"
    ));
    
  }
    
}