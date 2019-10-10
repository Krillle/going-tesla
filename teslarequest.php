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
    'content-type:application/json',
    'authorization:bearer '.$_GET['token']
  ));

  echo $json['body'];

?>
