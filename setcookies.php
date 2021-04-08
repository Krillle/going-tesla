<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8" />
		<title>Tesla Connection</title>
  <?php ?>
  <script>

	function getCookie(name) {
    var value = "; " + document.cookie;
	  var parts = value.split("; " + name + "=");
	  if (parts.length == 2) {
			return parts.pop().split(";").shift()
		} else {
	    return false
	  };
	};

  function setCookies () {

    document.cookie = 'access=' + document.getElementById('accessToken').value + '; expires=Thu, 10 Aug 2022 12:00:00 UTC";';
    console.log("Access: " + document.getElementById('accessToken').value);
    document.cookie = 'refresh=' + document.getElementById('refreshToken').value + '; expires=Thu, 10 Aug 2022 12:00:00 UTC";';
    console.log("Refresh: " + document.getElementById('refreshToken').value);
    document.cookie = 'vehicle=' + document	.getElementById('vehicle').value + '; expires=Thu, 10 Aug 2022 12:00:00 UTC";';
    console.log("Vehicle: " + document.getElementById('vehicle').value);

  }

	function presets() {
		console.log("Presets started")
		document.getElementById("accessToken").value = getCookie('access') || "";
		document.getElementById("refreshToken").value = getCookie('refresh') || "";
	 	document.getElementById("vehicle").value = getCookie('vehicle') || "";
	}

	function setToken() {
		console.log("Set Token started")
		document.getElementById("accessToken").value = '<?php echo $_ENV["token"]; ?>' || "";
		document.getElementById("refreshToken").value = getCookie('refresh') || "";
	 	document.getElementById("vehicle").value = getCookie('vehicle') || "";
	}

	// document.onload = presets()

  </script>
	<style>
	 p {
    font: 200 20px/1.15 'Gotham Medium', 'Verdana', 'Source Sans Pro', 'Helvetica Neue', Sans-serif;
    color: #8F8F8F;
	}
	 a.popupbutton {
    font: 400 20px/1.15 'Gotham Medium', 'Verdana', 'Source Sans Pro', 'Helvetica Neue', Sans-serif;
    color: #8F8F8F;
	}
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
	    background: #d6d6d6;
	    margin: 0px 0;
	    line-height: 1;
	    break-inside: avoid-column;
	    page-break-inside: avoid;
	}
	</style>
	</head>
	<body>
    <h1>Tesla Connection</h1>
		<form>
		  <div class="form-group">
		    <label>Access Token *</label> <input type="text" class="form-control" id="accessToken" placeholder="" required="required">
		  </div>

		  <div class="form-group">
		    <label>Refresh Token</label> <input type="text" class="form-control" id="refreshToken" placeholder="">
		  </div>

		  <div class="form-group">
		    <label>Vehicle</label> <input type="text" class="form-control" id="vehicle" placeholder="">
		  </div>
			<div><a class="popupbutton" href="#" style="width: 280px;" onclick="setToken(); return false;">Add Token</a></div>;
			<div><a class="popupbutton" href="#" style="width: 280px;" onclick="presets(); return false;">Load Cookies</a></div>;
			<div><a class="popupbutton" href="#" style="width: 280px;" onclick="setCookies(); return false;">Set Cookies</a></div>;
			<div><small>Felder markiert mit * sind Pflichtfelder.</small></div>
		</form>
	</body>
</html>
