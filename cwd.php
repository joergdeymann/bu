<?php
echo getcwd();
echo "<br>";
echo __FILE__;
echo "<br>";
echo $_SERVER['REQUEST_URI'];
echo "<br>";
echo $_SERVER['HTTP_HOST'];
echo "<br>";


	$url= splitURL();

	echo $url['protocol']."://".$url['dirname']."/".$url['filename'].".".$url['extension']."<br>";
	echo "CWD:".$url['protocol']."://".$url['dirname']."<br>";
	


function splitURL() {
		$protocol=((empty($_SERVER['HTTPS'])) ? 'http' : 'https');
		$path=$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$url=$protocol .'://'. $path;
		
		
		$pieces 	= parse_url($url);
		// $protocol   = $pieces['scheme']; // enthält "http"
		// $host  		= $pieces['host']; // enthält "www.example.com"
		// $path  		= $pieces['path']; // enthält "/dir/dir/file.php"
		// $query 		= $pieces['query']; // enthält "arg1=foo&arg2=bar"
		// $fragment 	= $pieces['fragment']; // ist leer, da getCurrentUrl() diesen Wert nicht zurückgibt

		$path=pathinfo($path); // Beispiel /www/htdocs/inc/lib_inc.php
								// dirname /www/htdocs/inc
								// basename  lib_inc.php
								// extension php 
								// filename lib_inc
								
		$pieces['protocol']=$protocol;
		foreach($path as $k => $v) {
			$pieces[$k]=$v;
		}
		$pieces['url']=$url;
		$pieces['host']=$_SERVER['HTTP_HOST'];
		
		
		return $pieces;
}
			

/*
    function getHostSplitted() {
        $matches = array();
        if (substr_count($_SERVER['HTTP_HOST'], '.')==1) {
            preg_match('/^(?P<d>.+)\.(?P<tld>.+?)$/', $_SERVER['HTTP_HOST'], $matches);
        } else {
            preg_match('/^(?P<sd>.+)\.(?P<d>.+?)\.(?P<tld>.+?)$/', $_SERVER['HTTP_HOST'], $matches);
        }
        return array(0=>(isset($matches['sd'])?$matches['sd']:''), 1=>$matches['d'], 2=>$matches['tld']);
    }
 
    function getSubdomain() {
        list($subdomain) = getHostSplitted();
        return $subdomain;
    }
 
    function getHost() {
        list(,$host) = getHostSplitted();
        return $host;
    }
 
    function getTld() {
        list(,,$tld) = getHostSplitted();
        return $tld;
    }
*/
?>
