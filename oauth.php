<?php

include "lib/teslapi.php";

$body = json_decode(file_get_contents('php://input'), true);
print(json_encode($t->getAccessToken($body['email'], $body['password'])));

?>
