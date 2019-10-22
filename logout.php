

<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8" />
		<title>Tesla Logout</title>
  <?php ?>
  <script>
      document.cookie = "access=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //  Access Cookie löschen
      document.cookie = "refresh=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //  Refresh Cookie löschen
      document.cookie = "vehicle=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //  Vehicle Toke Cookie löschen
      document.cookie = "token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //  Legacy Tesla Toke Cookie löschen
      console.log("Tesla Token Cookies entfernt");
  </script>
	</head>
	<body>
		<p id="done">Tesla Token entfernt. Logout erfolgreich.</p>
	</body>
</html>
