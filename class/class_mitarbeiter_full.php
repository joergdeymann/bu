<?php
include "../dbconnect.php";
$_SESSION['firmanr']=1;
$_POST['user']="Jörg Deymann";

$worker=new mitarbeiter($db);
// $worker->add();

$t=new zeiten($db);
$t->setUser(1);

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

// $t->add("PE");
// $t->add("AE");
$t->load();


// exit;


// $request="INSERT INTO `bu_mitarbeiter`(`firmanr`, `name`, `mail`, `pw`, `rank`) VALUES ('".$firmanr."','".$name."','".$pw."')";
// $result = $db->query($request);


/*

datei:
usernr
time       Stempelzeit
typ:       A = Arbeit, P=Pause, U = Urlaub, K=Krank, F=Frei Unbezahlt // S = Start E=Ende 
		   Beispiele: AS PS PE PS PE AE, US UE KS KE
*/

class zeiten {
	private $db = "";
	public $row = array();
	private $usernr=0;
	public $output = array();
	
	
	public function __construct(&$db) {
		$this->db=$db;
	}
	
	/*
		Unterschied von 2x Datum in Sekunden
	
			DateInterval Object
			(
				[y] => 0
				[m] => 0
				[d] => 7
				[h] => 0
				[i] => 0
				[s] => 0
				[invert] => 0
				[days] => 7
			)
	*/
	private function getDiff($dt1,$dt2) {
		$diff = $dt2->diff($dt1);		
		$s=(($diff->days*24+$diff->h)*60+$diff->i)*60+$diff->s;
		return $s;
	}

	private function getLastEntry() {
		$request="select * from `bu_zeit` order by time DESC limit 1;";
		$result = $this->db->query($request);
		$row = $result->fetch_assoc();
		return $row['type'];
	}
		
		

	/*
		Benuter (Mitarbeiter) vorbereiten
	*/
	public function setUser($usernr) {
		$this->usernr=$usernr;
	}

	/*
		Zeit hinzufügen
	*/
	public function add($type) {
		// Doppelpost verhindern
		if($type == $this->getLastEntry()) {
			return;
		}
		
		$request="insert into `bu_zeit` set `usernr`='".$this->usernr."',`type`='".$type."',`time`= now()"; 
		$result = $this->db->query($request);
	}		
	
	/*
		Zeit mit Text zurückgeben
	*/
	private function display_time($d,$h,$i) {
		//echo "Tag=$d, h=$h, m=$i<br>";		
		if ((int)$d > 0) {
			$text = sprintf("%d Tage, %d Stunden, %d Minuten",$d,$h,$i);
		} else 
		if ((int)$h > 0) {
			$text = sprintf("%d Stunden, %d Minuten",$h,$i);
		} else {
			$text = sprintf("%d Minuten",$i);
		}
		return $text;
	}
	
	/*
		Reale Arbeitszeiten mit vordefinierten Zeiten vergleichen
		unter Beachtung vonb:
		- Kaufdatum / Benutzer gewünschter start
		- Jahresanfang
		- Eintrittsdatum des Mitarbeters

		ACHTUNG: Diese funktion gehört in das auszuführende Script, nicht in der Klasse
		
	*/
	private function getWorkTime($datumvon="",$datumbis="",$format="h") {
		/*
			Variablen voreinstellungen
		*/
		$this->workhours_each=array(0,8,8,8,8,8,0); // w: 0=Sonntag, 1=Montag 6=Samstag
		$this->entree   = "2023-05-01";
		$this->startup  = "2023-05-01";

		$dt_entree  = new DateTime($this->entree);
		$dt_startup = new DateTime($this->startup);
		$dt_now     = new DateTime();
		$workhours  = $this->workhours_each;
		
		/* 
			Vorbereitungen / Vereinfachung / Autonmation
		*/	
		if ($datumbis == "") {
			$dt_bis=new DateTime();
		} else {
			//if (gettime($datumbis) == 'object') {
			if (gettype($datumbis) == 'object') {
				$dt_bis=$datumbis;
			} else {
				$dt_bis=new DateTime($datumbis);
			}
		}

		if ($datumvon == "") {
			$dt_von=new DateTime($dt_bis->format("Y")."-01-01");
		} else {
			if (gettype($datumvon) == 'object') {
				$dt_von=$datumvon;
			} else {
				$dt_von=new DateTime($datumvon);
			}
		}
			
		if ($dt_entree > $dt_von) {
			$dt_von=$dt_entree;
		}
		if ($dt_startup > $dt_von) {
			$dt_von=$dt_startup;
		}
		
		if ($dt_bis > $dt_now ) {
			$dt_bis=$dt_now;
		}
		
		
		/*
			Tage hochzählen in denenen gearbeitet wird
			Stunden hochzählen lt Voreinstellung
		*/
		$wt = $dt_von->format("w")-1; // Wochentagnummer 1=Montag 0=Sonntag
		$h=0; // Arbeitsstunden
		$d=0; // Arbeitstage
		
		$days=$dt_bis->format("z"); // 0-365
		$dayone=$dt_von->format("z"); // 0-365
		for($i=$dayone;$i <= $days;$i++) {
			$wt=($wt+1) % 7;
			$h+=$workhours[$wt];
			if ($workhours[$wt] > 0) {   // and isFeiertag == false
				$d++;
			}			
			// echo "i:$i, wt=$wt, D:$d<br>"; 
		}
		
		/*
			Rückgabe
			Standart = "h";
			"h" = Stunden
			"d" = Tage
		*/
		if ($format == "d") {
			$h=$d;
		}
		return $h;
	}
	

	/*
		Anzahl der normalen Tage herausfinden
		von Jahresanfang bis jetzt		
	*/
	private function getWorkTimeOfYear() {
		return $this->getWorkTime("","","h");
	}
	
	
	/*
	
		
		Ungenutzt
		
		Anzahl der normalen Tage herausfinden
		von Monatsanfang bis jetzt
		getWorkTime($datumbis,"d") Return $d
		getWorkTime($datebis,"h"); return $h
	*/
	private function XgetWorkTimeOfMonth($datumbis,$format="h") {
		if ($datumbis == "") {
			$dt_bis=new DateTime();
			$days = $dt_bis->format("t");
			$m    = $dt_bis->format("m");
			$y    = $dt_bis->format("Y");
			$dt_bis->setDate($y,$m,$days);		
			$dt_von->setDate($y,$m,1);
		} else {
			$dt_bis= new DateTime($datumbis);		
			$dt_von =new DateTime($dt_bis->format("Y")."-".$dt_bis->format("m")."-01");
		}
		
		return $this->getWorkTime($datumvon->format("Y-m-d H:i:s"),$datunmbis->format("Y-m-d H:i:s"),$format);		
	}
		
	/*
		Laden mittels Usernr (Mitarbeiternr)
	*/
	public function load() {
		$this->output['arbeitszeit']="keine";
		$this->output['inPause']=false;
		$this->output['inArbeit']=false;
		$this->output['pause_gesamt']="keine";

		$this->output['urlaubtage_gesamt']="30";

		$ax=0; // Arbeitszeiten im Jahr inc Pause
		$px=0; // Pausenzeiten im Jahr 
		$kx=0; // Kranheiutstage
		$kx_netto=0; // Krankheitstage ohne Freie Tage
		$ux=0; 		 // Urlaub
		$ux_netto=0; // Urlaub ohne Freie Tage


		/*
			letzten Arbeitsanfang finden
		*/
		$request="select max(time) as max from `bu_zeit` where `usernr`='".$this->usernr."' and `type`='AS'";
		$result = $this->db->query($request);
		$row = $result->fetch_assoc();
		$start = $row['max'];
		// echo "Start=".$start."<br>";
		
		/*
			letztes Arbeitsende finden
		*/
		$request="select max(time) as max from `bu_zeit` where `usernr`='".$this->usernr."' and `type`='AE'";
		$result = $this->db->query($request);
		$row = $result->fetch_assoc();
		$ende = $row['max'];
		
		
		if ($ende > $start) {
			$this->output['inArbeit']=false;
		} else {
			$this->output['inArbeit']=true;
		}
			
		if ($this->output['inArbeit'] == true) {
			
			$request="select * from `bu_zeit` where `usernr`='".$this->usernr."' and `time` >= '".$start."' order by `time`";
			$result = $this->db->query($request);
	
			$pause_gesamt=(int)0;
			$pause_start=(int)0;
			
			
			/*
				Momentane Arbeitszeit / Pause des Arbeits-TAGES
			*/
			$dt1=new DateTime();
			$dt2=new DateTime($start);
			$ax = $this->getDiff($dt1,$dt2);
			
			while ($row = $result->fetch_assoc()) {
				if ($row['type'] == "AS") {
					$date1 = new DateTime();
					$date2 = new DateTime($row['time']);
					$diff = $date2->diff($date1);
					
					// $this->output['arbeitszeit'] = sprintf("%d Stunden, %d Minuten",$diff->h,$diff->i);
					$this->output['arbeitszeit'] = $this->display_time($diff->days,$diff->h,$diff->i);
					$this->output['AS'] = $date2->format("d.m.Y H:i:s");
					$ax+=$this->getDiff($date1,$date2);  // AX in Sekunden
				}
				
				/*
					Momentane Pausenzeit : Start
				*/
				if ($row['type'] == "PS") {				
					$pause_start = $row['time'];
					$date1 = new DateTime($pause_start);
					$this->output['PS']=$date1->format("d.m.Y H:i:s");
				}
				
				
				/*
					Momentane Pausenzeit : Ende
				*/
				if ($row['type'] == "PE") {
					// echo "Pasue Start:$pause_start<br>";					
					$date1 = new DateTime($row['time']);
					$date2 = new DateTime($pause_start);
					//#$diff = $date2->diff($date1);

					//#$pause_teil = $diff->h*60+$diff->i; // in Minuten
					//#$pause_gesamt+=$pause_teil;
					$pause_gesamt+=$this->getDiff($date1,$date2); //#
					$pause_start=0;
				}
				
			}
			
			// if ($pause_start) {
			// echo "Pause_start:".$pause_start."<br>";		
			if ($pause_start>0) {
				$date1 = new DateTime();
				$date2 = new DateTime($pause_start);
				$diff = $date2->diff($date1);
				//# $pause_teil = $diff->h*60+$diff->i; // in Minuten
				//# $pause_gesamt+=$pause_teil;         // in Minuten
				$pause_gesamt = $this->getDiff($date1,$date2); //#
				// $this->output['pause_jetzt'] = sprintf("%d Stunden, %d Minuten",$diff->h,$diff->i);
				$this->output['pause_jetzt'] = $this->display_time($diff->days,$diff->h,$diff->i);
				$this->output['inPause'] = true;
				
			} else {
				$this->output['pause_jetzt'] = "keine Pause";
				$this->output['inPause'] = false;
				
				// $this->output['pause_gesamt']=$pause_gesamt;	
			}
			if ($pause_gesamt>0) {
				// $px=$pause_gesamt*60;
				$px=$pause_gesamt;				
				$this->output['pause_gesamt'] =  $this->display_time(0,($pause_gesamt/60),($pause_gesamt%60));
			}
		
		}

		/*
			Statistiken  Jahresangaben
			
			- Kranktage
			- Urlaubstage
			- Ueberstunden
		*/
		$s=0; //startzeit
		$e="now()";
		$request="select * from `bu_zeit` where `usernr`='".$this->usernr."' and `time` between '".$s."' and ".$e." order by `time`";
		// echo "$request<br>";
		$result = $this->db->query($request);
		// $ks=0; // Krank Start
		// $ke=0; // Krank ende
		
		while($row=$result->fetch_assoc()) {
			if ($row['type'] == "AS") {
				$as=$row['time'];
			} else 
			if ($row['type'] == "AE") {
				$ae=$row['time'];
				// echo "AE=$ae<br>AS=$as<br>";
				$ax=$ax+strtotime($ae)-strtotime($as);				
				// echo "AE=$ae<br>AS=$as<br>ax=$ax<br>";
			} else 
			if ($row['type'] == "PS") {
				$ps=$row['time'];
			} else 
			if ($row['type'] == "PE") {
				$pe=$row['time'];
				$px=$px+strtotime($pe)-strtotime($ps);				
			} else 
			if ($row['type'] == "KS") {
				$ks=$row['time'];
			} else 
			if ($row['type'] == "KE") {
				$ke=$row['time'];
				$date1 = new DateTime($ks);
				$date2 = new DateTime($ke);
				$diff = $date2->diff($date1);
				
				$kx=$kx+$diff->days+1; // inclusive deswegen +1
				$kx_netto+=$this->getWorkTime($ks,$ke,"d");
			} else
			if ($row['type'] == "US") {
				$us=$row['time'];
			} else 
			if ($row['type'] == "UE") {
				$ue=$row['time'];
				$date1 = new DateTime($us);
				$date2 = new DateTime($ue);
				$diff = $date2->diff($date1);
				
				$ux=$ux+$diff->days+1; // inclusive deswegen +1
				$ux_netto+=$this->getWorkTime($us,$ue,"d");
			} 
			
		}
		/*
			Krank
		*/
		$this->output['kranktage']=$kx;
		$this->output['kranktage_netto']=$kx_netto;

		/*
			Urlaub
		*/
		$this->output['urlaubtage']=$ux;
		$this->output['urlaubtage_netto']=$ux_netto;
		
		// $date1 = new DateTime($ks);
		// $date2 = new DateTime($ke);
		// $diff = $date2->diff($date1);
		
		/*
			Jahres- / Überstunden
		*/	
		
		// echo "AX=$ax<br>PX=$px<br>";
		$jahresstunden_ist = (int)(($ax-$px)/3600); // Jahresstunden
		$jahresstunden_soll = $this->getWorkTimeOfYear();

		$this->output['jahresstunden'] = $jahresstunden_ist;
		$this->output['ueberstunden']= $jahresstunden_ist - $jahresstunden_soll;
		
	}
}


class mitarbeiter {
	private $db = "";
	private $row = array();
	
	public function __construct(&$db) {
		$this->db=$db;
		if (isset($_COOKIE['username'])) {
			$_SESSION['username']=$_COOKIE['username'];
			setcookie("username",$_COOKIE['username'],time()+(3600*24)); // 24h
		}
		
	}
	
	public function load($name) {
		$request="select * from bu_mitarbeiter where name='".$this->db->real_escape_string($name)."'";
		$result = $this->db->query($request);
		$this->row = $result->fetch_assoc();
	}

	public function exist($name) {
		$request="select count(*) as sum from bu_mitarbeiter where name='".$this->db->real_escape_string($user)."'";
		$result = $this->db->query($request);
		$row = $result->fetch_assoc();

		if ($row['sum'] > 0) {
			return true;
		}
		return false;
	
	}

	/*
		Benötigte Variablen
		$record 
			'name'
		$row['firmanr']
		$_POST['user']
		$_SESSION['firmanr'];
		
	*/
	public function add($record="") {
		// $name=$record['name'];
		// $pw=cipher($name,$name);
		// $firmanr=$this->row['firmanr'];
		
		// $request="insert select count(*) as sum from bu_mitarbeiter where name='".$this->db->real_escape_string($user)."'";
		// $result = $this->db->query($request);

		$request  = sprintf("INSERT INTO bu_mitarbeiter(firmanr,name,pw) VALUES('%s','%s','%s');",
					$_SESSION['firmanr'],
					$this->db->real_escape_string($_POST['user']),
					password_hash($_POST['user'], PASSWORD_DEFAULT)
					);
		$result = $this->db->query($request);
	}

	/* 
		Erster Login
		-> Passwort muss geändert werden
	*/
	public function login_firsttime() {
		if (password_verify($this->row['name'], $this->row['pw'])) {
			return true;
		}
		return false;
	}
	
	/* 
		Normaler Login
	*/	
	public function login(&$pw) {
		if (password_verify($pw, $this->row['pw'])) {
			$_SESSION['username']=$_POST['name'];

			// setcookie("username",$_POST['name'],time()+(3600*24)); // 24h
			// if isset($_COOKIE['username']) {
			//	$_SESSION['username']=$_COOKIE['username'];
			//  setcookie("username",$_COOKIE['username'],time()+(3600*24)); // 24h
			// }
			return true;
		}
		return false;
	}
	
	
	
	
}


echo '
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta name="description" 
          content="Stempeluhr und Zeiterfassung mit Urlaubs und Krankheitsabwesenheiten für jeden, mit Auswertungen">
    <meta name="keywords" content="Stempeluhr, stempeln, Zeiterfassung, Krankheitserfassung, Urlaubserfassung">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zeiterfassung</title>
	<style>
html,body {
	width:100%;
	background-color:yellow;
}
button#gross {
	padding: 1em;
	font-weight: 1000;
	font-size: 2em;
}
h1 {
	background-color: red;
	margin:0;
}
h2 {
	background-color: lightblue;
	margin:0;
}
td {
	font-size: 1.5em;
}
	</style>
  </head>
<body><center>
<div style="display:inline-block;">
<h1>Hallo '.$_POST['user'].'</h1>
<table><tr>';
if ($t->output['inArbeit']) {
	echo '<td>arbeitet seit:</td><td align="right">'.$t->output['AS'].' Uhr</td><td>&nbsp;</td>';
} else {
	echo '<td>nicht am Arbeiten</td><td align="right">&nbsp;</td><td>&nbsp;</td>';
}	
echo '</tr>';
if ($t->output['inPause']) {
	echo '<tr><td>Pause seit:</td><td align="right">'.$t->output['PS'].' Uhr</td><td>('.$t->output['pause_jetzt'].')</td></tr>';
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
echo $t->output['jahresstunden'].' Arbeitsstunden - ';
if ($t->output['ueberstunden'] < 0) {
	echo -$t->output['ueberstunden'].' Fehlstunden';
} else {
	echo $t->output['ueberstunden'].' Überstunden';
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
</form><hr>
<button>Krank</button>
<button>Urlaub</button>
</div></center>
</body>
</html>';


