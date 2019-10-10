<?php
  function hol_url($url, $header=false, $interface=false, $post = false) {
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
      CURLOPT_VERBOSE => 0,
      CURLOPT_PROTOCOLS => (CURLPROTO_HTTP | CURLPROTO_HTTPS),
      CURLOPT_REDIR_PROTOCOLS => (CURLPROTO_HTTP | CURLPROTO_HTTPS),
      CURLOPT_ENCODING => '',
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_MAXREDIRS => 4,
      CURLOPT_CONNECTTIMEOUT => 20
    ));
    if ($header) {
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    if ($interface) {
      curl_setopt($ch, CURLOPT_INTERFACE, $interface);
    }
    if ($post && is_array($post)) {
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    } else {
      curl_setopt($ch, CURLOPT_POST, 0);
    }
    $erg = curl_exec($ch);
    $info = curl_getinfo($ch);
    return array('body' => $erg, 'info' => $info);
  }

  $json = hol_url('https://owner-api.teslamotors.com/api/1/vehicles/44234482861270508/data_request/drive_state',array(
    'User-Agent:Mozilla/5.0 (Linux; Android 9.0.0; VS985 4G Build/LRX21Y; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.83 Mobile Safari/537.36',
    'X-Tesla-User-Agent:custom/goingtesla',
    'content-type:application/json',
    'authorization:bearer '.$_GET['token']
  ));

  header('content-type: application/json');
  echo $json['body'];

?>
