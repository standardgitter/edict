<?php
set_time_limit(0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "https://github.com/standardgitter/edict/blob/main/ecdict.sqlite3?raw=true");
$fp = fopen ('ecdict.sqlite3', 'w+');
curl_setopt($curl, CURLOPT_FILE, $fp);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
//curl_setopt($curl,CURLOPT_TIMEOUT,50);
curl_exec($curl);
curl_close($curl);
fclose($fp);

?>

