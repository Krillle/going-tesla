<?php
// include_once("index.html");

// Sunset for location of Berlin
$lat = 52.52;
$lon = 13.41;
$offset = 0; // Herokuserver timezome is GMT

$sunrise = date_sunrise(time(), SUNFUNCS_RET_TIMESTAMP, $lat, $lon, 90, $offset);
$sunset = date_sunset(time(), SUNFUNCS_RET_TIMESTAMP, $lat, $lon, 90, $offset);
$now = time();

if ($now < $sunrise or $now > $sunset) {
  $darkmode = true;
} else {
  $darkmode = false;
};

if(isset($_GET["light"])) {$darkmode = false;};
if (isset($_GET["dark"])) {$darkmode = true;};

?>

<!DOCTYPE html>
<html>
<head>
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
  <style>
    body { margin:0; padding:0; }
    #map { position:absolute; top:0; bottom:0; width:100%; }

    #map .mapboxgl-ctrl {
      opacity: 0.7;
    }

    #map .mapboxgl-ctrl-group > button {
      width:70px;
      height:70px;
    }

    #map .mapboxgl-ctrl-icon.mapboxgl-ctrl-compass > .mapboxgl-ctrl-compass-arrow  {
      width:40px;
      height:40px;
    }

    #map .mapboxgl-ctrl-top-left .mapboxgl-ctrl {
      width: 400px;
      min-width: 400px;
      max-width:400px;
    }

    #map .mapboxgl-ctrl-icon.mapboxgl-ctrl-autozoom > .mapboxgl-ctrl-autozoom-icon {
      width: 40px;
      height: 40px;
      margin: 5px;
      background-image: url("https://img.icons8.com/small/40/333333/gps-device.png");
      background-repeat: no-repeat;
      display: inline-block;
    }

    #map .mapboxgl-ctrl-geocoder .suggestions {
      font: 20px/1.4 'Gotham Light', 'Verdana', 'Source Sans Pro', 'Helvetica Neue', Sans-serif;5
    }

    #map .mapboxgl-ctrl-geocoder--input {
      font:700 20px/1.15 'Gotham Light', 'Verdana', 'Source Sans Pro', 'Helvetica Neue', Sans-serif;
      height: 60px;
    }
    #map .mapboxgl-ctrl-geocoder--icon {
      top: 18px !important;
      left: 5px !important;
      width: 25px !important;
      height: 25px !important;
    }
    #map .mapboxgl-ctrl-geocoder--icon-close {
      margin-top:11px !important;
    }

    .mapboxgl-popup-anchor-bottom > .mapboxgl-popup-tip {
      border-top-color: #ffffff; /* light theme */
      <? if ($darkmode) {echo "border-top-color: #0d0d0b; /* dark theme */";} ?>
    }
    .mapboxgl-popup-anchor-top > .mapboxgl-popup-tip {
      border-bottom-color: #ffffff; /* light theme */
      <? if ($darkmode) {echo "border-bottom-color: #0d0d0b; /* dark theme */";} ?>
    }
    .mapboxgl-popup-anchor-left > .mapboxgl-popup-tip {
      border-right-color: #ffffff; /* light theme */
      <? if ($darkmode) {echo "border-right-color: #0d0d0b; /* dark theme */";} ?>
    }
    .mapboxgl-popup-anchor-right > .mapboxgl-popup-tip {
      border-left-color: #ffffff; /* light theme */
      <? if ($darkmode) {echo "border-left-color: #0d0d0b; /* dark theme */";} ?>
    }
    .mapboxgl-popup-close-button {
      display:none;
    }

    .mapboxgl-popup-content {
      font:700 20px/1.15 'Gotham Light', 'Verdana', 'Source Sans Pro', 'Helvetica Neue', Sans-serif;
      padding:40px;
      padding-bottom: 25px;
      border-radius:10px 10px 10px 10px;
      width:420px;
      background:#ffffff; /* light theme  */
      <? if ($darkmode) {echo "background:#191a1a; /* dark theme */";} ?>
      color:#8F8F8F; /* light theme  */
      <? if ($darkmode) {echo "color:#9c9c9c; /* dark theme */";} ?>
    }
    .mapboxgl-popup-content a {
      color:#8F8F8F;  /* light theme  */
      <? if ($darkmode) {echo "color:#9c9c9c; /* dark theme */";} ?>
    }
    .mapboxgl-popup-content strong {
      font:700 20px/1.15 'Gotham Medium', 'Verdana', 'Source Sans Pro', 'Helvetica Neue', Sans-serif;
      color:#000000; /* light theme  */
      <? if ($darkmode) {echo "color:#e6e6e6; /* dark theme */";} ?>
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
    	column-count: 1;
      padding: 0px !important;
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
      font-weight: 600;
    	padding: 8px;
      padding-top: 18px;
    	border-radius: 10px;
    	width: 100%;
    	height: 60px;
    	background: #d6d6d6; /* light theme */
      <? if ($darkmode) {echo "background: #4a4848; /* dark theme */";} ?>
    	color: #000000; /* light theme  */
    	<? if ($darkmode) {echo "color: #ffffff; /* dark theme */";} ?>
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

    .info-container {
      position: absolute;
      top: 25px;
      right: 100px;
      z-index: 1;
    }

    .info-container > * {
      background-color: rgba(255, 255, 255, 0.7); /* light theme  */
      <? if ($darkmode) {echo "background-color: rgba(0, 0, 0, 0.7); /* dark theme */";} ?>
      font:700 20px/1.15 'Gotham Medium', 'Verdana', 'Source Sans Pro', 'Helvetica Neue', Sans-serif;
      color:#8F8F8F; /* light theme  */
      <? if ($darkmode) {echo "color:#e6e6e6; /* dark theme */";} ?>
      display: block;
      margin: 0;
      padding: 10px 20px;
      border-radius:10px 10px 10px 10px;
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

      background-color: rgba(255, 255, 255, 0.7); /* light theme  */
      <? if ($darkmode) {echo "background-color: rgba(0, 0, 0, 0.7); /* dark theme */";} ?>
      /* font:700 20px/1.15 'Gotham Medium', 'Verdana', 'Source Sans Pro', 'Helvetica Neue', Sans-serif; */
      font:400 20px/1.15 'Gotham Medium', 'Verdana', 'Source Sans Pro', 'Helvetica Neue', Sans-serif;
      color:#8F8F8F; /* light theme  */
      <? if ($darkmode) {echo "color:#e6e6e6; /* dark theme */";} ?>
      display: block;
      margin: 0;
      padding: 10px 20px;
      border-radius:10px 10px 10px 10px;
    }

    .route-container a {
      color:#8F8F8F;  /* light theme  */
      text-decoration: none;
      <? if ($darkmode) {echo "color:#9c9c9c; /* dark theme */";} ?>
    }

  </style>
</head>
<body>

  <script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.4.2/mapbox-gl-geocoder.min.js'></script>
  <link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.4.2/mapbox-gl-geocoder.css' type='text/css' />
  <!-- Promise polyfill script required to use Mapbox GL Geocoder in IE 11 -->
  <script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js"></script>

  <div id='map'></div>
  <div id='info' class='info-container'></div>
  <div id='route' class='route-container'></div>
  <script>
    if (location.hostname == 'goingtesla.herokuapp.com' && location.protocol !== 'https:') {location.protocol = 'https:'; throw new Error('Changing to secure connection');};

    console.log("App started");

    <? if ($darkmode) {echo "const darkmode = true;";} else {echo "const darkmode = false;";}  ?>

    const goingelectricToken = '5471b3bae96a68bbf90cad00834fb10e';
    const compatiblePlugs = 'CCS,Tesla Supercharger,Tesla Supercharger CCS,Typ2';

    const chargerBigSize = '44';
    const chargerHighwaySize = '39';
    const chargerParkSize = '34';
    const chargerFaultSize = '24';

    var mapStyle = 'mapbox://styles/krillle/ck0my3cjp4nfm1cksdx1rap0q?optimize=true'; // Light Tesla
    const mapStyleSatellite = 'mapbox://styles/mapbox/satellite-v9'; // Satellite
    const chargerTeslaColor = "ff514a";
    var chargerThirdColor = "4b535a"; // dark marker for light map
    var chargerParkColor = "5a5a5a"; // dark marker for light map
    const chargerFaultColor = "ffb800";

    var routeColor = "4d69ea";

    if (darkmode) {
      console.log('Switching to Dark Mode');
      mapStyle = 'mapbox://styles/krillle/ck1fdx1ok208r1drsdxwqur5f?optimize=true'; // Dark Tesla
      chargerThirdColor = "787878"; // light marker for dark map
      chargerParkColor = "e6e6e6"; // light marker for dark map
    };

    const superCharger = {'minPower':'100', 'minZoom':null, 'toggle':2}
    const highwayCharger = {'minPower':'50', 'minZoom':11, 'toggle':2}
    const destinationCharger = {'minPower':'11', 'minZoom':14, 'toggle':1}

    var minPower = superCharger.minPower;

    const slowSpeed = 30;
    const highSpeed = 100;
    const slowSpeedZoom = '16';
    const highSpeedZoom = '9';

    const maxChargerDistance = 3000; // max senkrechter Abstand Charger von Route

    const updatePositionInterval = 20000;

    var zoomToogle = [
      {name:'AutoZoom', zoom:null, autoZoom:true, autoFollow:true, headUp:true, icon:'url("https://img.icons8.com/small/40/333333/gps-device.png"'},
      {name:'DestinationCharger', zoom:slowSpeedZoom, autoZoom:false, autoFollow:true, headUp:false, icon:'url("https://img.icons8.com/ios-glyphs/40/333333/park-and-charge.png"'},
      {name:'SuperCharger', zoom:highSpeedZoom, autoZoom:false, autoFollow:true, headUp:false, icon:'url("https://img.icons8.com/material-sharp/40/333333/tesla-supercharger-pin--v1.png"'}
    ];
    var zoomToggleState = 0;

    var teslaConnection = {'accessToken': getCookie('access'),'refreshToken': getCookie('refresh'), 'vehicle': getCookie('vehicle'), 'status': 'undefined' };
    // var teslaPosition = {'longitude' : 10.416667, 'latitude' : 51.133333, 'heading': 0, 'speed' : 100, 'zoom': 9, 'range': false};
    var teslaPosition = {'longitude' : 13.48, 'latitude' : 52.49, 'heading': 0, 'speed' : 100, 'zoom': 9, 'range': 100};

    const positionSize = '44';
    var positionColor = 'ff514a';

    var autoZoom = true;
    var autoFollow = true;
    var headUp = true;
    const m = (highSpeedZoom - slowSpeedZoom) / (highSpeed - slowSpeed);
    const b = slowSpeedZoom - m * slowSpeed;

    var infoContainer = document.getElementById('info');
    var routeContainer = document.getElementById('route');

    console.log('Establish Connection to Tesla');
    try {connectTesla ()}
    catch {console.log('Tesla not reachable')};

    var positionIcon = {
      type: 'Feature',
      properties: {'bearing': teslaPosition.heading},
      geometry: {
        type: 'Point',
        coordinates: [teslaPosition.longitude,teslaPosition.latitude]
      }
    };
    zoomToPower(teslaPosition.zoom);

    mapboxgl.accessToken = 'pk.eyJ1Ijoia3JpbGxsZSIsImEiOiJjazBlYWc5OTMwOGhrM2tsY2pxcmgyYzVtIn0.0novoDiTaGPwZ5tPMDDl1A';
    if (!mapboxgl.supported()) {
      console.log('Browser does not support Mapbox GL.');
      gtag('event', 'No Mapbox GL', {'event_category': 'Connect'});
      alert('Diese Anwendung läuft leider nicht auf MCU1.');
    } else {
      var map = new mapboxgl.Map({
        container: 'map', // container id
        style: mapStyle,
        center: [teslaPosition.longitude,teslaPosition.latitude], // starting position
        zoom: teslaPosition.zoom, // starting zoom
        bearing: teslaPosition.heading,
        attributionControl: false
      });
    };

    // Add geocoder search field
    var geocoderControl = new MapboxGeocoder({
      accessToken: mapboxgl.accessToken,
      mapboxgl: mapboxgl,
      trackProximity: true
    })
    geocoderControl.on('result', function(destination) {
      console.log('Destination:', destination.result.text);
      // ---- 8< -----v
      gtag('event', 'Route Chargers', {'event_category': 'Destination', 'event_label': `${destination.result.text}`});

      var route = getRoute(teslaPosition,{'longitude' : destination.result.center[0], 'latitude' : destination.result.center[1]},'simplified');
      // showBoxes(route.coordinates);
      var routeChargers = getRouteChargers(route.coordinates);
      var routeChargerList = '';
      routeChargers.features.forEach( chargeLocation => {
        // routeChargerList += `<p><strong>${chargeLocation.properties.name} ${chargeLocation.properties.city}</strong><br>`;
        routeChargerList += `<a href="#" onclick="flyToCharger(${chargeLocation.properties.coordinates.lng},${chargeLocation.properties.coordinates.lat},'${chargeLocation.properties.name}','${chargeLocation.properties.city}'); return false;"><p><strong>${chargeLocation.properties.distance} ${chargeLocation.properties.duration} ${chargeLocation.properties.range ? chargeLocation.properties.range : ""}</strong><br>`;
        routeChargerList += `${chargeLocation.properties.name} ${chargeLocation.properties.name.includes(chargeLocation.properties.city) ? '' : chargeLocation.properties.city}<br>`;
        routeChargerList += `${chargeLocation.properties.count}x ${chargeLocation.properties.power} kW ${chargeLocation.properties.type}</p></a>`;
      });
      routeChargerList += `<div class="onecolumn"><a class="popupbutton" href="#" onclick="hideRouteList();hideRoute(); return false;">Abbrechen</a></div>`;
      routeList(routeChargerList);

      var route = getRoute(teslaPosition,{'longitude' : destination.result.center[0], 'latitude' : destination.result.center[1]},'full');
      showRoute(route.coordinates);
      // ---- 8< -----^
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

      // Create Tesla Supercharger Image
      map.loadImage(`https://img.icons8.com/material-sharp/${chargerBigSize}/${chargerTeslaColor}/tesla-supercharger-pin--v1.png`, function(error, image) {
        if (error) throw error;
        map.addImage('teslaSuperCharger', image);
      });

      // Create Third Party Supercharger Image
      map.loadImage(`https://img.icons8.com/material-sharp/${chargerBigSize}/${chargerThirdColor}/tesla-supercharger-pin--v1.png`, function(error, image) {
        if (error) throw error;
        map.addImage('thirdSuperCharger', image);
      });

      // Create DC Highway Charger Image
      map.loadImage(`https://img.icons8.com/small/${chargerHighwaySize}/${chargerParkColor}/tesla-supercharger-pin.png`, function(error, image) {
        if (error) throw error;
        map.addImage('highwayCharger', image);
      });

      // Create Park Charger Image
      map.loadImage(`https://img.icons8.com/ios-glyphs/${chargerParkSize}/${chargerParkColor}/park-and-charge.png`, function(error, image) {
        if (error) throw error;
        map.addImage('parkCharger', image);
      });

      // Create Fault Report Image
      map.loadImage(`https://img.icons8.com/ios-glyphs/${chargerFaultSize}/${chargerFaultColor}/error.png`, function(error, image) {
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

      console.log("Initalize Chargers");
      updateChargers();

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

      var coordinates = e.features[0].geometry.coordinates.slice();
      map.flyTo({ 'center': e.features[0].geometry.coordinates});

      chargerID = e.features[0].id

      var popup = new mapboxgl.Popup({ offset: 25, anchor: 'bottom' })
      map.once('idle', function(e) {
        console.log('Map idle',chargerID);
        popup.setHTML(chargerDescription(chargerID).text)
      });
      popup.setLngLat(coordinates)
      .setHTML(chargerShortDescription(e.features[0].properties).text)
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
        // map.jumpTo({ 'center': [teslaPosition.longitude,teslaPosition.latitude], 'zoom': teslaPosition.zoom, 'bearing': teslaPosition.heading });
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
      return ((hours > 0) ? hours + '   Std ' : '') + minutes + ' Min';
    };

    function httpGet(url, token) {
      var httpReq = new XMLHttpRequest();
      httpReq.open('GET', url, false);
      if (token) {httpReq.setRequestHeader('authorization','bearer ' + teslaConnection.accessToken)};
      httpReq.send(null);
      // console.log("Result: " + httpReq.responseText);
      return httpReq.responseText;
    };

    function getCookie(name) {
      var value = "; " + document.cookie;
      var parts = value.split("; " + name + "=");
      if (parts.length == 2) return parts.pop().split(";").shift();
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
      var password = prompt("Bitte Passwort für diesen Tesla-Account eingeben");

      createTeslaToken(email, password);
    };

    function infoMessage(message) {
      if (infoContainer.innerHTML) {infoContainer.innerHTML = '';};
      var pre = document.createElement('pre');
      pre.textContent = message;
      infoContainer.appendChild(pre);
      setTimeout(function(){ infoContainer.innerHTML = ''; }, 3000);
    };

    function routeList(message) {
      if (routeContainer.innerHTML) {routeContainer.innerHTML = '';};
      // var pre = document.createElement('pre');
      routeContainer.innerHTML = message;
      // routeContainer.appendChild(pre);
      map.setLayoutProperty('route', 'visibility', 'visible');
    };

    function hideRouteList() {
      routeContainer.innerHTML = '';
      map.setLayoutProperty('route', 'visibility', 'none');
    };

    // Tesla connection - - - - - -

    function connectTesla () {
      // document.cookie = "access=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //  Cookie löschen
      console.log ('Access cookie: ' + getCookie('access'));
      console.log ('Access data: ' + teslaConnection.accessToken);

      if (typeof(teslaConnection.accessToken) == 'undefined') {
        teslaConnection.status = 'Kein Token';
        console.log(teslaConnection.status);
        infoMessage(teslaConnection.status);
        gtag('event', 'No Token', {'event_category': 'Connect'});
        settingsPopup ();
        return;
      };

      var vehicleData = getTeslaCarData();
      console.log(vehicleData);
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
        gtag('event', 'Not reachable', {'event_category': 'Connect'});
        return;
      }
      else {
        teslaConnection.status = 'Verbunden mit ' + vehicleData.response.vehicle_state.vehicle_name;
        console.log(teslaConnection.status);
        infoMessage(teslaConnection.status);
        gtag('event', 'Connected', {'event_category': 'Connect', 'event_label': vehicleData.response.vehicle_state.vehicle_name});
        setTeslaPosition(vehicleData.response.drive_state);
        console.log ('Starting continous update');
        setInterval(updatePosition, updatePositionInterval);
      };
    };

    function setTeslaPosition(driveStatus) {
      var zoom = ((driveStatus.speed) ? driveStatus.speed : 0) * m + b;
      zoom = (zoom > slowSpeedZoom) ? slowSpeedZoom : (zoom < highSpeedZoom) ? highSpeedZoom : zoom;

      teslaPosition = {
        'longitude': driveStatus.longitude,
        'latitude': driveStatus.latitude,
        'heading': driveStatus.heading,
        'speed': (driveStatus.speed) ? driveStatus.speed : 0,
        'zoom': zoom
      };
    };

    function updatePosition() {
      setTeslaPosition(getTeslaDriveStatus().response);

      if (positionIcon.geometry.coordinates != [teslaPosition.longitude,teslaPosition.latitude]
          && positionIcon.properties.bearing != teslaPosition.heading) {
        positionIcon.geometry.coordinates = [teslaPosition.longitude,teslaPosition.latitude];
        positionIcon.properties.bearing = teslaPosition.heading;

        map.getSource('positionIcon').setData(positionIcon);

        updateMapFocus ();
      };
    };

    // - - - - - - - - Tesla requests - - - - - - - - -

    function getTeslaChargeStatus() {
      var teslaUrl = 'https://goingtesla.herokuapp.com/corsproxy.php?'
          + 'csurl=https://owner-api.teslamotors.com/api/1/vehicles/' + teslaConnection.vehicle + '/data_request/charge_state';

      return JSON.parse(httpGet(teslaUrl,true));
    };

    function getTeslaDriveStatus() {
      var teslaUrl = 'https://goingtesla.herokuapp.com/corsproxy.php?'
          + 'csurl=https://owner-api.teslamotors.com/api/1/vehicles/' + teslaConnection.vehicle + '/data_request/drive_state';

      return JSON.parse(httpGet(teslaUrl,true));
    };

    function getTeslaCarData() {
      var teslaUrl = 'https://goingtesla.herokuapp.com/corsproxy.php?'
          + 'csurl=https://owner-api.teslamotors.com/api/1/vehicles/' + teslaConnection.vehicle + '/vehicle_data';

      return JSON.parse(httpGet(teslaUrl,true));
    };

    function getTeslaVehicles() {
      var teslaUrl = 'https://goingtesla.herokuapp.com/corsproxy.php?'
          + 'csurl=https://owner-api.teslamotors.com/api/1/vehicles';

      return JSON.parse(httpGet(teslaUrl,true));
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

          console.log("Access: " + teslaConnection.accessToken);
          console.log("Refresh: " + teslaConnection.refreshToken);

          teslaConnection.vehicle = getTeslaVehicles().response[0].id_s;
          console.log("Vehicle: " + teslaConnection.vehicle);

          document.cookie = 'access=' + teslaConnection.accessToken + '; expires=Thu, 10 Aug 2022 12:00:00 UTC";';
          document.cookie = 'refresh=' + teslaConnection.refreshToken + '; expires=Thu, 10 Aug 2022 12:00:00 UTC";';
          document.cookie = 'vehicle=' + teslaConnection.vehicle + '; expires=Thu, 10 Aug 2022 12:00:00 UTC";';

          connectTesla();
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
          console.log('Send destination: ' + this.responseText);
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

      // console.log("Ausgangsrichtung", bearing)
      corners.forEach( (vectors, i) => {
        vectors.forEach( (vector, j) => {
          box.push(bearingPoint(line[i], bearing + vector, distance));
        });
      });
      return box;
    };

    function boundingBox(lineBox){
      // var bounds = geolib.getBounds([
      //     { latitude: lineBox[0][1], longitude: lineBox[0][0] },
      //     { latitude: lineBox[1][1], longitude: lineBox[1][0] },
      //     { latitude: lineBox[2][1], longitude: lineBox[2][0] },
      //     { latitude: lineBox[3][1], longitude: lineBox[3][0] }
      // ]);
      // return([[bounds.minLng,bounds.minLat],[bounds.maxLng,bounds.minLat]]);

      var SW = [90,180];
      var NE = [0,0];
      lineBox.forEach( corner => {
        if (corner[0] < SW[0]) {SW[0] = corner[0]};
        if (corner[1] < SW[1]) {SW[1] = corner[1]};
        if (corner[0] > NE[0]) {NE[0] = corner[0]};
        if (corner[1] > NE[1]) {NE[1] = corner[1]};
      });
      // console.log("Linebox", lineBox);
      // console.log("Box", [SW,NE]);
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

    function showBoxes(coordinates) {
      var newList = {
          "type": "FeatureCollection",
          "features": []
      };
      var lineBox;

      coordinates.forEach( (point, i) => {
          if (i < coordinates.length-1) {
            // Bounding Boxes
            // box = boundingBox(distantLineBox([coordinates[i],coordinates[i+1]],maxChargerDistance));
            // lineBox = [box[0], [box[0][0],box[1][1]], box[1], [box[1][0],box[0][1]] ,box[0]];

            // Boxes aloung Route
            lineBox = distantLineBox([coordinates[i],coordinates[i+1]],maxChargerDistance);
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
      // console.log('distantBox:',newList.features[0].geometry.coordinates);
      map.getSource('distantBox').setData(newList);
    };

    // - - - - - mapBox requests - - - - - -
    function getRoute(start,destination,route){  // set route = true if we need route coordinates
       var routeUrl = 'https://api.mapbox.com/directions/v5/mapbox/driving/'
          + start.longitude + ',' + start.latitude + ';'
          + destination.longitude + ',' + destination.latitude
          + '?access_token=' + mapboxgl.accessToken + (route ? '&geometries=polyline&overview='+route : '&overview=false');
      result = httpGet(routeUrl)
      // console.log("Result" + result);
      if (result) {
        result = JSON.parse(result);
        if (result.code == "Ok") {
          return {
            'distanceRaw': result.routes[0].distance/1000,
            'distance': (result.routes[0].distance/1000).toFixed((result.routes[0].distance < 10000) ? 1 : 0).toString().replace(".",",")  + ' km',
            'duration': secondsToTime(result.routes[0].duration),
            'durationRaw': result.routes[0].duration,
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
    function getChargerDetails(id) {
      var geUrl = 'https://api.goingelectric.de/chargepoints/?'+
        `key=${goingelectricToken}&`+
        `ge_id=${id}`;
      return JSON.parse(httpGet(geUrl));
    };

    function getChargersInBoundingBox(boundingBox, minPower) {
      var geUrl = 'https://api.goingelectric.de/chargepoints/?'+
        `key=${goingelectricToken}&`+
        `plugs=${compatiblePlugs}&min_power=${minPower}&`+
        `sw_lat=${boundingBox[0][1]}&sw_lng=${boundingBox[0][0]}&`+
        `ne_lat=${boundingBox[1][1]}&ne_lng=${boundingBox[1][0]}`;
      return JSON.parse(httpGet(geUrl));
    };

    function getChargersInBounds(searchField) {
      var geUrl = 'https://api.goingelectric.de/chargepoints/?'+
        `key=${goingelectricToken}&`+
        `plugs=${compatiblePlugs}&min_power=${minPower}&`+
        `ne_lat=${searchField.getNorthEast().lat}&ne_lng=${searchField.getNorthEast().lng}&`+
        `sw_lat=${searchField.getSouthWest().lat}&sw_lng=${searchField.getSouthWest().lng}`;
      return JSON.parse(httpGet(geUrl));
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
            "parkCharger",

          "coordinates": chargeLocation.coordinates,
          "chargepoints": chargeLocation.chargepoints,
          "name": chargeLocation.name,
          "street": chargeLocation.address.street,
          "city": chargeLocation.address.city,
          "country": chargeLocation.address.country,
          "network": chargeLocation.network,
          "operator": chargeLocation.operator,
          "count" : maxChargePoint.count,
          "power" : maxChargePoint.power,
          "type" : maxChargePoint.type,
          "url": chargeLocation.url,
          "distance" : includeDistance ? route.distance : false,
          "duration" : includeDistance ? route.duration : false,
          "range" : (includeDistance & teslaPosition.range) ? teslaPosition.range - route.distanceRaw : false
        },
        "geometry": {
          "type": "Point",
          "coordinates": [chargeLocation.coordinates.lng, chargeLocation.coordinates.lat]
        }
      };
    };

    function getRouteChargers(coordinates) {
      var checkList = [];
      var newList = {
          "type": "FeatureCollection",
          "features": []
      };
      var lineBox, chargerList;

      coordinates.forEach( (point, i) => {
          if (i < coordinates.length-1) {
            lineBox = distantLineBox([coordinates[i],coordinates[i+1]],maxChargerDistance);

            chargerList = getChargersInBoundingBox(boundingBox(lineBox),superCharger.minPower);
            if (chargerList.status != "ok") {throw "GoingElectric request failed"};
            if (chargerList.startkey == 500) {console.log("More than 500 chargers in area");}

            chargerList.chargelocations.forEach(chargeLocation => {
              if (!checkList.includes(chargeLocation.ge_id)) {
                if (pointIsInBox([chargeLocation.coordinates.lng, chargeLocation.coordinates.lat],lineBox)) {
                  console.log(chargeLocation.ge_id, chargeLocation.name, chargeLocation.address.city);
                  checkList.push(chargeLocation.ge_id);
                  newList.features.push(chargeLocationDetails(chargeLocation,true));
                }
              }
            });
          };
      });
      return newList;
    };

    function updateChargers() {
      var chargerList = getChargersInBounds(map.getBounds())
      console.log("GE Reply: ", chargerList);
      if (chargerList.status != "ok") {throw "GoingElectric request failed"};
      if (chargerList.startkey == 500) {console.log("More than 500 chargers in area");}

      var newList = {
          "type": "FeatureCollection",
          "features": []
      };
      chargerList.chargelocations.forEach(chargeLocation => {
        newList.features.push(chargeLocationDetails(chargeLocation));
      });
      map.getSource('chargers').setData(newList);
    };

    function chargerShortDescription (chargeLocation) {
      var address = `${chargeLocation.street}, ${chargeLocation.city}, ${chargeLocation.country}`;

      var description = '';
      description = `<strong>${chargeLocation.name} ${chargeLocation.name.includes(chargeLocation.city) ? '' : chargeLocation.city}</strong>`;

      description += (chargeLocation.network && chargeLocation.network != chargeLocation.name && chargeLocation.network != chargeLocation.name + ' ' + chargeLocation.city) ?
                     (`<br>${chargeLocation.network}<p>`) :
                     (chargeLocation.operator && chargeLocation.operator != chargeLocation.name && chargeLocation.operator != chargeLocation.name + ' ' + chargeLocation.city) ?
                     `<br>${chargeLocation.operator}<p>` :
                     '<p>'
                     // <span id='A'></span>;
                     // document.getElementById('A').innerHTML="oijoij"

      description += `${chargeLocation.count}x ${chargeLocation.power} kW ${chargeLocation.type}<p>`;
      description += '<hr>';
      description += `${chargeLocation.street}<br>${chargeLocation.city}<p>`;

      // description += `<a href=${chargeLocation.url} target="_blank">Details auf GoingElectric</a><p>`;
      // description += `<a href=# onclick='sendDestinationToTesla("${address}");'>Als Navigationsziel setzen</a>`;

      description += `<div class="twocolumns"><a class="popupbutton popupbutton-icon-navigate" href="#" onclick="sendDestinationToTesla('${address}'); return false;"></a><a class="popupbutton popupbutton-icon-link" href="http://${chargeLocation.url}" target="_blank"></a></div>`;

      return {'text': description, 'address': address};
    };

    function chargerDescription (id) {
      var chargerDetails = getChargerDetails(id);
      if (chargerDetails.status != "ok") {throw "GoingElectric request failed"};
      var chargeLocation = chargerDetails.chargelocations[0];

      var route = getRoute(teslaPosition,{'longitude' : chargeLocation.coordinates.lng, 'latitude' : chargeLocation.coordinates.lat});
      var address = `${chargeLocation.address.street}, ${chargeLocation.address.city}, ${chargeLocation.address.country}`;

      var description = '';
      description = `<strong>${chargeLocation.name} ${chargeLocation.name.includes(chargeLocation.address.city) ? '' : chargeLocation.address.city}</strong>`;

      description += (chargeLocation.network && chargeLocation.network != chargeLocation.name && chargeLocation.network != chargeLocation.name + ' ' + chargeLocation.address.city) ?
                     (`<br>${chargeLocation.network}<p>`) :
                     (chargeLocation.operator && chargeLocation.operator != chargeLocation.name && chargeLocation.operator != chargeLocation.name + ' ' + chargeLocation.address.city) ?
                     `<br>${chargeLocation.operator}<p>` :
                     '<p>';

      var maxChargePoint = getMaxChargePoint(chargeLocation.chargepoints);
      description += `${maxChargePoint.count}x ${maxChargePoint.power} kW ${maxChargePoint.type}`;
      // description += `${chargeLocation.count}x ${chargeLocation.power} kW ${chargeLocation.type}`;
      description += (chargeLocation.location_description) ? (`<br>${chargeLocation.location_description}<p>`) : '<p>';
      description += (chargeLocation.fault_report) ? (`<strong>Störung:</strong> ${chargeLocation.fault_report.description}<p>`) : '';
      description += '<hr>';
      // description += (chargeLocation.general_information) ? (`Hinweis: ${chargeLocation.general_information}<br>`) : '';

      description += (chargeLocation.ladeweile) ? (`Ladeweile: ${chargeLocation.ladeweile}<p>`) : '';
      description += `${chargeLocation.address.street}<br>${chargeLocation.address.city}<p>`;

      if (route) {
        description += '<strong>' + route.distance + ', ' + route.duration + '</strong>';

        try {
          var rangeAtArrival = (milesToKm(getTeslaChargeStatus().response.est_battery_range).kmRaw - route.distanceRaw).toFixed()
          description += `<br>${rangeAtArrival<10?'<span class="mapboxgl-popup-content-warning">':''}Reichweite bei Ankunft ${rangeAtArrival} km${rangeAtArrival<10?'</span">':''}`;
      }
        catch {};
        description += '<p>'
      };
      // description += `<a href=${chargeLocation.url} target="_blank">Details auf GoingElectric ${id}</a><p>`;
      // description += `<a href=# onclick='sendDestinationToTesla("${address}");'>Als Navigationsziel setzen</a>`;

      description += `<div class="twocolumns"><a class="popupbutton popupbutton-icon-navigate" href="#" onclick="sendDestinationToTesla('${address}'); return false;"></a><a class="popupbutton popupbutton-icon-link" href="http://${chargeLocation.url}" target="_blank"></a></div>`;

      return {'text': description, 'address': address};
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
