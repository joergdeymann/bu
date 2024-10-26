<?php
session_start();
include "../class/dbconnect.php";
include "../class/class_mitarbeiter.php";
include "../class/class_zeiten.php";
include "../class/class_firma.php";
include "../class/class_cookie.php";

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
if (isset($_SESSION['usernr']) && !empty($_SESSION['usernr'])) {
	// echo $_SESSION['usernr'].'<br>';
	$worker->loadByRecnum($_SESSION['usernr']);
	$_POST['user']=$worker->row['name'];
	$display="ok";	
} else 
if ($c->login_cookie()) {
	$_POST['user']=$_COOKIE['username'];
	$worker->load($_POST['user']);
	$display="ok";
	session_start();
	$_SESSION['usernr']=$worker->row['recnum'];
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
					$_SESSION['usernr']=$worker->row['recnum'];
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

function getSek($d1,$d2) {
	$date1=new DateTime($d1);
	$date2=new DateTime($d2);
	
	$az = $date2->diff($date1);
	return ($az->h*60*60+$az->i*60+$az->s);
}

function info($msg="") {
	global $db;
	global $worker;
	
	$wt = array(
		$worker->row['so'],
		$worker->row['mo'],
		$worker->row['di'],
		$worker->row['mi'],
		$worker->row['do'],
		$worker->row['fr'],
		$worker->row['sa']
		);
	$start=$worker->row['startup'];
	if (empty($worker->row['ueberstunden_ab']) || ($worker->row['ueberstunden_ab']=="0000-00-00")) {
		$worker->row['ueberstunden_ab']=$start;
	}


	$us_woche = 0; // Überstunden Woche
	$us_monat = 0; // Überstunden Monat
	$us_jahr  = 0; // Übersstunden Jahr

	$ax       = 0;  // Arbeitsstunden gesamt
	$ax_woche = array();  // Arbeitsstunden Woche
	$ax_monat = array();  // Arbeitsstunden Monat
	$ax_jahr  = array();  // Arbeitsstunden Jahr
	$az_ist   = 0;

	$px       = 0;
	
	$us		  = 0;
	$us_tag   = 0;
	$us_woche = array();  // Arbeitsstunden Woche
	$us_monat = array();  // Arbeitsstunden Monat
	$us_jahr  = array();  // Arbeitsstunden Jahr

	$heute    = new DateTime(); //Aktelles Datum
	// $t=new zeiten($db);
	

	$monate = array(
	"",
	"Januar",
	"Februar",
	"März",
	"April",
	"Mai",
	"Juni",
	"Juli",
	"August",
	"September",
	"Oktober",
	"November",
	"Dezember",
	);

	$request="select * from `bu_zeit` where `usernr`='".$worker->row['recnum']."' and `time` >= '".$start."' order by `time`";
	$result = $db->query($request);

	while ($row = $result->fetch_assoc()) {
		if ($row['type'] == "AS") {
			$as=$row['time'];
			$px=0;	
			// $isworking=true;
		}
		
		if ($row['type'] == "AE") {
			if (!isset($as)) {
				continue;
			}
			$ae=$row['time'];
			
			$az_ist = getSek($as,$ae) - $px;  // in sekunden
		
			$az_h=(int)($az_ist / 3600);        // Abgerundetet Stunden^
			if (($az_ist % 3600) > (20*60)) { // > 20 min, dann aufrunden
				$az_h++;
			}
			$dt=new DateTime($as);
			$dt_woche=$dt->format("W");
			$dt_monat=$dt->format("n");
			$dt_jahr =$dt->format("Y");
		
			$az_soll= $wt[$dt->format("w")]; // Englischer Wochentag 0 = Sonntag
// echo "$az_h -- $az_soll<br>";			

			if (!isset($ax_woche[$dt_woche])) {
				$ax_woche[$dt_woche]=0;
			}
			if (!isset($ax_monat[$dt_monat])) {
				$ax_monat[$dt_monat]=0;
			}
			if (!isset($ax_jahr[$dt_jahr])) {
				$ax_jahr[$dt_jahr]=0;
			}
			
			if (!isset($us_woche[$dt_woche])) {
				$us_woche[$dt_woche]=0;
			}
			if (!isset($us_monat[$dt_monat])) {
				$us_monat[$dt_monat]=0;
			}
			if (!isset($us_jahr[$dt_jahr])) {
				$us_jahr[$dt_jahr]=0;
			}
			
			$date2=new DateTime($worker->row['ueberstunden_ab']);
		
			if ($dt >= $date2) {
				$us_tag    = $az_h-$az_soll;   			  // Überstunden gesamt
			}
			$us                  += $us_tag;
			$us_woche[$dt_woche] += $us_tag;          // Überstunden Woche
			$us_monat[$dt_monat] += $us_tag;          // Überstunden Monat
			
			$us_jahr[$dt_jahr]   += $us_tag;          // Übersstunden Jahr
			
			$px=0;
			// Geamt
			$ax                  += $az_ist;  // Arbeitsstunden gesamt
			$ax_woche[$dt_woche] += $az_ist;  // Arbeitsstunden Woche
// echo "Woche $dt_woche:".$ax_woche[$dt_woche]."<br>";			
			$ax_monat[$dt_monat] += $az_ist;  // Arbeitsstunden Monat
			$ax_jahr[$dt_jahr]   += $az_ist;  // Arbeitsstunden Jahr
			
			// ----------------------- Fehltage noch ------------------------------------------
			
		}
		if ($row['type'] == "PS") {
			$ps=$row['time'];
		}
		if ($row['type'] == "PE") {
			$pe=$row['time'];

			$px += getSek($ps,$pe);
		}
	}
	
	$woche=$heute->format("W");
	$monat=$heute->format("n");
	$jahr=$heute->format("Y");
	
	$monatsanfang=new DateTime($heute->format("Y-m-01")); // Anfang des Monats
	
			if (!isset($ax_woche[$woche])) {
				$ax_woche[$woche]=0;
			}
			if (!isset($ax_monat[$monat])) {
				$ax_monat[$monat]=0;
			}
			if (!isset($ax_jahr[$jahr])) {
				$ax_jahr[$jahr]=0;
			}
			if (!isset($us_woche[$woche])) {
				$us_woche[$woche]=0;
			}
			if (!isset($us_monat[$monat])) {
				$us_monat[$monat]=0;
			}
			if (!isset($us_jahr[$jahr])) {
				$us_jahr[$jahr]=0;
			}

	headers();
	// $start=$m->row['ueberstunden_reset'];
	
	echo '<body><center>';
	echo '<h1>Hallo <p>'.$_POST['user'].'</p></h1><br>';
	$dt=new DateTime($start);
	echo '<i style="font-size:1.2em;position:relative;top:-10px;">Erfassung seit: '.$dt->format('d.m.Y').'</i><br>';
	$dt=new DateTime($worker->row['ueberstunden_ab']);
	echo '<i style="font-size:1.2em;position:relative;top:-10px;">Überstunden seit: '.$dt->format('d.m.Y').'</i>';
	echo '<h2>Stunden von heute ('.$heute->format("d.m.Y").')</h2>';

	echo '<table cellspacing=0 id="zeiten">';

	echo '<tr><th>&nbsp;</th><th>Tag</th><th>Woche</th><th>Monat</th><th>Gesamt</th></tr>';
	echo '<th style="text-align:left">Stunden</th>    <td>'.(int)($az_ist/3600).'</td><td>'.(int)($ax_woche[$woche]/3600).'</td><td>'.(int)($ax_monat[$monat]/3600).'</td><td>'.(int)($ax/3600).'</td></tr>';
	echo '<th style="text-align:left">Überstunden</th><td>'.$us_tag.'</td><td>'.$us_woche[$woche].'</td><td>'.$us_monat[$monat].'</td><td>'.$us.'</td></tr>';
	echo '</table>';
	echo '<h2>Wochenstunden vom <nobr>'.$monate[$heute->format("n")].' '.$jahr.'</nobr></h2>';
	echo '<table cellspacing=0 id="zeiten">';
	echo '<tr><th>Woche</th><th>Stunden</th><th>Überstunden</th></tr>';
	for ($i=$monatsanfang->format("W");$i<=$heute->format("W");$i++) {
		if (empty ($ax_woche[$i])) {
			$ax_woche[$i]=0;
		}	
		if (empty ($us_woche[$i])) {
			$us_woche[$i]=0;
		}	
			
		echo '<tr><td>'.$i.'</th><td>'.(int)($ax_woche[$i]/3600).'</td><td>'.$us_woche[$i].'</td></tr>';
	}
	echo '</table>';

	echo '<h2>Monatsstunden von '.$jahr.'</h2>';
	echo '<table cellspacing=0 id="zeiten">';
	echo '<tr><th>Monat</th>     	<th>Stunden</th><th>Überstunden</th></tr>';
	for($i=1;$i<=12;$i++) {
		if (isset($ax_monat[$i])) {
			$a=(int)($ax_monat[$i]/3600);
			$b=$us_monat[$i];
			echo '<tr><th>'.$monate[$i].'</td><td>'.$a.'</td><td>'.$b.'</td></tr>';
 		}
	}
	echo '</table>';

	echo '<form method="POST" action="zeiterfassung.php">';
	echo '<button>Hauptansicht</button>';
	echo '</form>';
	echo '</center></body></html>';	
}

   // border-collapse: collapse;
   // border-collapse: separate;
   //      border-spacing: 10px;

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


/* 
	Doppelpost kontrolle bitte checken ob 2x AE hintereinander oder 2x PAusenanfang
*/

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
echo $t->output['jahresstunden'].' Arbeitsstunden<br>';
if ($t->output['ueberstunden'] < 0) {
	if (abs($t->output['ueberstunden']) < $t->output['jahresstunden']) { // Wenn aufzeichnung nicht ab angegeben datum ist dann, kommt es zu falschen anzeigen, diese vehindert die anzeige von Fehltagen
		echo -$t->output['ueberstunden'].' Fehlstunden';
	}
} else {
	echo $t->output['ueberstunden'].' Überstunden<br>';
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
<div id="bottom">
<button name="info">Info</button>
<button name="project" formaction="project.php" formmethod="POST">Projekt</button>
<button name="mehr" formaction="zeit_urlaub.php" formmethod="POST">mehr</button>
</div>
</form>
<!-- button>Krank</button>
<button>Urlaub</button -->
</div></center>
</body>
</html>';


