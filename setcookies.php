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
		document.getElementById("accessToken").value = '<?php echo $_ENV["access"]; ?>' || "";
		document.getElementById("refreshToken").value = '<?php echo $_ENV["refresh"]; ?>' || "";
	 	document.getElementById("vehicle").value = getCookie('vehicle') || "";
	}

	// document.onload = presets()

  </script>
	<style>

	body {
		margin:20;
		padding:20;
	}
	h1 {
			font: 400 20px/1.15 'Gotham Medium', 'Verdana', 'Source Sans Pro', 'Helvetica Neue', Sans-serif;
	    color: #8F8F8F;
			text-transform: uppercase;
	}

	#formContainer{
	  width:40%;
	  margin:auto;
	}

	#formC{
	  width:100%;
	}

	.rows{
	  width:100%;
	  display:block;

	}
	.column{
	    width:100%;
	    display:inline-block;

	}
	label {
		width:30%
		float:left;
		font: 200 18px/1.15 'Gotham Medium', 'Verdana', 'Source Sans Pro', 'Helvetica Neue', Sans-serif;
    color: #8F8F8F;
	}

	input {
		width:60%;
  	float:right;
		font: 200 18px/1.15 'Gotham Medium', 'Verdana', 'Source Sans Pro', 'Helvetica Neue', Sans-serif;
    color: #8F8F8F;
		height: 30px;
		width: 750px;

	}

	a.popupbutton {
			font: 400 20px/1.15 'Gotham Medium', 'Verdana', 'Source Sans Pro', 'Helvetica Neue', Sans-serif;
	    color: #8F8F8F;
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
		<form id="formC">
	  <div class="rows">
		  <div class="column">
		    <label>Access Token</label> <input type="text" class="form-control" id="accessToken" placeholder="" required="required">
		  </div>

		  <div class="column">
		    <label>Refresh Token</label> <input type="text" class="form-control" id="refreshToken" placeholder="">
		  </div>

		  <div class="column">
		    <label>Vehicle</label> <input type="text" class="form-control" id="vehicle" placeholder="">
		  </div>
			<p></p>
			<div><a class="popupbutton" href="#" style="width: 280px;" onclick="setToken(); return false;">Add Token</a></div>
			<p></p>
			<div><a class="popupbutton" href="#" style="width: 280px;" onclick="presets(); return false;">Load Cookies</a></div>
			<p></p>
			<div><a class="popupbutton" href="#" style="width: 280px;" onclick="setCookies(); return false;">Set Cookies</a></div>
			<p></p>
		</div>
		</form>
	</body>
</html>
