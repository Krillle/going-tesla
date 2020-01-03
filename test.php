<?php

/* calculate the sunset time for Lisbon, Portugal
Latitude: 52.52 North
Longitude: 13.41 East
Zenith ~= 90
offset: +1 GMT
*/

echo date("D M d Y"). ', current time : ' .time();
echo '<br>';
echo date("D M d Y"). ', sunset time : ' .date_sunset(time()-10*24 * 60 * 60, SUNFUNCS_RET_TIMESTAMP, 52.52, 13.41, 90, 1);

?>
