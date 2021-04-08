<?php

include "lib/teslapi.php";

$t = new Tesla();
$body = json_decode(file_get_contents('php://input'), true);

echo "Body:" . $body;
echo "Refresh" . $body['refreshtoken'];

print(json_encode($t->refreshAccessToken($body['refreshtoken'])));

?>
