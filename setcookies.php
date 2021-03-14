

<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8" />
		<title>Tesla Logout</title>
  <?php ?>
  <script>

  function setCookies () {
    document.cookie = 'access=' + teslaConnection.accessToken + '; expires=Thu, 10 Aug 2022 12:00:00 UTC";';
    console.log("Access: " + teslaConnection.accessToken);
    document.cookie = 'refresh=' + teslaConnection.refreshToken + '; expires=Thu, 10 Aug 2022 12:00:00 UTC";';
    console.log("Refresh: " + teslaConnection.refreshToken);
    document.cookie = 'vehicle=' + teslaConnection.vehicle + '; expires=Thu, 10 Aug 2022 12:00:00 UTC";';
    console.log("Vehicle: " + teslaConnection.vehicle);

  }

  </script>
	</head>
	<body>
    <h1><a>Tesla Connetion</a></h1>
		<form>
		  <div class="form-group">
		    <label>Access Token *</label> <input type="text" class="form-control" name="accessToken" placeholder="" required="required">
		  </div>

		  <div class="form-group">
		    <label>Refresh Token</label> <input type="text" class="form-control" name="refreshToken" placeholder="">
		  </div>

		  <div class="form-group">
		    <label>Vehicle</label> <input type="text" class="form-control" name="vehicle" placeholder="">
		  </div>

		  <div class="form-group">
		    <input type="submit" class="btn btn-primary" name="saveButton" value="Save" onclick="setCookies ()">
		  </div><small>Felder markiert mit * sind Pflichtfelder.</small>
		</form>
	</body>
</html>
