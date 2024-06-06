<?php
header('Content-Type: application/json');
include('config.php');
require 'vendor/autoload.php';

use HTTP_Request2;
use OAuthConsumer;
use OAuthRequest;
use OAuthSignatureMethod_HMAC_SHA1;
use OAuthToken;

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'];
$pseudo = $data['name']; // Assuming 'name' is used for both 'name' and 'pseudo'
$pwd = password_hash($data['password'], PASSWORD_DEFAULT);
$mail = $data['email'];

// Valeurs par défaut pour d'autres informations
$default_rights = '1';
$default_agency = '1';
$default_is_commercial = '0';
$default_sign = '';
$default_token = '';
$default_token_mail = '';

$response = array();

// Ajout de l'utilisateur à la base de données locale
$sql = "INSERT INTO users_inter (name, pseudo, pwd, mail, rights, agency, is_commercial, mail_commercial, sign, token, token_mail) VALUES ('$name', '$pseudo', '$pwd', '$mail', '$default_rights', '$default_agency', '$default_is_commercial', '$mail', '$default_sign', '$default_token', '$default_token_mail')";
if ($conn->query($sql) === TRUE) {
    // Intégration de l'API Sellsy
    $consumer_key = 'bfd82c54ad4dcc9a9ef07501bb18d894e7582022';
    $consumer_secret = '4ac53635bbb02d62d2b5f910a3bdadbc9aa77ce9';
    $token = '6ea8794b6d17232d0c27ecae475ad31c794acf6a';
    $token_secret = '29c94253df776b3da081d953bf6506960eedf60f';

    $consumer = new OAuthConsumer($consumer_key, $consumer_secret);
    $accessToken = new OAuthToken($token, $token_secret);

    $sig_method = new OAuthSignatureMethod_HMAC_SHA1();

    $url = 'https://apifeed.sellsy.com/0/request';
    $params = [
        'request' => 'People.create',
        'params' => [
            'name' => $name,
            'pseudo' => $pseudo,
            'mail' => $mail,
            'rights' => $default_rights,
            'agency' => $default_agency,
            'is_commercial' => $default_is_commercial,
            'mail_commercial' => $mail,
            'sign' => $default_sign,
            'token' => $default_token,
            'token_mail' => $default_token_mail,
        ]
    ];

    $req = OAuthRequest::from_consumer_and_token($consumer, $accessToken, 'POST', $url, $params);
    $req->sign_request($sig_method, $consumer, $accessToken);

    $request = new HTTP_Request2($url, HTTP_Request2::METHOD_POST);
    $request->setHeader('Content-Type: application/json');
    $request->setBody(json_encode($params));
    $request->setURL($req->to_url());

    try {
        $api_response = $request->send();
        if (200 == $api_response->getStatus()) {
            $response['status'] = 'success';
            $response['message'] = 'User registered successfully';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Unexpected HTTP status: ' . $api_response->getStatus() . ' ' . $api_response->getReasonPhrase();
        }
    } catch (HTTP_Request2_Exception $e) {
        $response['status'] = 'error';
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Error: ' . $conn->error;
}

echo json_encode($response);
?>
