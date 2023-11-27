<?php
//$jsonData = file_get_contents('http://localhost/2023-GP1-mid/2023-GP1-12-merges%202/2023-GP1-12-merges/Learniverse/Notes/index.php');
//print_r($jsonData);

function curl($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

echo curl('http://localhost/2023-GP1-mid/2023-GP1-12-merges%202/2023-GP1-12-merges/Learniverse/Notes/index.php');