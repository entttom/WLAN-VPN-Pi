<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>rasAP</title>
</head>
<?php
// perform WPS
if ($_POST['wps'] == "go") {
  shell_exec('sudo wpa_cli wps_pbc -i wlan1');
  echo "<big><big>WPS active</big></big>";
}
// start VPN
if ($_POST['start'] == "go") {
  shell_exec('sudo /home/pi/start-vpn.sh');
  echo "<big><big>Start VPN</big></big>";
}
// stop VPN
if ($_POST['stop'] == "go") {
  shell_exec('sudo /home/pi/stop-vpn-common.sh');
  echo "<big><big>Stop VPN</big></big>";
}
// Reset WIFI
if ($_POST['wlan1'] == "go") {
  shell_exec('sudo ifdown --force wlan1 && sudo ifup wlan1');
  echo "<big><big>Reset Host WLAN</big></big>";
}
// add wifi to /etc/wpa_supplicant/wpa_supplicant.conf
if ($_POST['addwifi'] == "go") {
  if($_POST['addwifiname2'] == "choose network or type networkname ->") 
  {$wifiname = $_POST['addwifiname'];}
  else {$wifiname = $_POST['addwifiname2'];}
  shell_exec('sudo echo "" >> /etc/wpa_supplicant/wpa_supplicant.conf');
  shell_exec('sudo echo "" >> /etc/wpa_supplicant/wpa_supplicant.conf');
  shell_exec('sudo echo "network={" >> /etc/wpa_supplicant/wpa_supplicant.conf');
  shell_exec('sudo echo "        ssid=\"' . $wifiname . '\"" >> /etc/wpa_supplicant/wpa_supplicant.conf');
  if ($_POST['secure'] == "WPA_WPA2") { shell_exec('sudo echo "        psk=\"' . $_POST['addwifipasswort'] . '\"" >> /etc/wpa_supplicant/wpa_supplicant.conf'); }
  if ($_POST['secure'] == "WPA_WPA2") { shell_exec('sudo echo "        proto=RSN" >> /etc/wpa_supplicant/wpa_supplicant.conf'); }
  if ($_POST['secure'] == "WPA_WPA2") { shell_exec('sudo echo "        key_mgmt=WPA-PSK" >> /etc/wpa_supplicant/wpa_supplicant.conf'); }
  if ($_POST['secure'] == "WPA_WPA2") { shell_exec('sudo echo "        pairwise=CCMP" >> /etc/wpa_supplicant/wpa_supplicant.conf'); }
  if ($_POST['secure'] == "WPA_WPA2") { shell_exec('sudo echo "        auth_alg=OPEN" >> /etc/wpa_supplicant/wpa_supplicant.conf'); }
  if ($_POST['secure'] == "unsecure") { shell_exec('sudo echo "        key_mgmt=NONE" >> /etc/wpa_supplicant/wpa_supplicant.conf'); }
  if ($_POST['hidden'] == "go") { shell_exec('sudo echo "        wpa-ap-scan 1" >> /etc/wpa_supplicant/wpa_supplicant.conf'); }
  if ($_POST['hidden'] == "go") { shell_exec('sudo echo "        wpa-scan-ssid 1" >> /etc/wpa_supplicant/wpa_supplicant.conf'); }
  shell_exec('sudo echo "}" >> /etc/wpa_supplicant/wpa_supplicant.conf');
  shell_exec('sudo ifdown --force wlan1 && sudo ifup wlan1');
  echo "<big><big>Wifi added!</big></big>";
}
// shutdown
if ($_POST['shutdown'] == "go") {
  shell_exec('sudo shutdown -h now');
  echo "<big><big>Shutdown!</big></big>";
}
// reboot
if ($_POST['reboot'] == "go") {
  shell_exec('sudo reboot');
  echo "<big><big>Reboot!</big></big>";
}
// free all post variables
unset($_POST);
?>


<body>
<br>
<form action="index.php" method="post"> <p><input type="submit" value="  REFRESH PAGE  "/></p></form>
IP address & SSID foreign network - wifi:

<?php
$output = str_replace(array("\r\n", "\r", "\n"), ' ', substr(shell_exec('sudo ifconfig wlan1 | grep "inet addr:" | cut -d: -f2 | awk "{ print $1}"'), 0, -8) . " " . shell_exec('sudo wpa_cli -i wlan1 status | grep "ssid="'));
echo "<pre>$output</pre>";
?><form action="index.php" method="post"><input type="hidden" name="wlan1" value="go"/><input type="submit" value="  RESET   "/></form> (hit for refreshing WIFI nearby)
<br><br>
IP address & SSID foreign network - lan:
<?php
$output = str_replace(array("\r\n", "\r", "\n"), ' ', substr(shell_exec('sudo ifconfig eth0 | grep "inet addr:" | cut -d: -f2 | awk "{ print $1}"'), 0, -8));
echo "<pre>$output</pre>";
?>
<br>
IP address & SSID own network - wifi:
<?php
$output = str_replace(array("\r\n", "\r", "\n"), ' ', substr(shell_exec('sudo ifconfig wlan0 | grep "inet addr:" | cut -d: -f2 | awk "{ print $1}"'), 0, -8));
echo "<pre>$output</pre>";
?>
<br>
IP address tun0:
<?php
$output = str_replace(array("\r\n", "\r", "\n"), ' ', substr(shell_exec('sudo ifconfig tun0 | grep "inet addr:" | cut -d: -f2 | awk "{ print $1}"'), 0, -8));
echo "<pre>$output</pre>";
?>
<br>
WIFI nearby
<?php
$output = shell_exec('sudo wpa_cli scan_result -i wlan1');
echo "<pre>$output</pre>";
$array = preg_split( '/[\s]+/', $output );

//count array elements number of options
$i = 14;
$j = 0;
while ($j==0):
if ($array[$i] == "")
{$j = 1;}
else
{$i++;}
endwhile;
$j = 14;
?>
<form action="index.php" method="post">
<input type="hidden" name="addwifi" value="go"/>
    <select name="addwifiname2" size="1">
      <option selected>choose network or type networkname -></option>
<?php
// list of networks found
while ($j <= $i):
echo "<option>$array[$j]</option>";
$j = $j +5;
endwhile;
?>
    </select>
<input type="text" name="addwifiname" value="type manual Wifi Name"/><br>
<input type="text" name="addwifipasswort" value="password"/><br>
    <select name="secure" size="1">
      <option selected>WPA_WPA2</option>
      <option>unsecure</option>
    </select><br>
Hidden network?<input type="checkbox" name="hidden" value="go"><br>
<input type="submit" value="  ADD WIFI   "/></form>
<br>
WPS BUTTON<br>
<br>
<form action="index.php" method="post"><input type="hidden" name="wps" value="go"/> <p><input type="submit" value="  WPS   "/> (RESET when you think connection is established)</p></form>
<br>
VPN<br>
<br>
<form action="index.php" method="post"><input type="hidden" name="start" value="go"/> <p><input type="submit" value="  START   "/></p></form>
<form action="index.php" method="post"><input type="hidden" name="stop" value="go"/> <p><input type="submit" value="  STOP   "/></p></form>
<br>
<br>
 <br>
<form action="index.php" method="post"><input type="hidden" name="shutdown" value="go"/> <p><input type="submit" value="  SHUTDOWN   "/></p></form>
<form action="index.php" method="post"><input type="hidden" name="reboot" value="go"/> <p><input type="submit" value="  REBOOT   "/></p></form>
</body>
</html>
