<?php

class Tesla
{
protected $apiBaseUrl = "https://owner-api.teslamotors.com/api/1";
protected $tokenUrl = 'https://owner-api.teslamotors.com/oauth/token';
protected $tokenUrlNew = 'https://auth.tesla.com/oauth2/v3/token';
protected $accessUrl = 'https://auth.tesla.com/oauth2/v3/authorize';
protected $accessToken;
protected $vehicleId = null;

public function __construct(string $accessToken = null)
{
    $this->accessToken = $accessToken;
}

public function setAccessToken(string $accessToken)
{
    $this->accessToken = $accessToken;
}

public function allData()
{
    return $this->sendRequest('/vehicle_data')['response'];
}

public function vehicles()
{
    return $this->sendRequest('/vehicles');
}

public function vehicle()
{
    return $this->sendRequest('')['response'];
}

public function setVehicleId(int $vehicleId)
{
    $this->vehicleId = $vehicleId;
}

public function setClientId(string $clientId)
{
    putenv('TESLA_CLIENT_ID=' . $clientId);
}

public function setClientSecret(string $clientSecret)
{
    putenv('TESLA_CLIENT_SECRET=' . $clientSecret);
}

public function mobileEnabled()
{
    return $this->sendRequest('/mobile_enabled')['response'];
}

public function chargeState()
{
    return $this->sendRequest('/data_request/charge_state')['response'];
}

public function climateState()
{
    return $this->sendRequest('/data_request/climate_state')['response'];
}

public function driveState()
{
    return $this->sendRequest('/data_request/drive_state')['response'];
}

public function guiSettings()
{
    return $this->sendRequest('/data_request/gui_settings')['response'];
}

public function vehicleState()
{
    return $this->sendRequest('/data_request/vehicle_state')['response'];
}

public function vehicleConfig()
{
    return $this->sendRequest('/data_request/vehicle_config')['response'];
}

public function wakeUp()
{
    return $this->sendRequest('/wake_up', [], 'POST')['response'];
}

public function startSoftwareUpdate( int $seconds = 0 )
{
    return $this->sendRequest('/command/schedule_software_update', [ 'offset_sec' => $seconds ], 'POST')['response'];
}

public function setValetMode(bool $active = false, int $pin = 0000)
{
    $params = [
        'on' => $active,
        'pin' => $pin
    ];

    return $this->sendRequest('/command/set_valet_mode', $params, 'POST')['response'];
}

public function resetValetPin()
{
    return $this->sendRequest('/command/reset_valet_pin', [], 'POST')['response'];
}

public function openChargePort()
{
    return $this->sendRequest('/command/charge_port_door_open', [], 'POST')['response'];
}

public function setChargeLimitToStandard()
{
    return $this->sendRequest('/command/charge_standard', [], 'POST')['response'];
}

public function setChargeLimitToMaxRange()
{
    return $this->sendRequest('/command/charge_max_range', [], 'POST')['response'];
}

public function setChargeLimit(int $percent = 90)
{
    $params = [
        'percent' => "$percent"
    ];
    return $this->sendRequest('/command/set_charge_limit', $params, 'POST')['response'];
}

public function startCharging()
{
    return $this->sendRequest('/command/charge_start', [], 'POST')['response'];
}

public function stopCharging()
{
    return $this->sendRequest('/command/charge_stop', [], 'POST')['response'];
}

public function flashLights()
{
    return $this->sendRequest('/command/flash_lights', [], 'POST')['response'];
}

public function honkHorn()
{
    return $this->sendRequest('/command/honk_horn', [], 'POST')['response'];
}

public function unlockDoors()
{
    return $this->sendRequest('/command/door_unlock', [], 'POST')['response'];
}

public function lockDoors()
{
    return $this->sendRequest('/command/door_lock', [], 'POST')['response'];
}

public function windowControl(string $state = 'close', int $lat = 0, int $lon = 0)
{
    return $this->sendRequest('/command/window_control', [ 'command' => $state, 'lat' => $lat, 'lon' => $lon ], 'POST')['response'];
}

public function setTemperature(float $driverDegreesCelcius = 20.0, float $passengerDegreesCelcius = 20.0)
{
    return $this->sendRequest('/command/set_temps?driver_temp=' . $driverDegreesCelcius . '&passenger_temp=' . $passengerDegreesCelcius, [], 'POST')['response'];
}

public function startHvac()
{
    return $this->sendRequest('/command/auto_conditioning_start', [], 'POST')['response'];
}

public function stopHvac()
{
    return $this->sendRequest('/command/auto_conditioning_stop', [], 'POST')['response'];
}

public function movePanoramicRoof(string $state = 'vent', int $percent = 50)
{
    return $this->sendRequest('/command/sun_roof_control?state=' . $state . '&percent=' . $percent, [], 'POST')['response'];
}

public function remoteStart(string $password = '')
{
    return $this->sendRequest('/command/remote_start_drive?password=' . $password, [], 'POST')['response'];
}

public function openTrunk()
{
    return $this->sendRequest('/command/actuate_trunk', [ 'which_trunk' => 'rear' ], 'POST')['response'];
}

public function openFrunk()
{
    return $this->sendRequest('/command/actuate_trunk', [ 'which_trunk' => 'front' ], 'POST')['response'];
}

public function setNavigation(string $location)
{
    $params = [
        'type' => 'share_ext_content_raw',
        'value' => [
            'android.intent.extra.TEXT' => $location
        ],
        'locale' => 'en-US',
        'timestamp_ms' => time(),
    ];
    return $this->sendRequest('/command/navigation_request', $params, 'POST')['response'];
}

public function startSentry()
{
    return $this->sendRequest('/command/set_sentry_mode', [ 'on' => True], 'POST')['response'];
}

public function stopSentry()
{
    return $this->sendRequest('/command/set_sentry_mode', [ 'on' => False], 'POST')['response'];
}

public function setSeatHeater(int $heater = 0, int $level = 0)
{
    return $this->sendRequest('/command/remote_seat_heater_request', ['heater' => $heater, 'level' => $level], 'POST')['response'];
}

 public function setTemps(float $driver = 21.0, float $passenger = 21.0)
{
    return $this->sendRequest('/command/set_temps', ['driver_temp' => $driver, 'passenger_temp' => $passenger], 'POST')['response'];
}

public function base64url_encode($data) {
	return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

public function getAccessToken(string $username, string $password)
{
###Step 1: Obtain the login page

	$code_verifier = substr(hash('sha512', mt_rand()), 0, 86);
	$state = $this->base64url_encode(substr(hash('sha256', mt_rand()), 0, 12));
	$code_challenge = $this->base64url_encode($code_verifier);

	$data =[
        'client_id' => 'ownerapi',
        'code_challenge' => $code_challenge,
        'code_challenge_method' => 'S256',
        'redirect_uri' => 'https://auth.tesla.com/void/callback',
        'response_type' => 'code',
        'scope' => 'openid email offline_access',
        'state' => $state,
    ];

	$GetUrl = $this->accessUrl.'?'.http_build_query ($data);


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $GetUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, 1);
	$apiResult = curl_exec($ch);
    curl_close($ch);

    $dom = new DomDocument();
	@ $dom->loadHTML($apiResult);
	$child_elements = $dom->getElementsByTagName('input'); //DOMNodeList
	foreach( $child_elements as $h2 ) {
		$hiddeninputs[$h2->getAttribute('name')]=$h2->getAttribute('value');
	}
	$headers = [];
	$output = rtrim($apiResult);
	$data = explode("\n",$output);
	$headers['status'] = $data[0];
	array_shift($data);

	foreach($data as $part){
		//some headers will contain ":" character (Location for example), and the part after ":" will be lost, Thanks to @Emanuele
		$middle = explode(":",$part,2);

		//Supress warning message if $middle[1] does not exist, Thanks to @crayons
		if ( !isset($middle[1]) ) { $middle[1] = null; }
			$headers[trim($middle[0])] = trim($middle[1]);
	}

	if (isset($headers['Set-Cookie'])){
		$cookie = $headers['Set-Cookie'];
	} elseif (isset($headers['set-cookie'])){
		$cookie = $headers['set-cookie'];
	}

###Step 2: Obtain an authorization code

	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $GetUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Cookie: '.$cookie));
	$postData = array(
		'_csrf' => $hiddeninputs['_csrf'],
		'_phase' => $hiddeninputs['_phase'],
		'_process' => $hiddeninputs['_process'],
		'transaction_id' => $hiddeninputs['transaction_id'],
		'cancel' =>$hiddeninputs['cancel'],
        'identity' => $username,
      	'credential' => $password
    );
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);


    $apiResult = curl_exec($ch);
    curl_close($ch);
	#print_r($apiResult);
	$code= explode('&',explode('https://auth.tesla.com/void/callback?code=',$apiResult)[1])[0];
	#print 'CODE'.$code;
###Step 3: Exchange authorization code for bearer token

	$ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $this->tokenUrlNew);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'User-Agent: Mozilla/5.0 (Linux; Android 9.0.0; VS985 4G Build/LRX21Y; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.83 Mobile Safari/537.36',
    'X-Tesla-User-Agent: TeslaApp/3.4.4-350/fad4a582e/android/9.0.0',
  ]);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'grant_type' => 'authorization_code',
    'client_id' => 'ownerapi',
    'code' => $code,
    'code_verifier' => $code_verifier,
    'redirect_uri' => 'https://auth.tesla.com/void/callback'
  ]));

  $apiResult = curl_exec($ch);
  curl_close($ch);

  $apiResultJson = json_decode($apiResult, true);
  $BearerToken = $apiResultJson['access_token'];
  $RefreshToken = $apiResultJson['refresh_token'];
###Step 4: Exchange bearer token for access token

	$ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $this->tokenUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '.$BearerToken,
    'Content-Type: application/json'
  ]);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
    'client_id' => getenv('TESLA_CLIENT_ID'),
    'client_secret' => getenv('TESLA_CLIENT_SECRET'),
  ]));

  $apiResult = curl_exec($ch);
  curl_close($ch);

  $apiResultJson = json_decode($apiResult, true);
  $apiResultJson['refresh_token']=$RefreshToken;
  #print_r($apiResultJson);exit;
  $this->accessToken = $apiResultJson['access_token'];

    return $apiResultJson;
}

public function refreshAccessToken(string $refreshToken)
{
$tesla = new Tesla();
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://pastebin.com/raw/pS7Z6yyP');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
$api = explode(PHP_EOL,$result);
$id=explode('=',$api[0]);
$secret=explode('=',$api[1]);
$tesla->setClientId(trim($id[1]));
$tesla->setClientSecret(trim($secret[1]));

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $this->tokenUrlNew);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'User-Agent: Mozilla/5.0 (Linux; Android 9.0.0; VS985 4G Build/LRX21Y; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.83 Mobile Safari/537.36',
    'X-Tesla-User-Agent: TeslaApp/3.4.4-350/fad4a582e/android/9.0.0',
  ]);
  #print 'XX '.getenv('TESLA_CLIENT_ID');exit;
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'grant_type' => 'refresh_token',
    'client_id' => 'ownerapi',
    'client_secret' => getenv('TESLA_CLIENT_SECRET'),
    'refresh_token' => $refreshToken,
    'scope' => 'openid email offline_access'
  ]));

  $apiResult = curl_exec($ch);
  $apiResultJson = json_decode($apiResult, true);

  curl_close($ch);
  #print_r($apiResult);exit;

  $apiResultJson = json_decode($apiResult, true);
  $BearerToken = $apiResultJson['access_token'];
  $RefreshToken = $apiResultJson['refresh_token'];
###Step 4: Exchange bearer token for access token

	$ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $this->tokenUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '.$BearerToken,
    'Content-Type: application/json'
  ]);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
    'client_id' => getenv('TESLA_CLIENT_ID'),
    'client_secret' => getenv('TESLA_CLIENT_SECRET'),
  ]));

  $apiResult = curl_exec($ch);
  curl_close($ch);

  $apiResultJson = json_decode($apiResult, true);
  $apiResultJson['refresh_token']=$RefreshToken;
  #print_r($apiResultJson);exit;
  $this->accessToken = $apiResultJson['access_token'];

    return $apiResultJson;
}

protected function sendRequest(string $endpoint, array $params = [], string $method = 'GET')
{
    $ch = curl_init();

    if ($endpoint !== '/vehicles' && ! is_null($this->vehicleId)) {
        $endpoint = '/vehicles/' . $this->vehicleId . $endpoint;
    }

    curl_setopt($ch, CURLOPT_URL, $this->apiBaseUrl . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'User-Agent: Mozilla/5.0 (Linux; Android 9.0.0; VS985 4G Build/LRX21Y; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.83 Mobile Safari/537.36',
        'X-Tesla-User-Agent: TeslaApp/3.4.4-350/fad4a582e/android/9.0.0',
        'Authorization: Bearer ' . $this->accessToken,
    ]);

    if ($method == 'POST' || $method == 'PUT' || $method == 'DELETE') {
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        }
        if (in_array($method, ['PUT', 'DELETE'])) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    }

    $apiResult = curl_exec($ch);
    $headerInfo = curl_getinfo($ch);
    $apiResultJson = json_decode($apiResult, true);
    curl_close($ch);

    $result = [];
    if ($apiResult === false) {
        $result['errorcode'] = 0;
        $result['errormessage'] = curl_error($ch);

        //print $result['errormessage'].' api<br>';
    }

    if (! in_array($headerInfo['http_code'], ['200', '201', '204'])) {
        $result['errorcode'] = $headerInfo['http_code'];
        if (isset($apiResult)) {
            $result['errormessage'] = $apiResult;
        }

        //print $result['errormessage'].' header<br>';;
    }

    return $apiResultJson ?? $apiResult;

}
}

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

var_dump(file_get_contents('php://input'));

$body = json_decode(file_get_contents('php://input'), true);

var_dump($body);
print ($body["email"]);
print ($body["password"]);

// var_dump($t->getAccessToken($_POST["email"], $_POST["password"]));

?>
