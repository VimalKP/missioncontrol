<?php

//echo getSource('https://twitter.com/Quiznos/status/353809921487278081');
$type = $_REQUEST['type'];
$handle = $_REQUEST['handle'];
$source = '';
if ($type == 'facebook') {
    $source = 'http://www.facebook.com/ford';
} else if ($type == 'twitter') {
    $source = 'http://www.twitter.com/ford';
} else if($type == 'youtube'){
    $source = 'http://www.youtube.com/ford';
}
echo getSource("$source");

function getSource($url) {

    //$proxy='211.140.189.247:80';
    $count = 0;
    $buffer = '';
    while (!$buffer) {
        $count++;
        try {
            $refer = "http://www.portalinmobiliario.com/Catalogo/Fichas.asp?ProyectoID" . substr($url, strpos($url, "="));
            $refer = 'http://www.portalinmobiliario.com/Buscador.asp?Pref=1';
            $setHeaders = array(
                'User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.121 Safari/535.2',
                'Accept: text/plain',
                'Accept-Language: en-US,en;q=0.8',
                'Accept-Charset: utf-8,ISO-8859-1;q=0.7,*;q=0.3',
                'Accept-Encoding: ',
                'Referer: ' . $refer
            );

            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_HTTPHEADER, $setHeaders);
            //curl_setopt($handle, 	CURLOPT_VERBOSE, 			TRUE				);
            curl_setopt($handle, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
            curl_setopt($handle, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 120);
            curl_setopt($handle, CURLOPT_TIMEOUT, 120);
            //curl_setopt($handle, 	CURLOPT_STDERR, 			'error_log.txt'		);
            //curl_setopt($handle, 	CURLOPT_COOKIE, 			$sessionID			);
            //curl_setopt($handle, 	CURLOPT_PROXY, 				$proxy				);

            try {
                set_time_limit(0);
                $buffer = curl_exec($handle);
                if (!$buffer) {
                    echo curl_error($handle) . '\r\n';
                }
                if ($buffer === FALSE) {
                    throw new Exception();
                }
            } catch (Exception $ex) {
                echo 'curl_exec exception \r\n'; //exit;
                echo $ex->getMessage() . '\r\n';
            }

            curl_close($handle);
            if (empty($buffer)) {
                return "EMPTY";
            } else {
                return $buffer;
            }
        } catch (Exception $ex) {
            echo $ex->getMessage() . '\r\n';
            $buffer = '';
        }
        if ($count > 5) {
            return "EMPTY";
            break;
        }
    }
}

?>