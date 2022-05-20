<?php

$coingeckoapi = "https://api.coingecko.com";
$cachepath = "cache/";
if(!is_dir($cachepath)) {
	mkdir($cachepath);
}

function url_get_contents($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

function validateJSON(string $json): bool {
    try {
        $test = json_decode($json);
        return true;
    } catch  (Exception $e) {
        return false;
    }
}

$uri = $_SERVER["REQUEST_URI"];
$hashfile = md5($uri);
$cachefile = $cachepath.$hashfile;

$getfresh = FALSE;
if (file_exists($cachefile)) {
	$filemtime = filemtime($cachefile);		
	if( (time() - $filemtime) > 60) {
		$getfresh = TRUE;
	}
}
else
{
	$getfresh = TRUE;
}

if ($getfresh) {
	$contents = url_get_contents($coingeckoapi.$uri);
	$valid = validateJSON($contents);	
	if($valid) {
		// VERFIY VALID VALUES FOR ANY SPECIFIC PATHS (better old data than invalid data)
		switch($uri) {
			case "/api/v3/coins/0chain?localization=false":
				$json = json_decode($contents, TRUE);
				if($json["market_data"]["current_price"]["usd"] > 0) {
					$valid = TRUE;
				}
				else
				{
					$valid = FALSE;
				}
				break;
		}
	}
	if($valid) {
		file_put_contents($cachefile, $contents);
	}
	else
	{
		file_put_contents($cachefile.".err", $contents);		
	}
	file_put_contents($cachefile.".log", $uri);
}

header('Content-Type: application/json');
echo file_get_contents($cachefile);
