<?php
/*

datei:
usernr
time       Stempelzeit
typ:       A = Arbeit, P=Pause, U = Urlaub, K=Krank, F=Frei Unbezahlt // S = Start E=Ende 
		   Beispiele: AS PS PE PS PE AE, US UE KS KE
*/
/*
	Aufruf:
	$z=new Zeit($db);
	$z->setMitarbeiter($m);
	$z->setEntree($m->entree);
	$z->setStartup($m->startup);
	$z->setUsernr($m->recnum);
	
	
*/


class Zeit {
	private $db = "";
	private $m_row;
	
	public  $row = array();
	private $usernr=0;
	public  $output = array();
	public  $result;

	private $workhours_each=array(0,8,8,8,8,8,0); // w: 0=Sonntag, 1=Montag 6=Samstag
	private $entree   = "2023-05-01";             //------------------------------ Aus Datei holen
	private $startup  = "2023-05-01";             //------------------------------ Aus Datei holen

	private $dark = false;
	// private $aufrundenAS=15;         //min; In einer Funktion einbauen
	// private $aufrundenAE=15;         //min; In einer Funktion einbauen

	public int $active_mitarbeiter;   // Mitarbeiter Recnum vor dem wechsel
						              // Gesamt der Selektion:
	public int $sum_minutes=0;        // Arbeits-Stunden 60 Minuten * 24 h *30 Tage = Maximal 43.200: int 32 Bit >= 2.147.483.647
	public int $sum_pause=0;          // Arbeits-Pausen  
	public int $sum_overhours=0;      // Über Stunden
	public int $sum_underhours=0;     // unter Stunden
	
									  // Tagesstunden:
	private int $sum_day_minutes=0;   // Arbeits stunden
	private int $sum_day_pause=0;     // Arbeits pausen (in minuten)
	
	private int $ASR;                 // Recnum AS
	private int $AER;                 // Recnum AS
	
	public DateTime $dt_AS;          // Arbeits start
	public DateTime $dt_AE;          // Arbeits ende

	private DateTime $dt_PS;          // Pause start
	private DateTime $dt_PE;          // Pause Ende
	
	private $pause=array();           // "PS" => array(),"PE" => array()); // Muster: $pause[0]['PS']=dt;
	private $pause_index =0;          // Zähler für Pausen an einem Tag
	
	// private $sum_week_hours
	// private $sum_week_pause;
	
	
	
	public function __construct(&$db) {
		$this->db=$db;
	}

	/*
		Benutzer (Mitarbeiternr) vorbereiten
	*/
	public function setUserNr($usernr) {
		$this->usernr=$usernr;
	}

	
	/* 
		Arbeitszeiten festlegen  0 = Sonntag 1=Montag
		Typ: array(7)
	*/
	public function setWorkTimes($t) {
		$this->workhours_each=$t;
	}

	/* 
		Eintrittsdatum des Mitarbeiters festlegen  Type String oder DateTime
		Type: String oder DateTime
	*/
	public function setEntree($t) {
		$this->entree=$this->toDateTime($t);
	}

	/* 
		Kaufdatum des Programms festlegen  
		Type: String oder DateTime
	*/
	public function setStartup($t) {
		$this->startup=$this->toDateTime($t);;
	}
	
	
	/* 
		Zeit hinzufügen
		Input:
		add("AS") 
		add("AS","2003-12-02");
		add("AS",new DateTime());
	*/
	public function add($type,$time="") {
		if ($time=="") {			
			// Doppelpost verhindern
			if ($type == $this->getLastEntry()) {
				return;
			}
			$request="insert into `bu_zeit` set `usernr`='".$this->usernr."',`type`='".$type."',`time`= now()"; 
		} else {
			if (gettype($time) == "string") {
				$time=new DateTime($time);
			}
			$request="insert into `bu_zeit` set `usernr`='".$this->usernr."',`type`='".$type."',`time`= '".$time->format("Y-m-d H:i:s")."'"; 
		}
		$result = $this->db->query($request);
		if ($result) {
			$dt=new DateTime();
			$this->row['recnum']=$this->db->insert_id;
			$this->row['type']=$type;
			$this->row['time']=$dt->format("Y-m-d H:i:s");
		}

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
	
	
	private function toDateTime($d) {
		if (gettype($d) == 'object') {
			return $d;
		} else {
			return new DateTime($d);
		}
	}

	// Zeitunterschied in Minuten
	
	private function getDiff($dt1,$dt2) {
		$diff = $dt2->diff($dt1);		
		// $s=(($diff->days*24+$diff->h)*60+$diff->i)*60+$diff->s;
		$m=($diff->days*24+$diff->h)*60+$diff->i;
		return $m;
	}

	private function getLastEntry() {
		$request="select * from `bu_zeit` where `usernr`='".$this->usernr."' order by time DESC limit 1;";
		$result = $this->db->query($request);
		if ($result) {
			$row = $result->fetch_assoc();
		} else {
			$row['type']="";
		}
		return $row['type'];
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
			$text = sprintf("%d Std, %d Min",$h,$i);
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
		// $this->workhours_each=array(0,8,8,8,8,8,0); // w: 0=Sonntag, 1=Montag 6=Samstag
		// $this->entree   = "2023-05-01";
		// $this->startup  = "2023-05-01";

		$dt_entree  = $this->entree;
		$dt_startup = $this->startup;
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
	
	
		
	
	public function loadByDate($m,$dt_von,$dt_bis) {
		if (!empty($this->uernr) && empty($m)) {
			$m=$this->uernr;
		}
		// $request='SELECT * from `bu_zeit` where `firmanr`="'.$_SESSION['firmanr'].'" and `mitarbeiternr`="'.$m.'" and `time` between "'.$dt_von.'" and "'.$dt_bis.'"'; 
		$request='SELECT * from `bu_zeit` where `usernr`="'.$m.'" and `time` between "'.$dt_von.'" and "'.$dt_bis.'" order by time'; 
		// echo $request;
		$this->result=$this->db->query($request);
		return $this->result;
	}
	
	public function next() {
		$this->row=$this->result->fetch_assoc();
		return $this->row;
	}

	/*
		Update über Recnum
	*/
	public function update($row) {
		$recnum=$row['recnum'];
		unset($row['recnum']);
		
		$set="";
		foreach($row as $k => $v) {
			if ($set != "") {
				$set.=",";
			}
			$set.="`".$k."`='".$this->db->real_escape_string($v)."'";
		}
		
		$request="update bu_zeit set $set where `recnum`='".$recnum."'";	
		$result = $this->db->query($request);
		// echo $request;
		$this->row['recnum']=$recnum;
		return $result; 
		
		
	}


	/*
		Löschen über Recnum
	*/
	public function del($recnum) {
		$request="delete from `bu_zeit` where `recnum`='".$recnum."'";	
		$result = $this->db->query($request);
		return $result; 	
	}
	/* 
		Löschen eines Zeitraums
	*/
	public function delByDate($dt_von,$dt_bis) {
		if (empty($this->usernr)) {
			return false;
		}
		$m = $this->usernr;
		$request='delete from `bu_zeit` where `usernr`="'.$m.'" and `time` between "'.$dt_von.'" and "'.$dt_bis.'"';	
		$result = $this->db->query($request);
		return $result; 	
	}
	
	public function loadRequest($request) {
		$this->result = $this->db->query($request);
		return $this->result; 			
	}
	
	public function firstAS() {
		while($row = $this->result->fetch_assoc()) {
			if ($row['type']=='AS')  {
				$this->row=$row;
				return $row;
			}
		}
		$this->dt_AS="";
		$this->dt_AE="";
		$this->ASR="";
		$this->AER="";
		$this->pause=array();
		return false; // AS nicht gefunden
	}
	
	public function output() {
		$dark=&$this->dark;
		
		// Hier datensumme hinschreiben
		if ($dark) {
			$dark=false;
			$line='<tr id="dark">';
		} else {
			$dark=true;
			$line='<tr>';
		}
		$dt_value="";
		if (!empty($this->dt_AS) and !empty($this->dt_AE)) {
			$dt=$this->dt_AS->diff($this->dt_AE);			
			$this->sum_minutes+=($dt->h*60+$dt->i);
			$dt_value=sprintf("%02d:%02d",$dt->h,$dt->i);
		} 
			
		
		$as="";
		$ae="";
		if (!empty($this->dt_AS)) {
			$as=$this->dt_AS->format("d.m.Y H:i");
		}
		if (!empty($this->dt_AE)) {
			$ae=$this->dt_AE->format("d.m.Y H:i");
		}
		
		$line.='<td>'.$as;
		$line.=' - '.$ae;
		$line.='</td>';
		$line.='<td>'.$dt_value.'</td>';
		$line.='<td style="width:auto">&nbsp;</td>';
			
		
		
		/*
			Pausenzeiten von bis
		*/
		$sum_pause=0;
		$pausen=array();
		$line.='<td>';
		foreach($this->pause as $k => $v) {
			if (!empty($this->pause[$k]['PS']) and !empty($this->pause[$k]['PE'])) {
				$dt=$this->pause[$k]['PS']->diff($this->pause[$k]['PE']);
				$this->sum_pause+=($dt->h*60+$dt->i);
				$sum_pause+=($dt->h*60+$dt->i);
				$pausen[]=$dt;
				$line.=$this->pause[$k]['PS']->format('H:i').' - '.$this->pause[$k]['PE']->format('H:i').'<br>';
			} else {
				$pausen[]="";
				$ps="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				$pe=$ps;
				if (!empty($this->pause[$k]['PS']) ) {
					$ps=$this->pause[$k]['PS']->format('H:i');
				} 
				if (!empty($this->pause[$k]['PE']) ) {
					$ps=$this->pause[$k]['PE']->format('H:i');
				} 
				$line.="$ps - $pe<br>";
			}
		}		
		$line.='</td>';

		/*
			Pausenzeiten Summe
		*/
		$line.='<td>';


		foreach($pausen as $k => $dt) {
			if (empty($dt)) {
				$line.="&nbsp;<br>";
			} else {
				$line.=sprintf("%02d:%02d",$dt->h,$dt->i).'<br>';
			}
		}
		$line.='</td>';
		
		
			
		$line.='<td>';
		$line.='<form method="POST" action="zeiten_liste.php">';

// if (isset($this->dt_AS)) {
		$line.='<input type="hidden" name="AZ_von" value="'.$this->dt_AS->format('Y-m-d H:i:s').'">';		
		$line.='<input type="hidden" name="AS_recnum" value="'.$this->ASR.'">';
// }
		if (isset($this->dt_AE)) {
			$line.='<input type="hidden" name="AZ_bis" value="'.$this->dt_AE->format('Y-m-d H:i:s').'">';
			$line.='<input type="hidden" name="AE_recnum" value="'.$this->AER.'">';
		}
		$line.='<input type="hidden" name="mitarbeiter_recnum" value="'.$this->active_mitarbeiter.'">';
		$line.='<input type="submit" value="bearbeiten">';
		$line.='</form>';
		$line.='</td>';
		$line.='</tr>';
		
		unset($this->dt_AS);
		unset($this->dt_AE);
		$this->ASR=0;
		$this->AER=0;
		$this->pause=array();
		
		return $line;
	}
	
	public function calc() {
		// echo $this->row['type'].":".$this->row['time']."<br>";
		
		if ($this->row['type'] == "AS") {
			if (!empty($this->dt_AS)) {
				// hier muss dann die Ausgabe vorbereitet werden, bervor alles überschrieben wird
				return $this->output();
			}
			$this->active_mitarbeiter=$this->row['mitarbeiter_recnum'];
			// $dt= new DateTime($row['time']);
			// $this->dt_AS = new DateTime($dt->format("Y-m-d H:i:00"));
			$this->dt_AS = new DateTime($this->row['time']);
			$this->ASR = $this->row['recnum'];
			// echo " AS=".$this->dt_AS->format("d.m.Y")."<br>";
			
		}
		
		if ($this->row['type'] == "AE") {
			// $dt= new DateTime($row['time']);
			// $this->dt_AE = new DateTime($dt->format("Y-m-d H:i:59"));
			$this->dt_AE = new DateTime($this->row['time']);
			$this->AER = $this->row['recnum'];
			return $this->output();
		}


		if ($this->row['type'] == "PS") {
			$this->dt_PS = new DateTime($this->row['time']);
			// $dt= new DateTime($row['time']);
			// $this->dt_PS = new DateTime($dt->format("Y-m-d H:i:10"));
			$i=&$this->pause_index;
			if (isset($this->pause[$i]["PS"])) {				
				$i++; // Hier ist ein Fehler in der Reihenfolge aufgetreten
			}
			$this->pause[$i]=array();
			$this->pause[$i]['PS']=$this->dt_PS;
		}
		
		if ($this->row['type'] == "PE") {
			$this->dt_PE = new DateTime($this->row['time']);
			// $dt= new DateTime($row['time']);
			// $this->dt_PE = new DateTime($dt->format("Y-m-d H:i:50"));
			$i=&$this->pause_index;
			if (isset($this->pause[$i]["PE"])) {
				$i++; // Hier ist ein Fehler in der Reihenfolge aufgetreten
				$this->pause[$i]=array();
			}
			$this->pause[$i]['PE']=$this->dt_PE;
			$i++;
		}
		
			
	}
	
			
}

?>
