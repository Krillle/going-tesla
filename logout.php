

<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8" />
		<title>Tesla Logout</title>
  <?php ?>
  <script>

      var teslaConnection = {'accessToken': getCookie('access'),'refreshToken': getCookie('refresh'), 'vehicle': getCookie('vehicle'), 'status': 'undefined' };
      revokeTeslaToken()
      console.log("Tesla Access Token widerrufen");

      document.cookie = "access=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //  Access Cookie löschen
      document.cookie = "refresh=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //  Refresh Cookie löschen
      document.cookie = "vehicle=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //  Legacy Tesla Toke Cookie löschen
      document.cookie = "token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //  Legacy Tesla Toke Cookie löschen
      console.log("Tesla Token Cookies entfernt");

      function revokeTeslaToken() {
        console.log('Set destination: ' + destination);
        var teslaUrl = 'https://goingtesla.herokuapp.com/corsproxy.php?'
        + 'csurl=https://owner-api.teslamotors.com/oauth/token';

        var body = JSON.stringify({
          "token": teslaConnection.accessToken
        });

        var xhr = new XMLHttpRequest();
        xhr.withCredentials = true;

        xhr.addEventListener("readystatechange", function () {
          if (this.readyState === 4) {
            console.log('Token revoked: ' + this.responseText);
          }
        });

        xhr.open("POST", teslaUrl);

        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.setRequestHeader("Authorization", 'bearer ' + teslaConnection.accessToken);
        xhr.setRequestHeader("cache-control", "no-cache");

        xhr.send(body);

      };

  </script>
	</head>
	<body>
		<p id="done">Tesla Token widerrufen.<br>Token aus Cookies gelöscht.<br>Logout erfolgreich.</p>
	</body>
</html>
