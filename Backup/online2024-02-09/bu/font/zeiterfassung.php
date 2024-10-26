<?php
// info();
// exit;
//  echo $_SERVER['SERVER_ADDR'];
// echo $_SERVER['SERVER_NAME'];
// exit;
 
include "dbconnect.php";
include "class/class_mitarbeiter.php";
include "class/class_zeiten.php";
include "class/class_firma.php";
include "class/class_cookie.php";

// echo "Hier";

// $_POST['user']="Jörg Deymann";

$worker	=new mitarbeiter($db);
$c		=new cookielogin();

// $c->clear(); // Cookies und Sesseion löschen

// if (isset($_COOKIE["username"])) {
// 	echo "Cookie ist da<br>";
// }

$msg="";
$display="login";

if ($c->login_cookie()) {
	$_POST['user']=$_COOKIE['username'];
	$worker->load($_POST['user']);
	$display="ok";
	// echo "cookie";
} else {
	$err=false;
	
	if (isset($_POST['pw1']) && isset($_POST['pw2'])) {
		// echo "PW1/2<br>";
		if ($_POST['pw1'] != $_POST['pw2']) {
			$msg="Die Passwörter stimmen nicht überein<br>";
			$err=true;
			$display="change";
		} else {
			$worker->load($_POST['user']);
			$worker->setPassword($_POST['pw1']);
			$display="OK";
		}
				
	}
	
	if (empty($_POST['user']) && isset($_POST['user'])) {
		$msg.="Benutzer nicht eingegeben<br>";
		$err=true;	
	}
	if (empty($_POST['pw']) && isset($_POST['pw'])) {
		$msg.="Benutzer nicht eingegeben<br>";
		$err=true;	
	}
	
	if (($err==false) && isset($_POST['user']))  {
		// changepw();
		$worker->load($_POST['user']);
		if ($worker->load($_POST['user'])) {
			if ($c->check($_POST['pw'],$worker->row['pw'])) {  // PW = Nutzer Standart
				if ($c->check($_POST['user'],$worker->row['pw'])) {  // PW = Nutzer erster login
					$display="change";
				} else 	{
					$display="OK";
				    $c->setCookie();  // nur setzten wenn login complett
				}
					
			} else {
				$msg="Passwort falsch";
				$err=true;
			}
		} else {
			$msg.="Benutzer ".$_POST['user']." nicht vorhanden!<br>";
			$err=true;
		}
		
	}
}

//	changepw($msg);exit;

if ($display=="login") {
	login($msg);
	exit;
}	
if ($display=="change") {
	changepw($msg);
	exit;
}	


function headers() {
echo '<html lang="de"><head>
<meta charset="utf-8">
<meta name="description" 
	  content="Stempeluhr und Zeiterfassung mit Urlaubs und Krankheitsabwesenheiten für jeden, mit Auswertungen">
<meta name="keywords" content="Stempeluhr, stempeln, Zeiterfassung, Krankheitserfassung, Urlaubserfassung">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Zeiterfassung</title>
<link rel="stylesheet" href="zeiterfassung.css">
</head>
';
}
function login($msg="") {
	headers();
	echo '<body><center>';
	echo '<h1>Zeiterfassung</h1>';
	echo $msg;
	echo '<form method="POST" action="zeiterfassung.php">';
	echo '<table>';
	echo '<tr><th>Name:</th><td><input type="text" size="50" name="user"></td></tr>';
	echo '<tr><th>Passwort:</th><td><input type="password" size="25" name="pw"></td></tr>';
	echo '<tr><th colspan=2><button name="OK">OK</th></tr>';
	echo '</table>';
	echo '</form>';
	echo '</center></body></html>';
}

function changepw($msg="") {
	headers();
	echo '<body><center>';
	echo '<h1>Zeiterfassung</h1>';
	echo 'Sie loggen sich das erste mal ein.<br>Bitte ändern sie Ihr Passwort<br>';		
	echo $msg;
	echo '<br>';
	echo '<form method="POST" action="zeiterfassung.php">';
	echo '<input type="hidden" name="user" value="'.$_POST['user'].'">';
	echo '<table border =1>';
	echo '<tr><th>Passwort:</th><td><input type="password" size="30" name="pw1"></td></tr>';
	echo '<tr><th>Passwort wiederholen:</th><td><input type="password" size="30" name="pw2"></td></tr>';
	echo '<tr><th colspan=2><button name="OK">OK</th></tr>';
	echo '</table>';
	echo '</form>';
	echo '</center></body></html>';
}

function info($msg="") {
	headers();
	echo '<body><center>';
	echo '<h1>Hallo <p>'.$_POST['user'].'</p></h1><br>';
	echo '<h2>Stunden von heute (19.06.2023)</h2>';

	echo '<table cellspacing=0 id="zeiten">';
	echo '<tr><th>&nbsp;</th><th>Tag</th><th>Woche</th><th>Monat</th><th>Gesamt</th></tr>';
	echo '<th style="text-align:left">Stunden</th><td>12</td><td>90</td><td>360</td><td>2000</td></tr>';
	echo '<th style="text-align:left">Überstunden</th><td>2</td><td>20</td><td>110</td><td>300</td></tr>';
	echo '</table>';

	echo '<h2>Wochenstunden vom Juni 2023</h2>';
	echo '<table cellspacing=0 id="zeiten">';
	echo '<tr><th>Woche</th><th>Stunden</th><th>Überstunden</th></tr>';
	echo '<tr><td>15</th><td>72</td><td>2</td></tr>';
	echo '<tr><td>16</th><td>70</td><td>0</td></tr>';
	echo '<tr><td>17</th><td>75</td><td>5</td></tr>';
	echo '</table>';

	echo '<h2>Monatsstunden von 2023</h2>';
	echo '<table cellspacing=0 id="zeiten">';
	echo '<tr><th>Monat</th>     	<th>Stunden</th><th>Überstunden</th></tr>';
	echo '<tr><th>Januar</td> 		<td>281</td><td>  1</td></tr>';
	echo '<tr><th>Februar</td>		<td>300</td><td> 20</td></tr>';
	echo '<tr><th>März</td>			<td>310</td><td> 30</td></tr>';
	echo '<tr><th>April</td>		<td>270</td><td>-10</td></tr>';
	echo '<tr><th>Mai</td>			<td>280</td><td>  0</td></tr>';
	echo '<tr><th>Juni</td>			<td>295</td><td> 15</td></tr>';
	echo '</table>';

	echo '<form method="POST" action="zeiterfassung.php">';
	echo '<button>Hauptansicht</button>';
	echo '</form>';
	echo '</center></body></html>';	
}


// $worker=new mitarbeiter($db);
// $worker->load($_POST['user']);
$_SESSION['firmanr']=$worker->row['firmanr'];
// echo $worker->row['firmanr'];
// echo "<pre>";
// var_dump($worker->row);
// echo "</pre>";

$f = new firma($db);
$f->load($_SESSION['firmanr']);
// echo $f->row['startup'];
 
// $worker->add();

$t=new zeiten($db);
$t->setUser($worker->row['recnum']);
$t->setUrlaub($worker->row['jahresurlaub']);
$t->setWorkTimes($worker->getWorkTimes());
$t->setEntree($worker->row['entree']);
$t->setStartup($worker->row['startup']);  // Arbeitserfassung jees EInzelnen Arbeiters
// $t->setStartup("01.04.2023");

if (isset($_POST['info'])) {
	info();
	exit;
}


if (isset($_POST['AE'])) {
	$t->add("AE");
}
if (isset($_POST['AS'])) {
	$t->add("AS");
}
if (isset($_POST['PE'])) {
	$t->add("PE");
}
if (isset($_POST['PS'])) {
	$t->add("PS");
}

$t->load();



headers();
echo '
<body><center>
<div style="display:inline-block;">
<h1>Hallo <p>'.$_POST['user'].'</p></h1>
<table width="100%"><tr>';
if ($t->output['inArbeit']) {
	echo '<td><nobr>arbeitet seit:</nobr></td><td align="right"><nobr>'.$t->output['AS'].' Uhr</nobr></td>';
} else {
	echo '<td><nicht am Arbeiten</td><td align="right">&nbsp;</td>';
}	
echo '</tr>';
if ($t->output['inPause']) {
	echo '<tr><td align="left">Pause seit:</td><td  align="right">'.$t->output['PS'].' Uhr</td></tr>';
	echo '<tr><td align="left">&nbsp;</td>     <td  align="right">('.$t->output['pause_jetzt'].')</td></tr>';

}
echo '
</table>
<h2>Statistik Heute</h2>
<table>
<tr><td>Pausenzeit:</td><td>'.$t->output['pause_gesamt'].'</td></tr>';
if (isset($t->output['arbeitszeit'])) {
	echo '<tr><td>Arbeitszeit:</td><td>'.$t->output['arbeitszeit'].'</td></tr>';
}
echo '
</table>
<h2>Jahresübersicht</h2><p>';
echo $t->output['jahresstunden'].' Arbeitsstunden';
if ($t->output['ueberstunden'] < 0) {
	if (abs($t->output['ueberstunden']) < $t->output['jahresstunden']) { // Wenn aufzeichnung nicht ab angegeben datum ist dann, kommt es zu falschen anzeigen, diese vehindert die anzeige von Fehltagen
		echo -$t->output['ueberstunden'].' - Fehlstunden';
	}
} else {
	echo $t->output['ueberstunden'].' - Überstunden';
}
echo '	
</p>
<p>'.$t->output['kranktage'].' Krankheitstage (='.$t->output['kranktage_netto'].' Fehltage)</p>
<p>'.$t->output['urlaubtage_netto'].' von '.$t->output['urlaubtage_gesamt'].' Urlaubstage</p>
<h2>Aktionen</h2>
<form method="post" action="'.$_SERVER['PHP_SELF'].'">';

if ($t->output['inArbeit']) {
	if ($t->output['inPause']) {
		echo '<button id="gross" name="PE">Pause beenden</button>';
	} else {
		echo '<button id="gross" name="PS">Pause starten</button>';
		echo '<button id="gross" name="AE">Arbeit beenden</button>'; // GEHT
	}		
} else {
	echo '<button id="gross" name="AS">Arbeit starten</button>'; // KOMMT
}
echo '		
<div id="bottom"><button name="info">Info</button></div>
</form>
<!-- button>Krank</button>
<button>Urlaub</button -->
</div></center>
</body>
</html>';


