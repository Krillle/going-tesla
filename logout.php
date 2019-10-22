

<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8" />
		<title>Tesla Logout</title>
  <?php ?>
  <script>

      var teslaConnection = {'accessToken': getCookie('access'),'refreshToken': getCookie('refresh'), 'vehicle': getCookie('vehicle'), 'status': 'undefined' };
      console.log("Requesting Revoke Tesla Access Token");
      try {revokeTeslaToken()}
      catch {console.log('Revoking token not successful')};

      // document.cookie = "access=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //  Access Cookie löschen
      // document.cookie = "refresh=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //  Refresh Cookie löschen
      // document.cookie = "vehicle=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //  Legacy Tesla Toke Cookie löschen
      // document.cookie = "token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //  Legacy Tesla Toke Cookie löschen
      console.log("Tesla Token Cookies entfernt");

      function getCookie(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length == 2) return parts.pop().split(";").shift();
      };

      function revokeTeslaToken() {
        console.log('Revoke Tesla Token: ' + teslaConnection.accessToken);
        var teslaUrl = 'https://goingtesla.herokuapp.com/corsproxy.php?'
        + 'csurl=https://owner-api.teslamotors.com/oauth/revoke';

        var body = JSON.stringify({
          "token": teslaConnection.accessToken
        });

        var xhr = new XMLHttpRequest();
        // xhr.withCredentials = true;

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
