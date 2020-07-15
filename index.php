<!DOCTYPE html>
<html>
<head>
  <?php
  function console_log($output, $with_script_tags = true) {
      $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
      if ($with_script_tags) {
          $js_code = '<script>' . $js_code . '</script>';
      }
      echo $js_code;
  };

  if (!isset($_SERVER["mapbox"])) {
    console_log("No mapbox API key found.",true);
  };

  if (!isset($_SERVER["goingelectric"])) {
    console_log("No GoingElectric API key found.",true);
  };

  if (isset($_COOKIE["location"])) {
    // Sunset for last known location
    $location = json_decode($_COOKIE["location"]);
    $lat = $location->latitude;
    $lon = $location->longitude;

    console_log("Location Cookie found. Using individual daylight times.",true);
  } else {
    console_log("No location Cookie found. Using default daylight times.",true);
    // Sunset defalut location Berlin
    $lat = 52.52;
    $lon = 13.41;
  };

  $offset = 0; // Herokuserver timezome is GMT
  $sunrise = date_sunrise(time(), SUNFUNCS_RET_TIMESTAMP, $lat, $lon, 90, $offset);
  $sunset = date_sunset(time(), SUNFUNCS_RET_TIMESTAMP, $lat, $lon, 90, $offset);
  $now = time();

  if ($now < $sunrise or $now > $sunset) {
    $darkmode = true;
  } else {
    $darkmode = false;
  };

  if (isset($_GET["light"])) {$darkmode = false;};
  if (isset($_GET["dark"])) {$darkmode = true;};

  ?>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-154570316-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'UA-154570316-1');
  </script>
  <meta charset='utf-8' />
  <title>Goingtesla</title>
  <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
  <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.3.1/mapbox-gl.js'></script>
  <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.3.1/mapbox-gl.css' rel='stylesheet' />
  <script src="lib/geolib.js"></script>

  <script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.4.2/mapbox-gl-geocoder.min.js'></script>
  <link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.4.2/mapbox-gl-geocoder.css' type='text/css' />
  <!-- Promise polyfill script required to use Mapbox GL Geocoder in IE 11 -->
  <script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js"></script>

  <style>
    body { margin:0; padding:0; }
    #map { position:absolute; top:0; bottom:0; width:100%; }

    .mapboxgl-ctrl {
      opacity: 0.7;
    }

    .mapboxgl-ctrl-bottom-left .mapboxgl-ctrl  { margin: 0 0 10px 10px; float: left; }
    .mapboxgl-ctrl-bottom-right .mapboxgl-ctrl { margin: 0 10px 15px 0; float: right; }

    .mapboxgl-ctrl-group,
    .mapboxgl-ctrl-geocoder,
    .mapboxgl-ctrl-geocoder .suggestions,
    .mapboxgl-ctrl-geocoder .suggestions > li > a,
    .mapboxgl-ctrl-geocoder .suggestions > .active > a,
    .mapboxgl-ctrl-geocoder .suggestions > li > a:hover,
    .mapboxgl-popup-content,
    .info-container,
    .range-container,
    .log-container,
    .route-container {
      background:#ffffff; /* light theme  */
      <? if ($darkmode) {echo "background:#000000; /* dark theme */";} ?>
    }

    .mapboxgl-ctrl-geocoder--input,
    .mapboxgl-ctrl-geocoder--input:focus,
    .mapboxgl-ctrl-geocoder .suggestions > li > a,
    .mapboxgl-ctrl-geocoder .suggestions > .active > a,
    .mapboxgl-ctrl-geocoder .suggestions > li > a:hover,
    .mapboxgl-popup-content,
    .mapboxgl-popup-content a,
    .info-container,
    .range-container,
    .log-container,
    .route-container,
    .route-container a {
      color:#8F8F8F; /* light theme  */
      <? if ($darkmode) {echo "color:#9c9c9c; /* dark theme */";} ?>
    }

    .mapboxgl-ctrl-geocoder,
    .mapboxgl-popup-content,
    .info-container,
    .range-container,
    .log-container,
    .route-container {
      font:400 20px/1.15 'Gotham Medium', 'Verdana', 'Source Sans Pro', 'Helvetica Neue', Sans-serif;
    }

    .mapboxgl-ctrl-geocoder--suggestion-title,
    .mapboxgl-popup-content strong {
      font-weight: 700;
      color:#000000; /* light theme  */
      <? if ($darkmode) {echo "color:#e6e6e6; /* dark theme */";} ?>
    }

    .mapboxgl-ctrl-group > button {
      width:70px;
      height:70px;
    }

    .mapboxgl-ctrl-group > button + button {
        border-top: 1px solid #ddd;
        <? if ($darkmode) {echo "border-top: 1px solid #333333; /* dark theme */";} ?>
    }

    .mapboxgl-ctrl-group > button:focus,
    .mapboxgl-ctrl-group > button:focus:focus-visible,
    .mapboxgl-ctrl-group > button:focus:first-child,
    .mapboxgl-ctrl-group > button:focus:last-child {
      outline: none;
      border: none;
      box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
    }

    .mapboxgl-ctrl-icon.mapboxgl-ctrl-compass > .mapboxgl-ctrl-compass-arrow  {
      width:40px;
      height:40px;
    }

    .mapboxgl-ctrl-icon.mapboxgl-ctrl-autozoom > .mapboxgl-ctrl-autozoom-icon {
      width: 40px;
      height: 40px;
      margin: 5px;
      background-image: url("https://img.icons8.com/small/40/333333/gps-device.png");
      background-repeat: no-repeat;
      display: inline-block;
    }

    .mapboxgl-ctrl-top-left .mapboxgl-ctrl {
      width: 400px;
      min-width: 400px;
      max-width:400px;
      opacity: 0.9;
    }

    .mapboxgl-ctrl-geocoder--input {
      height: 60px;
    }

    .mapboxgl-ctrl-geocoder .suggestions {
      font-size: 20px;
    }

    .mapboxgl-ctrl-geocoder--button {
      background-color: transparent;
    }

    .mapboxgl-ctrl-geocoder--icon {
      top: 18px;
      left: 5px;
      width: 25px;
      height: 25px;
    }

    .mapboxgl-ctrl-geocoder--icon-close {
      margin-top:11px
    }

    .mapboxgl-ctrl-geocoder--icon-loading {
      width: 36px;
      height: 36px;
      margin-top: 5px;
      margin-left: 356px;
    }

    .mapboxgl-popup-anchor-bottom > .mapboxgl-popup-tip {
      border-top-color: #ffffff; /* light theme */
      <? if ($darkmode) {echo "border-top-color: #000000; /* dark theme */";} ?>
    }
    .mapboxgl-popup-anchor-top > .mapboxgl-popup-tip {
      border-bottom-color: #ffffff; /* light theme */
      <? if ($darkmode) {echo "border-bottom-color: #000000; /* dark theme */";} ?>
    }
    .mapboxgl-popup-anchor-left > .mapboxgl-popup-tip {
      border-right-color: #ffffff; /* light theme */
      <? if ($darkmode) {echo "border-right-color: #000000; /* dark theme */";} ?>
    }
    .mapboxgl-popup-anchor-right > .mapboxgl-popup-tip {
      border-left-color: #ffffff; /* light theme */
      <? if ($darkmode) {echo "border-left-color: #000000; /* dark theme */";} ?>
    }
    .mapboxgl-popup-close-button {
      display:none;
    }

    .mapboxgl-popup-content {
      padding:40px 40px 25px;
      border-radius:10px 10px 10px 10px;
      width:420px;
    }

    .mapboxgl-popup-content-warning {
      color:#ff514a;
    }

    .mapboxgl-popup-content hr {
      height: 1px ;
      border-width: 1px 0 0 0 ;
      border-top: 1px solid gray ;
    }

    .mapboxgl-popup-content-wrapper {
      padding:1%;
    }

    .mapboxgl-popup-content div {
      padding:10px;
    }

    .onecolumn {
    	height: 70px;
      padding-top: 20px;
    }

    .twocolumns {
    	height: 70px;
    	column-count: 2; column-gap: 8px;
      padding: 0px !important;
    }

    a.popupbutton {
    	box-sizing: border-box;
    	display: inline-block;
    	text-decoration: none;
    	text-align: center;
      text-transform: uppercase;
      font-weight: 900;
      font-size: 18px;
    	padding: 8px;
      padding-top: 21px;
    	border-radius: 10px;
    	width: 100%;
    	height: 60px;
    	background: #d6d6d6; /* light theme */
      <? if ($darkmode) {echo "background: #4a4848; /* dark theme */";} ?>
    	margin: 0px 0;
    	line-height: 1;
    	break-inside: avoid-column;
    	page-break-inside: avoid;
    }

    a.popupbutton:active {
    	background: #a2a2a2;
    }

    a.popupbutton-icon-navigate {
      background-image: url('https://img.icons8.com/ios-glyphs/40/333333/navigation.png'); /* light theme  */
      <? if ($darkmode) {echo "background-image: url('https://img.icons8.com/ios-glyphs/40/ffffff/navigation.png'); /* dark theme */";} ?>
      background-repeat: no-repeat;
      background-position: center;
    }

    a.popupbutton-icon-link {
      background-image: url('https://img.icons8.com/material-outlined/40/333333/globe--v2.png'); /* light theme */
      <? if ($darkmode) {echo "background-image: url('https://img.icons8.com/material-outlined/40/ffffff/globe--v2.png'); /* dark theme */";} ?>
      background-repeat: no-repeat;
      background-position: center;
    }

    a.popupbutton-icon-superCharger {
      background-image: url('https://img.icons8.com/material-sharp/44/333333/tesla-supercharger-pin--v1.png'); /* light theme */
      <? if ($darkmode) {echo "background-image: url('https://img.icons8.com/material-sharp/44/ffffff/tesla-supercharger-pin--v1.png');  /* dark theme */";} ?>
      background-repeat: no-repeat;
      background-position: center;
    }

    a.popupbutton-icon-highwayCharger {
      background-image: url('https://img.icons8.com/small/39/8F8F8F/tesla-supercharger-pin.png'); /* light theme */
      <? if ($darkmode) {echo "background-image: url('https://img.icons8.com/small/39/9c9c9c/tesla-supercharger-pin.png');  /* dark theme */";} ?>
      background-repeat: no-repeat;
      background-position: center;
    }

    a.popupbutton-icon-highwayCharger-active {
      background-image: url('https://img.icons8.com/small/39/5a5a5a/tesla-supercharger-pin.png'); /* light theme */
      <? if ($darkmode) {echo "background-image: url('https://img.icons8.com/small/39/e6e6e6/tesla-supercharger-pin.png');  /* dark theme */";} ?>
      background-repeat: no-repeat;
      background-position: center;
    }

    .info-container,
    .range-container,
    .log-container,
    .route-container {
      visibility: hidden;
      box-shadow: 0 0 10px 2px rgba(0,0,0,.1);
      border-radius:10px 10px 10px 10px;
      margin: 0;
    }

    .info-container {
      position: absolute;
      top: 25px;
      right: 100px;
      z-index: 1;

      opacity: 0.7;
      font-weight: 700;
      display: block;
      padding: 10px 20px;
    }

    .range-container {
      position: absolute;
      bottom: 30px;
      left: 10px;
      z-index: 1;

      opacity: 0.7;
      font-weight: 700;
      display: block;
      padding: 10px 20px;
    }

    .log-container {
      position: absolute;
      top: 75px;
      left: 10px;
      z-index: 2;

      opacity: 0.7;
      font-weight: 700;
      display: block;
      padding: 10px 20px;
    }

    .route-container {
      position: absolute;
      top: 75px;
      left: 10px;
      z-index: 1;

      width: 400px;
      max-height: 800px;
      box-sizing: border-box;
      overflow-y: auto;

      opacity: 0.9;
      display: block;
      padding: 10px 0px;
    }

    .route-container a {
      text-decoration: none;
    }

    img.battery-icon {
      margin-left: 4px;
      margin-right: 4px;
      margin-bottom: -6px;
      margin-top: -4px;
    }

    img.conection-icon {
      margin-left: 4px;
      margin-right: 4px;
      margin-bottom: -6px;
      margin-top: -4px;
    }

  </style>
</head>
<body>
  <div id='map'></div>
  <div id='info' class='info-container'></div>
  <div id='range' class='range-container'></div>
  <div id='log' class='log-container'></div>
  <div id='route' class='route-container'></div>
  <script>
    if (location.protocol !== 'https:') {location.protocol = 'https:'; throw new Error('Changing to secure connection');};
    if (!mapboxgl.supported()) {
      gtag('event', 'No Mapbox GL', {'event_category': 'Connect'});
      alert('Diese Anwendung läuft leider nicht auf MCU1');
      throw new Error('Browser does not support Mapbox GL');
    };

    console.log("App started");

    <? if ($darkmode) {echo "const darkmode = true;";} else {echo "const darkmode = false;";}  ?>
    <? if (isset($_GET["debug"])) {echo "const debugLog = true;";} else {echo "const debugLog = false;";} ?>

    const goingelectricToken = '<? echo $_SERVER["goingelectric"] ?>';
    const compatiblePlugs = 'CCS,Tesla Supercharger,Tesla Supercharger CCS,Typ2,CEE Rot';

    const chargerBigSize = '44';
    const chargerHighwaySize = '39';
    const chargerParkSize = '34';
    const socketChargerSize = '24';
    const chargerFaultSize = '24';
    const destinationSize = '39';
    const batterySize = '25';
    const connectionSize = '25';

    const iconColumnWidth = Number(chargerBigSize)+10;

    var mapStyle = 'mapbox://styles/krillle/ck0my3cjp4nfm1cksdx1rap0q?optimize=true'; // Light Tesla
    const mapStyleSatellite = 'mapbox://styles/mapbox/satellite-v9'; // Satellite
    const chargerTeslaColor = "ff514a";
    var chargerThirdColor = "4b535a"; // dark marker for light map
    var chargerParkColor = "5a5a5a"; // dark marker for light map
    const chargerFaultColor = "ffb800";
    const batteryColor = "8F8F8F";
    const connectionColor = "8F8F8F";

    var routeColor = "4d69ea";

    if (darkmode) {
      console.log('Switching to Dark Mode');
      mapStyle = 'mapbox://styles/krillle/ck1fdx1ok208r1drsdxwqur5f?optimize=true'; // Dark Tesla
      chargerThirdColor = "787878"; // light marker for dark map
      chargerParkColor = "e6e6e6"; // light marker for dark map
      const batteryColor = "9c9c9c";
    };

    const teslaSuperChargerImage = `https://img.icons8.com/material-sharp/${chargerBigSize}/${chargerTeslaColor}/tesla-supercharger-pin--v1.png`;
    const thirdSuperChargerImage = `https://img.icons8.com/material-sharp/${chargerBigSize}/${chargerThirdColor}/tesla-supercharger-pin--v1.png`;
    const highwayChargerImage = `https://img.icons8.com/small/${chargerHighwaySize}/${chargerParkColor}/tesla-supercharger-pin.png`;
    const parkChargerImage = `https://img.icons8.com/ios-glyphs/${chargerParkSize}/${chargerParkColor}/park-and-charge.png`;
    const socketChargerImage = `https://img.icons8.com/material-outlined/${chargerParkSize}/${chargerParkColor}/wall-socket.png`;
    const faultReportImage = `https://img.icons8.com/ios-glyphs/${chargerFaultSize}/${chargerFaultColor}/error.png`;
    const destinationImage = `https://img.icons8.com/small/${destinationSize}/${routeColor}/order-delivered.png`;
    const waitImage = `https://img.icons8.com/ios-glyphs/${chargerParkSize}/${chargerParkColor}/hourglass.png`;

    const offlineImage = `https://img.icons8.com/ios-glyphs/${connectionSize}/${connectionColor}/wifi-off.png`
    const onlineImage = `https://img.icons8.com/ios-glyphs/${connectionSize}/${connectionColor}/wifi.png`

    const batteryImageSet = [
      `https://img.icons8.com/ios-glyphs/${batterySize}/${batteryColor}/no-battery.png`,
      `https://img.icons8.com/ios-glyphs/${batterySize}/${batteryColor}/empty-battery.png`,
      `https://img.icons8.com/ios-glyphs/${batterySize}/${batteryColor}/low-battery.png`,
      `https://img.icons8.com/ios-glyphs/${batterySize}/${batteryColor}/medium-battery.png`,
      `https://img.icons8.com/ios-glyphs/${batterySize}/${batteryColor}/high-battery.png`,
      `https://img.icons8.com/ios-glyphs/${batterySize}/${batteryColor}/full-battery.png`
    ];
    var fullBatteryRange = 350;

    const superCharger = {'minPower':'100', 'minZoom':null, 'toggle':2}
    const highwayCharger = {'minPower':'50', 'minZoom':11, 'toggle':2}
    const destinationCharger = {'minPower':'3', 'minZoom':14, 'toggle':1}

    var minPower = superCharger.minPower;
    var minPowerList = superCharger.minPower;

    const slowSpeed = 30;
    const highSpeed = 100;
    const slowSpeedZoom = '16';
    const highSpeedZoom = '9';

    const maxChargerDistance = 6000; // max senkrechter Abstand Charger von Route in m

    const connectionState = [ // Used for debug message
      'UNSENT',
      'OPENED',
      'HEADERS RECEIVED',
      'LOADING',
      'DONE'
    ];

    var teslaConnection = {'accessToken': getCookie('access'),'refreshToken': getCookie('refresh'), 'vehicle': getCookie('vehicle'),'connected' : false ,'status': 'undefined' };
    // var teslaPosition = JSON.parse(decodeURIComponent(getCookie('location'))) || {'longitude' : 10.416667, 'latitude' : 51.133333, 'heading': 0, 'speed' : 100, 'zoom': 9, 'range': false};
    var teslaPosition = JSON.parse(decodeURIComponent(getCookie('location'))) || {'longitude' : 13.48, 'latitude' : 52.49, 'heading': 0, 'speed' : 100, 'zoom': 9, 'range': 350};

    var currentDestination = JSON.parse(decodeURIComponent(getCookie('destination')));
    var currentRoute = false;

    const updatePositionTime = 10000;
    const updateListTime = 120000;
    const updateListDistance = 1000;
    var updateListInterval;
    var updateListPosition = teslaPosition;

    var zoomToogle = [
      {name:'AutoZoom', zoom:null, autoZoom:true, autoFollow:true, headUp:true, icon:'url("https://img.icons8.com/small/40/333333/gps-device.png"'},
      {name:'DestinationCharger', zoom:slowSpeedZoom, autoZoom:false, autoFollow:true, headUp:false, icon:'url("https://img.icons8.com/ios-glyphs/40/333333/park-and-charge.png"'},
      {name:'SuperCharger', zoom:highSpeedZoom, autoZoom:false, autoFollow:true, headUp:false, icon:'url("https://img.icons8.com/material-sharp/40/333333/tesla-supercharger-pin--v1.png"'}
    ];
    var zoomToggleState = 0;

    const positionSize = '44';
    var positionColor = 'ff514a';

    var autoZoom = true;
    var autoFollow = true;
    var headUp = true;
    const m = (highSpeedZoom - slowSpeedZoom) / (highSpeed - slowSpeed);
    const b = slowSpeedZoom - m * slowSpeed;

    if (location.hash) {
      autoZoom = false;
      autoFollow = false;
      headUp = false;

      decodeHash(location.hash);

    };

    var infoContainer = document.getElementById('info');
    var rangeContainer = document.getElementById('range');
    var logContainer = document.getElementById('log');
    var routeContainer = document.getElementById('route');

    var positionIcon = {
      type: 'Feature',
      properties: {'bearing': teslaPosition.heading},
      geometry: {
        type: 'Point',
        coordinates: [teslaPosition.longitude,teslaPosition.latitude]
      }
    };

    if (debugLog) {logMessage('Debug started')};

    mapboxgl.accessToken = '<? echo $_SERVER["mapbox"] ?>';
    var map = new mapboxgl.Map({
      container: 'map', // container id
      style: mapStyle,
      center: [teslaPosition.longitude,teslaPosition.latitude], // starting position
      zoom: teslaPosition.zoom, // starting zoom
      bearing: teslaPosition.heading,
      attributionControl: false
    });

    // Add geocoder search field
    var geocoderControl = new MapboxGeocoder({
      accessToken: mapboxgl.accessToken,
      mapboxgl: mapboxgl,
      trackProximity: true
    })
    geocoderControl.on('result', function(destination) {
      cancelRouteChargerList(); // Stop previous list update in case one is running

      console.log('Destination:', destination.result.text);
      currentDestination = {
        'center': destination.result.center,
        'name': destination.result.place_name,
        'text': destination.result.text
      };
      document.cookie = 'destination=' + encodeURIComponent(JSON.stringify(currentDestination)) + '; expires=Thu, 10 Aug 2022 12:00:00 UTC";';

      gtag('event', 'Route Chargers', {'event_category': 'Destination', 'event_label': `${currentDestination.text}`});
      updateRouteChargerList(true);
      console.log ('Starting continuous list update');
      updateListInterval = setInterval(updateRouteChargerList, updateListTime);

    });
    map.addControl(geocoderControl,'top-left');

    // Add zoom and rotation controls to the map.
    var comp = new mapboxgl.NavigationControl({
      showCompass: true,
      showZoom: false,
      visualizePitch: true
    });
    comp._compass.addEventListener('click', () => {infoMessage('Norden oben'); stopHeadUp()});
    map.addControl(comp);

    var zoom = new mapboxgl.NavigationControl({
      showCompass: false,
      showZoom: true,
      visualizePitch: false
    });
    zoom._zoomInButton.addEventListener('click', () => stopAutoZoom());
    zoom._zoomOutButton.addEventListener('click', () => stopAutoZoom());
    map.addControl(zoom);

    var nav = new mapboxgl.NavigationControl({
      showCompass: false,
      showZoom: false,
      visualizePitch: false
    })
    nav._toggle = nav._createButton('mapboxgl-ctrl-icon mapboxgl-ctrl-autozoom', 'Toggle Autozoom', () => toggleAutoZoom());
    const el = window.document.createElement('span');
    el.className = 'mapboxgl-ctrl-autozoom-icon';
    nav._icon = nav._toggle.appendChild(el);
    map.addControl(nav, 'bottom-right');

    zoomToPower(teslaPosition.zoom);

    // Add geolocate control to the map.
    const geolocate = new mapboxgl.GeolocateControl({
      positionOptions: {
        enableHighAccuracy: true
      },
      fitBoundsOptions: {
        maxZoom: 9
      }
    })
    map.addControl(geolocate, 'bottom-right');
    // geolocate.trigger();
    // map.setPitch(30);


    map.on('load', function() {
      // Prepare empty Route Layer
      map.addSource('route', {
        'type': 'geojson',
        'data': {
          "type": "FeatureCollection",
          "features": []
        }
      });
      map.addLayer({
        'id': 'route',
        'type': 'line',
        'source': 'route',
        'layout': {
          'line-join': 'round',
          'line-cap': 'round'
        },
        'paint': {
          'line-color': '#'+routeColor,
          'line-width': 6
        }
      });

      // Prepare empty Distant Box Layer
      map.addSource('distantBox', {
        'type': 'geojson',
        'data': {
          "type": "FeatureCollection",
          "features": []
        }
      });
      map.addLayer({
        'id': 'distantBox',
        'type': 'fill',
        'source': 'distantBox',
        'layout': {
        },
        'paint': {
          'fill-color': '#088',
          'fill-opacity': 0.2
        }
      });

      // Create Tesla Supercharger Image
      map.loadImage(teslaSuperChargerImage, function(error, image) {
        if (error) throw error;
        map.addImage('teslaSuperCharger', image);
      });

      // Create Third Party Supercharger Image
      map.loadImage(thirdSuperChargerImage, function(error, image) {
        if (error) throw error;
        map.addImage('thirdSuperCharger', image);
      });

      // Create DC Highway Charger Image
      map.loadImage(highwayChargerImage, function(error, image) {
        if (error) throw error;
        map.addImage('highwayCharger', image);
      });

      // Create Park Charger Image
      map.loadImage(parkChargerImage, function(error, image) {
        if (error) throw error;
        map.addImage('parkCharger', image);
      });

      // Create Socket Charger Image
      map.loadImage(socketChargerImage, function(error, image) {
        if (error) throw error;
        map.addImage('socketCharger', image);
      });

      // Create Fault Report Image
      map.loadImage(faultReportImage, function(error, image) {
        if (error) throw error;
        map.addImage('faultReport', image);
      });

      // Prepare empty Charger Layer
      map.addSource('chargers', {
        "type": "geojson",
        "data": {
          "type": "FeatureCollection",
          "features": []
        }
      });
      map.addLayer({
        "id": "chargers",
        "type": "symbol",
        "source": "chargers",
        "layout": {
           "icon-image": "{icon}",
           "icon-anchor": "bottom"

           // "icon-allow-overlap": true
          // "icon-size": 0.25

          // "text-field": ["get", "status"],
          // "text-variable-anchor": ["top"],
          // "text-radial-offset": 1.5,
          // "text-justify": "auto",
        }
      });

      rangeDisplay(`<img class="connection-icon" src="${offlineImage}">`)
      console.log('Establishing Connection to Tesla');
      connectTesla ();

      console.log("Initalizing Chargers");
      updateChargers();

      // if (currentDestination) {
      //   gtag('event', 'Route Chargers Recover', {'event_category': 'Destination', 'event_label': `${currentDestination.text}`});
      //   updateRouteChargerList(true);
      //   console.log ('Recovering continuous list update');
      //   updateListInterval = setInterval(updateRouteChargerList, updateListTime);
      // };

    });

    // Events to disable AutoZoom
    map.on('touchstart', function() {
    });

    map.on('mousedown', function() {
    });

    map.on('dragstart', function() {
      stopHeadUp();
      stopAutoZoom();
      console.log("AutoFollow stopped");
      autoFollow = false;
    });

    map.on('rotateend', function() {
      console.log("Rotate End");
    });

    // Events to update chargers
    map.on('moveend', function() {
      console.log("Move End: Invoke Update");
      updateChargers();
      location.hash = encodeHash();
    });

    map.on('zoomend', function() {
      console.log("Zoom End: Set Charger for zoom level " + map.getZoom());
      zoomToPower(map.getZoom());
    });

    // map.on('idle', function() {
    //   console.log('Map idle');
    //   infoMessage('Map idle');
    // });

    // Charger places events (Popup)
    map.on('click', 'chargers', function (e) {
      stopAutoZoom();
      stopHeadUp();
      console.log("AutoFollow stopped");
      autoFollow = false;
      gtag('event', 'Charger Details', {'event_category': 'Charger', 'event_label': `${e.features[0].properties.name} ${e.features[0].properties.city}`});

      map.flyTo({ 'center': e.features[0].geometry.coordinates});

      var chargerID = e.features[0].id

      var popup = new mapboxgl.Popup({ offset: 25, anchor: 'bottom' })
      // map.once('idle', function(e) {
      //   console.log('Map idle',chargerID);
      //   popup.setHTML(chargerDescription(chargerID).text)
      // });
      // var coordinates = e.features[0].geometry.coordinates.slice();
      // popup.setLngLat(coordinates)
      popup.setLngLat(e.features[0].geometry.coordinates)
      .setHTML(chargerShortDescription(e.features[0].id, e.features[0].properties).text)
      .once('open',function () {
        addChargerDetails(e.features[0].id);
        addChargerDistance(e.features[0].id, e.features[0].geometry.coordinates);
      })
      .addTo(map);
    });

    // Change the cursor to a pointer when the mouse is over the places layer
    map.on('mouseenter', 'chargers', function () {
      map.getCanvas().style.cursor = 'pointer';
    });

    // Change it back to a pointer when it leaves
    map.on('mouseleave', 'chargers', function () {
      map.getCanvas().style.cursor = '';
    });

    function createPositionImage() {
      // Create Position Image
      map.addSource('positionIcon', { 'type': 'geojson', 'data': positionIcon });

      map.loadImage(`https://img.icons8.com/small/${positionSize}/${positionColor}/gps-device.png`, function(error, image) {
        if (error) throw error;
        map.addImage('position', image);

        /* Style layer: A style layer ties together the source and image and specifies how they are displayed on the map. */
        map.addLayer({
          id: "position",
          type: "symbol",
          source: 'positionIcon',
          layout: {
            "icon-image": "position",
            "icon-rotate": ["get", "bearing"],
            "icon-rotation-alignment": "map",
            "icon-allow-overlap": true,
            "icon-ignore-placement": true
          }
        });
      });
    };

    function toggleAutoZoom() {
      zoomToggleState = zoomToggleState < 2 ? ++zoomToggleState : 0;

      console.log(`${zoomToogle[zoomToggleState].name}: Zoom ${zoomToogle[zoomToggleState].zoom}, AutoZoom ${zoomToogle[zoomToggleState].autoZoom}, AutoFollow ${zoomToogle[zoomToggleState].autoFollow}, HeadUp ${zoomToogle[zoomToggleState].headUp} `);

      updateZoomIcon();
      autoZoom = zoomToogle[zoomToggleState].autoZoom;
      autoFollow = zoomToogle[zoomToggleState].autoFollow;
      headUp = zoomToogle[zoomToggleState].headUp;

      if (zoomToogle[zoomToggleState].zoom) {
        map.jumpTo({ 'zoom': zoomToogle[zoomToggleState].zoom });
      } else {
        infoMessage('Autozoom aktiviert')
        updateMapFocus();
      }
    };

    function stopAutoZoom() {
      if (autoZoom) {
        console.log('AutoZoom stopped');
        infoMessage('Autozoom deaktiviert');
        autoZoom = false;
        updateZoomIcon();
      };
    };

    function stopHeadUp() {
      console.log("HeadUp stopped");
      headUp = false;
    };

    function updateZoomIcon() {
      nav._icon.style['background-image'] = zoomToogle[zoomToggleState].icon;
    };

    function updateMapFocus() {
      var jumpTarget = {};
      if (autoFollow) {jumpTarget.center = [teslaPosition.longitude,teslaPosition.latitude]};
      if (autoZoom) {jumpTarget.zoom = teslaPosition.zoom};
      if (headUp) {jumpTarget.bearing = teslaPosition.heading};
      if (autoFollow || autoZoom || headUp) {
        map.jumpTo(jumpTarget);
      };
    };

    function flyToCharger(lon,lat,name,city){
        stopAutoZoom();
        stopHeadUp();
        console.log("AutoFollow stopped");
        autoFollow = false;
        gtag('event', 'Click in List', {'event_category': 'Charger', 'event_label': `${name} ${city}`});

        map.flyTo({ 'center': [lon,lat]});
    };

    function zoomToPower(zoom) {
      if (zoom >= destinationCharger.minZoom) {
        minPower = destinationCharger.minPower;
        zoomToggleState = destinationCharger.toggle;
      } else if (zoom >= highwayCharger.minZoom) {
        minPower = highwayCharger.minPower;
        zoomToggleState = highwayCharger.toggle;
      } else {
        minPower = superCharger.minPower;
        zoomToggleState = superCharger.toggle;
      }
      if (!autoZoom) {nav._icon.style['background-image'] = zoomToogle[zoomToggleState].icon};
    };

    function batteryImage(range) {
      if (range > fullBatteryRange) {return batteryImageSet[5]}
      else if (range < 1) {return batteryImageSet[0]}
      else {return batteryImageSet[Math.round(range/fullBatteryRange * 4)+1]};
    };

    function milesToKm(miles) {
        var km = parseFloat(miles) * 1.61;
        return {
          'km': km.toFixed().toString().replace(".",",")  + ' km',
          'kmRaw': km
        }
    };

    function secondsToTime(totalSeconds) {
      var hours = Math.floor(totalSeconds / 3600);
      totalSeconds %= 3600;
      var minutes = Math.floor(totalSeconds / 60);
      // return ((hours > 0) ? hours + '   Std ' : '') + minutes + ' Min';
      return (hours + ':') + (minutes > 9 ? minutes : '0' + minutes) + ' h';
    };

    function httpGet(url, token, f) {
      var httpReq = new XMLHttpRequest();
      httpReq.open('GET', url, f ? true : false);
      if (token) {httpReq.setRequestHeader('authorization','bearer ' + teslaConnection.accessToken)};
      if (f) {httpReq.addEventListener("readystatechange", f)}
      httpReq.send(null);
      if (f) { return false}
      else { return httpReq.responseText };
    };

    function getCookie(name) {
      var value = "; " + document.cookie;
      var parts = value.split("; " + name + "=");
      if (parts.length == 2) {
        return parts.pop().split(";").shift()
      } else {
        return false
      };
    };

    function encodeHash() {
      var center = map.getCenter();
      return center.lat + ',' + center.lng + ',' + map.getZoom() + ',' + map.getBearing();
    };

    function decodeHash(hash) {
      var payload = hash.substring(1).split(',');

      console.log('Hash:' + ' latitude ' + payload[0]+ ', longitude ' + payload[1]+ ', zoom ' + payload[2] +', heading '+ payload[3]);

      if (payload[0]) {teslaPosition.latitude =  Number(payload[0]);console.log('longitude'+ Number(payload[0]));};
      if (payload[1]) {teslaPosition.longitude =  Number(payload[1]);console.log('latitude'+ Number(payload[1]));};
      if (payload[2]) {teslaPosition.zoom =  Number(payload[2]);console.log('zoom'+ Number(payload[2]));};
      if (payload[3]) {teslaPosition.heading =  Number(payload[3]);console.log('heading' + Number(payload[3]));};

    };

    function settingsPopup () {
      // var popup = new mapboxgl.Popup({closeOnClick: false})
      // .setLngLat(map.getCenter())
      // .setHTML(settingsContent())
      // .addTo(map);
      //
      // document.getElementById('connect').addEventListener('click', createTeslaToken(document.getElementById("email").value, document.getElementById("password").value));
      //
      // document.querySelector('status').textContent = teslaConnection.status;

      var email = prompt('Verbindungsstatus: ' + teslaConnection.status + '\rBitte Tesla-Account E-Mail eingeben');
      if (email == null) {return};
      var password = prompt("Bitte Passwort für diesen Tesla-Account eingeben");
      if (password == null) {return};

      createTeslaToken(email, password);
    };

    function infoMessage(message) {
      if (infoContainer.innerHTML) {infoContainer.innerHTML = null;};
      // var pre = document.createElement('pre');
      // pre.textContent = message;
      // infoContainer.appendChild(pre);
      infoContainer.innerHTML = message;
      infoContainer.style.visibility = 'visible';
      setTimeout(function(){ infoContainer.style.visibility = 'hidden';  infoContainer.innerHTML = null; }, 3000);
    };

    function rangeDisplay(message) {
      rangeContainer.innerHTML = `<a class="" href="#" onclick="settingsPopup(); return false;">${message}</a>;`
      rangeContainer.style.visibility = 'visible';
    };

    function logMessage(message) {
      logContainer.innerHTML += message + '<br>';
      logContainer.style.visibility = 'visible';
    };

    function routeList(message) {
      if (routeContainer.innerHTML) {routeContainer.innerHTML = '';};
      routeContainer.innerHTML = message;
      routeContainer.style.visibility = 'visible';
    };

    function hideRouteList() {
      routeContainer.innerHTML = '';
      routeContainer.style.visibility = 'hidden';
    };

    // Tesla connection - - - - - -

    function connectTesla () {
      if (!teslaConnection.accessToken) {
        teslaConnection.status = 'Kein Token';
        console.log(teslaConnection.status);
        infoMessage(teslaConnection.status);
        gtag('event', 'No Token', {'event_category': 'Connect'});
        // settingsPopup ();   // Connection is done after popup by recursive call in getTeslaVehicles callback
      } else {
        updatePosition(true);
      };
    };

    function setTeslaPosition(vehicleData) {
      var zoom = ((vehicleData.drive_state.speed) ? vehicleData.drive_state.speed : 0) * m + b;
      zoom = (zoom > slowSpeedZoom) ? slowSpeedZoom : (zoom < highSpeedZoom) ? highSpeedZoom : zoom;

      teslaPosition = {
        'longitude': vehicleData.drive_state.longitude,
        'latitude': vehicleData.drive_state.latitude,
        'heading': vehicleData.drive_state.heading,
        'speed': (vehicleData.drive_state.speed) ? vehicleData.drive_state.speed : 0,
        'zoom': zoom,
        'range': milesToKm(vehicleData.charge_state.est_battery_range).kmRaw
      };
      document.cookie = 'location=' + encodeURIComponent(JSON.stringify(teslaPosition)) + '; expires=Thu, 10 Aug 2022 12:00:00 UTC";';
    };

    function updatePosition(initial) {
      getTeslaCarData(function () {
        if (debugLog) {logMessage('Verbindungsstatus: ' + connectionState[this.readyState])};
        if (this.readyState === 4) {
          if (debugLog) {logMessage('Response:' + this.responseText)};
          var vehicleData = JSON.parse(this.responseText);

          if (initial) {
            if (debugLog) {logMessage('Initial: Checking Vehicle Data')};
            console.log('Vehicle Data:',vehicleData);
            if (vehicleData == null) {
              teslaConnection.status = 'Ungültiges Token';
              console.log(teslaConnection.status);
              infoMessage(teslaConnection.status);
              gtag('event', 'Invalid Token', {'event_category': 'Connect'});
              settingsPopup ();
              return;

            } else if (vehicleData.response == null) {
              // Car sleeps
              teslaConnection.status = 'Fahrzeug nicht erreichbar';
              console.log(teslaConnection.status);
              infoMessage(teslaConnection.status);
              if (debugLog) {logMessage(teslaConnection.status)};
              gtag('event', 'Not reachable', {'event_category': 'Connect'});
              return;
            }
            else {
              rangeDisplay(`<img class="connection-icon" src="${onlineImage}">`)
              teslaConnection.status = 'Verbunden mit ' + vehicleData.response.vehicle_state.vehicle_name;
              teslaConnection.connected = true;
              console.log(teslaConnection.status);
              infoMessage(teslaConnection.status);
              if (debugLog) {logMessage(teslaConnection.status)};
              gtag('event', 'Connected', {'event_category': 'Connect', 'event_label': vehicleData.response.vehicle_state.vehicle_name});
              console.log ('Starting continuous position update');
              createPositionImage();
              setInterval(updatePosition, updatePositionTime);
            };
          };

          setTeslaPosition(vehicleData.response);
          rangeDisplay(`<img class="battery-icon" src="${batteryImage(teslaPosition.range)}">${teslaPosition.range.toFixed(0).toString()} km`);
          if (positionIcon.geometry.coordinates[0] != teslaPosition.longitude ||
              positionIcon.geometry.coordinates[1] != teslaPosition.latitude ||
              positionIcon.properties.bearing != teslaPosition.heading) {

            positionIcon.geometry.coordinates = [teslaPosition.longitude,teslaPosition.latitude];
            positionIcon.properties.bearing = teslaPosition.heading;

            map.getSource('positionIcon').setData(positionIcon);
            updateMapFocus ();

            if (currentDestination && lineDistance([[teslaPosition.longitude,teslaPosition.latitude],currentDestination.center]) < 250) {cancelRouteChargerList()};
          };

          if (initial && currentDestination) {
            gtag('event', 'Route Chargers Recover', {'event_category': 'Destination', 'event_label': `${currentDestination.text}`});
            updateRouteChargerList(true);
            console.log ('Recovering continuous list update');
            updateListInterval = setInterval(updateRouteChargerList, updateListTime);
          };

        }
      })
    };

    // - - - - - - - - Tesla requests - - - - - - - - -

    // function getTeslaChargeStatus() {
    //   var teslaUrl = 'https://goingtesla.herokuapp.com/corsproxy.php?'
    //       + 'csurl=https://owner-api.teslamotors.com/api/1/vehicles/' + teslaConnection.vehicle + '/data_request/charge_state';
    //
    //   return JSON.parse(httpGet(teslaUrl,true));
    // };
    //
    // function getTeslaDriveStatus() {
    //   var teslaUrl = 'https://goingtesla.herokuapp.com/corsproxy.php?'
    //       + 'csurl=https://owner-api.teslamotors.com/api/1/vehicles/' + teslaConnection.vehicle + '/data_request/drive_state';
    //
    //   return JSON.parse(httpGet(teslaUrl,true));
    // };

    function getTeslaCarData(f) {
      var teslaUrl = 'https://goingtesla.herokuapp.com/corsproxy.php?'
          + 'csurl=https://owner-api.teslamotors.com/api/1/vehicles/' + teslaConnection.vehicle + '/vehicle_data';

      httpGet(teslaUrl,true,f);
    };

    function getTeslaVehicles(f) {
      var teslaUrl = 'https://goingtesla.herokuapp.com/corsproxy.php?'
          + 'csurl=https://owner-api.teslamotors.com/api/1/vehicles';

      httpGet(teslaUrl,true,f);
    };

    function createTeslaToken (email, password) {
      var teslaUrl = 'https://goingtesla.herokuapp.com/corsproxy.php?'
      + 'csurl=https://owner-api.teslamotors.com//oauth/token?grant_type=password';

      var body = JSON.stringify({
        "grant_type": "password",
        "client_id": "81527cff06843c8634fdc09e8ac0abefb46ac849f38fe1e431c2ef2106796384",
        "client_secret": "c7257eb71a564034f9419ee651c7d0e5f7aa6bfbd18bafb5c5c033b093bb2fa3",
        "email": email,
        "password": password
      });

      var xhr = new XMLHttpRequest();
      xhr.withCredentials = true;

      xhr.addEventListener("readystatechange", function () {
        if (this.readyState === 4) {
          console.log("GetToken Listener Result: " + this.responseText);
          var result = JSON.parse(this.responseText);
          teslaConnection.accessToken = result.access_token;
          teslaConnection.refreshToken = result.refresh_token;
          // result.expires_in
          // result.created_at

          document.cookie = 'access=' + teslaConnection.accessToken + '; expires=Thu, 10 Aug 2022 12:00:00 UTC";';
          document.cookie = 'refresh=' + teslaConnection.refreshToken + '; expires=Thu, 10 Aug 2022 12:00:00 UTC";';

          console.log("Access: " + teslaConnection.accessToken);
          console.log("Refresh: " + teslaConnection.refreshToken);

          getTeslaVehicles (function () {
            if (this.readyState === 4) {
              var result = JSON.parse(this.responseText);
              teslaConnection.vehicle = result.response[0].id_s;

              document.cookie = 'vehicle=' + teslaConnection.vehicle + '; expires=Thu, 10 Aug 2022 12:00:00 UTC";';
              console.log("Vehicle: " + teslaConnection.vehicle);

              connectTesla();
            }
          });
        }
      });

      xhr.open("POST", teslaUrl);
      xhr.setRequestHeader("Content-Type", "application/json");
      xhr.setRequestHeader("cache-control", "no-cache");

      xhr.send(body);
    };

    function sendDestinationToTesla(destination) {
      console.log('Set destination: ' + destination);
      var teslaUrl = 'https://goingtesla.herokuapp.com/corsproxy.php?'
      + 'csurl=https://owner-api.teslamotors.com/api/1/vehicles/' + teslaConnection.vehicle + '/command/share';

      var body = JSON.stringify({
        "type": "share_ext_content_raw",
        "value": {
          "android.intent.extra.TEXT": destination
        },
        "locale": "en-US",
        "timestamp_ms": Date.now()
      });

      var xhr = new XMLHttpRequest();
      xhr.withCredentials = true;

      xhr.addEventListener("readystatechange", function () {
        if (this.readyState === 4) {
          console.log('Sent destination: ' + this.responseText);
        }
      });

      xhr.open("POST", teslaUrl);

      xhr.setRequestHeader("Content-Type", "application/json");
      xhr.setRequestHeader("Authorization", 'bearer ' + teslaConnection.accessToken);
      xhr.setRequestHeader("cache-control", "no-cache");

      xhr.send(body);

    };

    // - - - - - GEO operations - - - - - -
    function bearingPoint(startPoint, bearing, distance) {
      // 	φ is latitude, λ is longitude, brng is bearing (clockwise from north), d being the distance travelled, R the earth’s radius
      var λ1 = startPoint[0] * (Math.PI/180);
      var φ1 = startPoint[1] * (Math.PI/180);
      var brng = bearing * (Math.PI/180);
      var d = distance;
      const R = 6371e3;

      var φ2 =Math.asin( Math.sin(φ1)*Math.cos(d/R) + Math.cos(φ1)*Math.sin(d/R)*Math.cos(brng) );
      var λ2 = λ1 + Math.atan2(Math.sin(brng)*Math.sin(d/R)*Math.cos(φ1), Math.cos(d/R)-Math.sin(φ1)*Math.sin(φ2));

      return [λ2 * 180 / Math.PI, φ2 * 180 / Math.PI];
    };

    function lineBearing(line) {
      // 	φ is latitude, λ is longitude, brng is bearing (clockwise from north), d being the distance travelled, R the earth’s radius
      var λ1 = line[0][0] * (Math.PI/180);
      var φ1 = line[0][1] * (Math.PI/180);
      var λ2 = line[1][0] * (Math.PI/180);
      var φ2 = line[1][1] * (Math.PI/180);

      var y = Math.sin(λ2-λ1) * Math.cos(φ2);
      var x = Math.cos(φ1)*Math.sin(φ2) - Math.sin(φ1)*Math.cos(φ2)*Math.cos(λ2-λ1);
      return Math.atan2(y, x) * 180 / Math.PI;
    };

    function lineDistance(line) {
      var R = 6371e3;
      var φ1 = line[0][1] * (Math.PI/180);
      var φ2 = line[1][1] * (Math.PI/180);
      var Δφ = (line[1][1]-line[0][1]) * (Math.PI/180);
      var Δλ = (line[1][0]-line[0][0]) * (Math.PI/180);

      var a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
      Math.cos(φ1) * Math.cos(φ2) *
      Math.sin(Δλ/2) * Math.sin(Δλ/2);
      var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

      return R * c;
    };

    // function pointDistance(line, point) {
    //   // δ13 is (angular) distance from start point to third point
    //   // θ13 is (initial) bearing from start point to third point
    //   // θ12 is (initial) bearing from start point to end point
    //   // R is the earth’s radius
    //   var δ13 = lineDistance([line[0],point]);
    //   var θ12 = lineBearing(line) * (Math.PI/180);
    //   var θ13 = lineBearing([line[0],point]) * (Math.PI/180);
    //   const R = 6371e3;
    //
    //   console.log('Point Distance',line,point,'=',Math.asin(Math.sin(δ13/R)*Math.sin(θ13-θ12)) * R);
    //
    //   return Math.asin(Math.sin(δ13/R)*Math.sin(θ13-θ12)) * R;
    // };

    function distantLineBox(line, distance) {
      var distance = Math.sqrt(2*distance*distance);
      var bearing = lineBearing(line);
      var corners = [[+135,-135],[-45,+45]];
      var box = [];

      corners.forEach( (vectors, i) => {
        vectors.forEach( (vector, j) => {
          box.push(bearingPoint(line[i], bearing + vector, distance));
        });
      });
      return box;
    };

    function boundingBox(lineBox){
      var SW = [90,180];
      var NE = [0,0];
      lineBox.forEach( corner => {
        if (corner[0] < SW[0]) {SW[0] = corner[0]};
        if (corner[1] < SW[1]) {SW[1] = corner[1]};
        if (corner[0] > NE[0]) {NE[0] = corner[0]};
        if (corner[1] > NE[1]) {NE[1] = corner[1]};
      });
      return([SW,NE]);
    };

    function pointIsInBox(point, lineBox) {
      return geolib.isPointInPolygon({ latitude: point[1], longitude: point[0] }, [
        { latitude: lineBox[0][1], longitude: lineBox[0][0] },
        { latitude: lineBox[1][1], longitude: lineBox[1][0] },
        { latitude: lineBox[2][1], longitude: lineBox[2][0] },
        { latitude: lineBox[3][1], longitude: lineBox[3][0] }
      ]);
    };

    function decodePolyline(polyline_str) {
      var index = 0;
      var lat = 0;
      var lng  = 0;
      var coordinates = [];
      var changes = {'latitude': 0, 'longitude': 0};

      // Coordinates have variable length when encoded, so just keep
      // track of whether we've hit the end of the string. In each
      // while loop iteration, a single coordinate is decoded.
      while (index < polyline_str.length) {
          // Gather lat/lon changes, store them in a dictionary to apply them later
          ['latitude', 'longitude'].forEach( unit => {
              var shift = 0, result = 0, byte;

              while (true) {
                  byte = polyline_str.charCodeAt(index) - 63;
                  index+=1;
                  result = result | ((byte & 0x1f) << shift);
                  shift += 5;
                  if (!(byte >= 0x20)) {
                      break;
                  };
              };
              if (result & 1) {
                  changes[unit] = ~(result >> 1);
              } else {
                  changes[unit] = (result >> 1);
              };
          });
          lat += changes['latitude'];
          lng += changes['longitude'];

          coordinates.push([lng / 100000.0, lat / 100000.0]);
      }
      return coordinates;
    };

    function showRoute(coordinates) {
      map.getSource('route').setData(
        {
          'type': 'Feature',
          'properties': {},
          'geometry': {
            'type': 'LineString',
            'coordinates': coordinates
          }
        }
      );
    };

    function hideRoute() {
      map.getSource('route').setData(
        {
          "type": "FeatureCollection",
          "features": []
        }
      )
    };

    function showBoxes() {
      var newList = {
          "type": "FeatureCollection",
          "features": []
      };
      var lineBox;

      currentRoute.coordinates.forEach( (point, i) => {
          if (i < currentRoute.coordinates.length-1) {
            // Bounding Boxes
            // box = boundingBox(distantLineBox([coordinates[i],coordinates[i+1]],maxChargerDistance));
            // lineBox = [box[0], [box[0][0],box[1][1]], box[1], [box[1][0],box[0][1]] ,box[0]];

            // Boxes aloung Route
            lineBox = distantLineBox([currentRoute.coordinates[i],currentRoute.coordinates[i+1]],maxChargerDistance);
            lineBox.push(lineBox[0]); // close Polygon

            // console.log(lineBox);
            newList.features.push({
              "id": i.toString(),
              "type": "Feature",
              "properties": {},
              "geometry": {
                "type": "Polygon",
                "coordinates": [lineBox]
              }
            });
          };
      });
      map.getSource('distantBox').setData(newList);
    };

    function showBigBox() {
      var newList = {
          "type": "FeatureCollection",
          "features": []
      };
      var bigBox, showBox;

      bigBox = boundingBox(distantLineBox(boundingBox(currentRoute.coordinates),maxChargerDistance));
      showBox = [[bigBox[0][0],bigBox[0][1]],
                 [bigBox[1][0],bigBox[0][1]],
                 [bigBox[1][0],bigBox[1][1]],
                 [bigBox[0][0],bigBox[1][1]]];
      showBox.push(showBox[0]); // close Polygon
      newList.features.push({
        "id": "0",
        "type": "Feature",
        "properties": {},
        "geometry": {
          "type": "Polygon",
          "coordinates": [showBox]
        }
      });

      map.getSource('distantBox').setData(newList);
    };

    // - - - - - mapBox requests - - - - - -
    function getRoute(start,destination,route,f){  // set route = true if we need route coordinates
       var routeUrl = 'https://api.mapbox.com/directions/v5/mapbox/driving/'
          + start.longitude + ',' + start.latitude + ';'
          + destination.longitude + ',' + destination.latitude
          + '?access_token=' + mapboxgl.accessToken
          + (route ? '&geometries=polyline&overview='+route : '&overview=false');
      result = httpGet(routeUrl,false,f);
      if (result) {
        result = JSON.parse(result);
        if (result.code == "Ok") {
          return {
            'distanceRaw': result.routes[0].distance/1000,
            'distance': (result.routes[0].distance/1000).toFixed((result.routes[0].distance < 10000) ? 1 : 0).toString().replace(".",",")  + ' km',
            'durationRaw': result.routes[0].duration,
            'duration': secondsToTime(result.routes[0].duration),
            'rangeRaw' : teslaPosition.range ? teslaPosition.range - result.routes[0].distance/1000 : false,
            'range' : teslaPosition.range ? (teslaPosition.range - result.routes[0].distance/1000).toFixed(0).toString() + ' km' : false,
            'coordinates': route ? decodePolyline(result.routes[0].geometry) : false
          }
        } else {
          return null
        }
      } else {
        return null;
      };
    };

    // - - - - - - - - GoingElectric requests - - - - - - - -
    function getChargersInBoundingBox(boundingBox, minPower, f) {
      var geUrl = 'https://api.goingelectric.de/chargepoints/?'+
        `key=${goingelectricToken}&`+
        `plugs=${compatiblePlugs}&min_power=${minPower}&`+
        `sw_lat=${boundingBox[0][1]}&sw_lng=${boundingBox[0][0]}&`+
        `ne_lat=${boundingBox[1][1]}&ne_lng=${boundingBox[1][0]}`;
      httpGet(geUrl,true,f);
    };

    function getChargersInBounds(searchField, f) {
      var geUrl = 'https://api.goingelectric.de/chargepoints/?'+
        `key=${goingelectricToken}&`+
        `plugs=${compatiblePlugs}&min_power=${minPower}&`+
        `ne_lat=${searchField.getNorthEast().lat}&ne_lng=${searchField.getNorthEast().lng}&`+
        `sw_lat=${searchField.getSouthWest().lat}&sw_lng=${searchField.getSouthWest().lng}`;
      httpGet(geUrl,true,f);
    };

    function getMaxChargePoint (chargePoints) {
      var maxPower = 0;
      var maxType = "";
      var maxCount = 0;

      chargePoints.forEach(chargePoint => {
        if (chargePoint.power > maxPower && compatiblePlugs.includes(chargePoint.type)) {
          maxPower = chargePoint.power;
          maxType = chargePoint.type;
          maxCount = chargePoint.count;
        };
      });
      return {'power': maxPower, 'type': maxType, 'count': maxCount};
    };

    function chargeLocationDetails(chargeLocation,includeDistance) {
      // var maxPower = 0;
      // chargeLocation.chargepoints.forEach(chargePoint => { maxPower = (chargePoint.power > maxPower) ? chargePoint.power : maxPower; });
      // var maxChargePoint = getMaxChargePoint(JSON.parse(chargeLocation.chargepoints));
      var maxChargePoint = getMaxChargePoint(chargeLocation.chargepoints);

      if (includeDistance) {
        var route = getRoute(teslaPosition,{'longitude' : chargeLocation.coordinates.lng, 'latitude' : chargeLocation.coordinates.lat});
      };
      return {
        "id": chargeLocation.ge_id.toString(),
        "type": "Feature",
        "properties": {
          "icon": (chargeLocation.fault_report) ? "faultReport" :
            (chargeLocation.network.toString().toLowerCase().includes("tesla supercharger")) ? "teslaSuperCharger" :
            (maxChargePoint.power >= superCharger.minPower) ? "thirdSuperCharger" :
            (maxChargePoint.power >= highwayCharger.minPower) ? "highwayCharger" :
            (maxChargePoint.type == "Typ2") ? "parkCharger" :
            "socketCharger",

          "coordinates": chargeLocation.coordinates,
          "chargepoints": chargeLocation.chargepoints,
          "name": chargeLocation.name.replace("\'","’"),
          "street": chargeLocation.address.street,
          "city": chargeLocation.address.city,
          "country": chargeLocation.address.country,
          "network": chargeLocation.network,
          "operator": chargeLocation.operator,
          "count" : maxChargePoint.count,
          "power" : maxChargePoint.power,
          "type" : maxChargePoint.type,
          "url": chargeLocation.url,
          "distanceRaw" : includeDistance ? route.distanceRaw : false,
          "distance" : includeDistance ? route.distance : false,
          "duration" : includeDistance ? route.duration : false,
          "rangeRaw" : includeDistance ? route.rangeRaw : false,
          "range" : includeDistance ? route.range : false
        },
        "geometry": {
          "type": "Point",
          "coordinates": [chargeLocation.coordinates.lng, chargeLocation.coordinates.lat]
        }
      };
    };

    function chargerListHeader() {
      var routeChargerList = '';
      routeChargerList += `<div style="max-height: 690px; box-sizing: border-box; overflow-y: auto; padding: 0px 20px;">`;
      return routeChargerList;
    };

    function chargerListFooter() {
      var routeChargerList = '';
      routeChargerList += `<a href="#" onclick="flyToCharger(${currentDestination.center[0]},${currentDestination.center[1]},'${currentDestination.text}',''); return false;">`;
      routeChargerList += `<div style="position: relative; padding-left: ${iconColumnWidth}px;">`;
      routeChargerList += `<div style="position: absolute; left: -10px; width: ${iconColumnWidth}px;">`;
      routeChargerList += `<img style="display: block; margin-left: auto; margin-right: auto; padding-top: 20px;" src="${destinationImage}"/>`
      routeChargerList += `</div>`;
      routeChargerList += `<p><table border="0" width="100%" style="border-collapse: collapse;"><tbody><tr>`;
      routeChargerList += `<td align="left" style="padding: 0px;margin: 0px;"><strong>${currentRoute.distance}, ${currentRoute.duration}</strong></td>`;
      routeChargerList += `<td align="right" style="padding: 0px;margin: 0px;"><img class="battery-icon" src="${batteryImage(currentRoute.rangeRaw)}">${currentRoute.range ? currentRoute.range : ""}</td>`;
      routeChargerList += `</tr></tbody></table>`;
      routeChargerList += `${currentDestination.name}</p>`;
      routeChargerList += `</div></a>`;

      routeChargerList += `</div>`;

      routeChargerList += `<div class="onecolumn" style="padding: 20px 20px 0px;"><a class="popupbutton" href="#" style="width: 280px;" onclick="cancelRouteChargerList(); return false;">Abbrechen</a>`;
      routeChargerList += `<a class="popupbutton ${minPowerList == highwayCharger.minPower ? 'popupbutton-icon-highwayCharger-active': 'popupbutton-icon-highwayCharger'}" style="width: 60px; float: right; o" href="#" onclick="toggleeRouteList(); return false;"></a></div>`;
      return routeChargerList;
    };

    function waitChargerList() {
      var routeChargerList = chargerListHeader();
      routeChargerList += `<p style="text-align: center;">Ladestationen für die Route<br>werden gesucht</p>`;
      routeChargerList += `<img style="display: block; margin-left: auto; margin-right: auto;" src="${waitImage}"/>`;
      routeChargerList += chargerListFooter();
      routeList(routeChargerList);
    };

    function setRouteLine() {
      getRoute(teslaPosition,{'longitude' : currentDestination.center[0], 'latitude' : currentDestination.center[1]},'full',function () {
        if (this.readyState === 4) {
          var result = JSON.parse(this.responseText);
          if (result.code == "Ok") {
            var lineRoute = processRouteResults(result);
            showRoute(lineRoute.coordinates);
          };
        }
      });
    };

    var numUpdates, maxUpdates = 30;
    function updateRouteChargerEmergencyStop(initial) {
      if (initial) { numUpdates = 0 };
      numUpdates++;
      if (numUpdates >= maxUpdates) {
        // clearInterval(updateListInterval);
        cancelRouteChargerList();
        console.log('Maximum List updates reached. Updates force stopped.');
      };
    };

    function updateRouteChargerList(initial) {
      if (initial || lineDistance([[teslaPosition.longitude,teslaPosition.latitude],[updateListPosition.longitude,updateListPosition.latitude]]) > updateListDistance) {
        updateListPosition = teslaPosition;
        setRouteLine();
        setRouteChargerList(initial);
        updateRouteChargerEmergencyStop(initial);
      } else {
        console.log('Positon unchaged. Skipping list update.');
      };
    };

    function processRouteResults(result) {
      if (result.code == "Ok") {
        return {
          'distanceRaw': result.routes[0].distance/1000,
          'distance': (result.routes[0].distance/1000).toFixed((result.routes[0].distance < 10000) ? 1 : 0).toString().replace(".",",")  + ' km',
          'durationRaw': result.routes[0].duration,
          'duration': secondsToTime(result.routes[0].duration),
          'rangeRaw' : teslaPosition.range ? teslaPosition.range - result.routes[0].distance/1000 : false,
          'range' : teslaPosition.range ? (teslaPosition.range - result.routes[0].distance/1000).toFixed(0).toString() + ' km' : false,
          'coordinates': result.routes[0].geometry ? decodePolyline(result.routes[0].geometry) : false
        }
      } else {
        return null
      }
    };

    function processLoop( actionFunc, numTimes, doneFunc, contCond ) {
      var i = 0;
      var f = function () {
        if (i < numTimes && contCond()) {
          actionFunc( i++ );
          setTimeout( f, 50 );
        }
        else if (doneFunc && contCond()) {
          doneFunc();
        }
      };
      f();
    }

    var chargerList;
    var checkList = [];
    var routeChargers = {
        "type": "FeatureCollection",
        "features": []
    };

    function processRouteSegments(i) {
      var lineBox;
      lineBox = distantLineBox([currentRoute.coordinates[i],currentRoute.coordinates[i+1]],maxChargerDistance);
      chargerList.chargelocations.forEach(chargeLocation => {
        if (!checkList.includes(chargeLocation.ge_id)) {
          if (pointIsInBox([chargeLocation.coordinates.lng, chargeLocation.coordinates.lat],lineBox)) {
            console.log('Add:', chargeLocation.ge_id, chargeLocation.name, chargeLocation.address.city);
            checkList.push(chargeLocation.ge_id);
            routeChargers.features.push(chargeLocationDetails(chargeLocation,true));
          }
        }
      });
    };

    function postProcessSegments() {
      routeChargers.features.sort((a,b) => { return a.properties.distanceRaw - b.properties.distanceRaw });

      var routeChargerList = chargerListHeader();
      var icon = '';
      routeChargers.features.forEach( chargeLocation => {
        icon = (chargeLocation.properties.icon == "faultReport") ? faultReportImage :
               (chargeLocation.properties.icon == "teslaSuperCharger") ? teslaSuperChargerImage :
               (chargeLocation.properties.icon == "thirdSuperCharger") ? thirdSuperChargerImage :
               (chargeLocation.properties.icon == "highwayCharger") ? highwayChargerImage :
               parkChargerImage;
        routeChargerList += `<a href="#" onclick="flyToCharger(${chargeLocation.properties.coordinates.lng},${chargeLocation.properties.coordinates.lat},'${chargeLocation.properties.name}','${chargeLocation.properties.city}'); return false;">`;
        routeChargerList += `<div style="position: relative; padding-left: ${iconColumnWidth}px;${chargeLocation.properties.rangeRaw < 0 ? ' opacity: 0.5;' : ''}">`;
        routeChargerList += `<div style="position: absolute; left: -10px; width: ${iconColumnWidth}px;">`;
        routeChargerList += `<img style="display: block; margin-left: auto; margin-right: auto; padding-top: 20px;" src="${icon}"/>`
        routeChargerList += `</div>`;
        routeChargerList += `<p><table border="0" width="100%" style="border-collapse: collapse;"><tbody><tr>`;
        routeChargerList += `<td align="left" style="padding: 0px;margin: 0px;"><strong>${chargeLocation.properties.distance}, ${chargeLocation.properties.duration}</strong></td>`;
        routeChargerList += `<td align="right" style="padding: 0px;margin: 0px;"><img class="battery-icon" src="${batteryImage(chargeLocation.properties.rangeRaw)}">${chargeLocation.properties.range ? chargeLocation.properties.range : ""}</td>`;
        routeChargerList += `</tr></tbody></table>`;
        routeChargerList += `${chargeLocation.properties.network && !chargeLocation.properties.name.includes(chargeLocation.properties.network) ? chargeLocation.properties.network : ''} ${chargeLocation.properties.name} ${chargeLocation.properties.name.includes(chargeLocation.properties.city) ? '' : chargeLocation.properties.city}<br>`;
        routeChargerList += `${chargeLocation.properties.count}x ${chargeLocation.properties.power} kW ${chargeLocation.properties.type}</p>`;
        routeChargerList += `</div></a>`;
      });

      routeChargerList += chargerListFooter();
      routeList(routeChargerList);

    };

    function processRouteChargers() {
      checkList = [];
      routeChargers = {
          "type": "FeatureCollection",
          "features": []
      };
      var routeBox;
      routeBox = distantLineBox(boundingBox(currentRoute.coordinates),maxChargerDistance);

      getChargersInBoundingBox(boundingBox(routeBox), minPowerList, function () {
        if (this.readyState === 4) {
          chargerList = JSON.parse(this.responseText);
          if (chargerList.status != "ok") {throw "GoingElectric request failed"};
          if (chargerList.startkey == 500) {console.log("More than 500 chargers in area");}
          console.log('Charger List:', chargerList);
          processLoop(processRouteSegments, currentRoute.coordinates.length-1, postProcessSegments, () => {return currentDestination !== false});
        }
      });
    };

    function setRouteChargerList(showWait) {
      getRoute(teslaPosition,{'longitude' : currentDestination.center[0], 'latitude' : currentDestination.center[1]}, 'simplified', function () {
        if (this.readyState === 4) {
          var result = JSON.parse(this.responseText);
          if (result.code == "Ok") {
            currentRoute = processRouteResults(result);
            if (showWait) {
              waitChargerList()
            };
            <? if (isset($_GET["bigbox"])) {echo "showBigBox();";} ?>
            <? if (isset($_GET["boxes"])) {echo "showBoxes();";} ?>
            <? if (isset($_GET["debugroute"])) {echo "showRoute(currentRoute.coordinates);";} ?>
            processRouteChargers();
          };
        }
      });
    };

    function toggleeRouteList(){
      minPowerList = minPowerList == superCharger.minPower ? highwayCharger.minPower : superCharger.minPower;
      updateRouteChargerList(true);
    };

    function cancelRouteChargerList() {
      clearInterval(updateListInterval);
      currentDestination = false;
      hideRouteList();
      hideRoute()
      document.cookie = "destination=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //  Destination Cookie löschen
    };

    function updateChargers() {
      getChargersInBounds(map.getBounds(), function () {
        if (this.readyState === 4) {
          var chargerList = JSON.parse(this.responseText);
          if (chargerList.status != "ok") {throw "GoingElectric request failed"};
          if (chargerList.startkey == 500) {console.log("More than 500 chargers in area");}
          console.log("Update Chargers: ", chargerList);

          var newList = {
              "type": "FeatureCollection",
              "features": []
          };
          chargerList.chargelocations.forEach(chargeLocation => {
            newList.features.push(chargeLocationDetails(chargeLocation));
          });
          map.getSource('chargers').setData(newList);
        }
      })
    };

    function chargerShortDescription (id, chargeLocation) {
      var address = `${chargeLocation.street}, ${chargeLocation.city}, ${chargeLocation.country}`;

      var description = '';
      description = `<strong>${chargeLocation.name} ${chargeLocation.name.includes(chargeLocation.city) ? '' : chargeLocation.city}</strong>`;

      description += (chargeLocation.network && !chargeLocation.name.includes(chargeLocation.network)) ?
                     (`<br>${chargeLocation.network}<p>`) :
                     // (chargeLocation.operator && !chargeLocation.name.includes(chargeLocation.operator)) ?
                     // `<br>${chargeLocation.operator}<p>` :
                     '<p>';

      description += `${chargeLocation.count}x ${chargeLocation.power} kW ${chargeLocation.type}`;
      description += `<span id='location_description_${id}'></span><p>`;
      description += `<span id='fault_report_${id}'></span>`;
      description += '<hr>';
      description += `<span id='ladeweile_${id}'></span>`;

      description += `${chargeLocation.street}<br>${chargeLocation.city}<p>`;
      description += `<span id='distance_${id}'></span>`;

      description += `<div class="twocolumns"><a class="popupbutton popupbutton-icon-navigate" href="#" onclick="sendDestinationToTesla('${address}'); return false;"></a><a class="popupbutton popupbutton-icon-link" href="http://${chargeLocation.url}" target="_blank"></a></div>`;

      return {'text': description, 'address': address};
    };

    function addChargerDetails(id) {
      var locationDescription = document.getElementById(`location_description_${id}`);
      var faultReport = document.getElementById(`fault_report_${id}`);
      var ladeweile = document.getElementById(`ladeweile_${id}`);

      var geUrl = 'https://api.goingelectric.de/chargepoints/?'+
        `key=${goingelectricToken}&`+
        `ge_id=${id}`;

      httpGet(geUrl, false, function () {
        if (this.readyState === 4) {
          var chargerDetails = JSON.parse(this.responseText);
          if (chargerDetails.status != "ok") {throw "GoingElectric request failed"};
          var chargeLocation = chargerDetails.chargelocations[0];

          locationDescription.innerHTML = (chargeLocation.location_description) ? (`<br>${chargeLocation.location_description.replace("\'","’")}`) : '';
          faultReport.innerHTML = (chargeLocation.fault_report) ? (`<strong>Störung:</strong> ${chargeLocation.fault_report.description.replace("\'","’")}<p>`) : '';

          ladeweile.innerHTML = (chargeLocation.ladeweile) ? (`Ladeweile: ${chargeLocation.ladeweile.replace("\'","’")}<p>`) : '';
        }
      });
    };

    function addChargerDistance(id, coordinates) {
      var distance = document.getElementById(`distance_${id}`);

      getRoute(teslaPosition,{'longitude' : coordinates[0], 'latitude' : coordinates[1]}, false, function () {
        if (this.readyState === 4) {
          var result = JSON.parse(this.responseText);
          if (result.code == "Ok") {
            var route = processRouteResults(result);
            var rangeAtArrival = (teslaPosition.range - route.distanceRaw).toFixed()

            var rangeBlock = '';
            rangeBlock += `<table border="0" width="100%" style="border-collapse: collapse;"><tbody><tr>`;
            rangeBlock += `<td align="left" style="padding: 0px;margin: 0px;"><strong>${route.distance}, ${route.duration}</strong></td>`;
            rangeBlock += `<td align="right" style="padding: 0px;margin: 0px;">${rangeAtArrival<10?'<span class="mapboxgl-popup-content-warning">':''}<img class="battery-icon" src="${batteryImage(rangeAtArrival)}">${rangeAtArrival} km${rangeAtArrival<10?'</span>':''}</td>`;
            rangeBlock += `</tr></tbody></table>`;
            rangeBlock += '<p>'

            distance.innerHTML = rangeBlock;
          };
        }
      });
    };

    // function settingsContent(){
    //   var content = '';
    //   content += '';
    //   content += '<form>';
    //   content += '	<strong>Mit Tesla verbinden</strong><p>';
    //   content += '	<label for="email">E-Mail</label>';
    //   content += '	<input id="email" name="email" type="text" value="" /> <br />';
    //
    //   content += '  <label for="password">Passwort</label>';
    //   content += '	<input id="password" name="password" type="text" value="" /> <br />';
    //
    //   content += '  <button type="button" id="connect">Aktivieren</button><p>';
    //   content += '  Status: <status></status>';
    //   content += '</form>';
    //
    //   return content;
    // };

  </script>

</body>
</html>
