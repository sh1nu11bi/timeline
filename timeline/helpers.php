<?php
		#### This is the helper files with all helper functions ###

		function get_all_integrations(){
			// $url = "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/api/integrations/getAll";
			$scheme = $_SERVER['REQUEST_SCHEME'] . '://';
			$domainName = $_SERVER['HTTP_HOST'].'/';
			$url = $scheme . $domainName . 'api/integrations/getAll';			
        	$ch = curl_init();
        	$timeout = 5;
    		curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$html = curl_exec($ch);
			return json_decode($html);
		}

?>
