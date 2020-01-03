<?php

/* calculate the sunset time for Lisbon, Portugal
Latitude: 52.52 North
Longitude: 13.41 East
Zenith ~= 90
offset: +1 GMT
*/

echo date("D M d Y"). ', sunset time : ' .date_sunset(time(), SUNFUNCS_RET_STRING, 52.52, 13.41, 90, 1);

?>
