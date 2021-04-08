<?php

include "lib/teslapi.php";

$t = new Tesla();
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://pastebin.com/raw/pS7Z6yyP');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
$api = explode(PHP_EOL,$result);
$id=explode('=',$api[0]);
$secret=explode('=',$api[1]);
$t->setClientId(trim($id[1]));
$t->setClientSecret(trim($secret[1]));

$body = json_decode(file_get_contents('php://input'), true);

echo $body;
echo $body['refreshtoken'];

print(json_encode($t->refreshAccessToken($body['refreshtoken'])));

?>
