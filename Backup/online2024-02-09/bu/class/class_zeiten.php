<?php
/*

datei:
usernr
time       Stempelzeit
typ:       A = Arbeit, P=Pause, U = Urlaub, K=Krank, F=Frei Unbezahlt // S = Start E=Ende 
		   Beispiele: AS PS PE PS PE AE, US UE KS KE
*/

class zeiten {
	private $db = "";
	public  $row = array();
	private $usernr=0;
	public  $output = array();
	public  $result;

	private $workhours_each=array(0,8,8,8,8,8,0); // w: 0=Sonntag, 1=Montag 6=Samstag
	private $entree   = "2023-05-01";             //------------------------------ Aus Datei holen
	private $startup  = "2023-05-01";             //------------------------------ Aus Datei holen
	
	
	public function __construct(&$db) {
		$this->db=$db;
	}

	/*
		Benutzer (Mitarbeiternr) vorbereiten
	*/
	public function setUser($usernr) {
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
		Jahresurlaub des Mitarbeiters festlegen  
		Type: int 
	*/
	public function setUrlaub($t) {
		$this->output['urlaubtage_gesamt']=$t;
	}
	
	public function addTime($type,$time) {
		$time=$this->toDateTime($time);
		
		// if (isString($time)) {
		//	$time=DateTime($time);
		// }
		
		$request="insert into `bu_zeit` set `usernr`='".$this->usernr."',`type`='".$type."',`time`= '".$time->format("Y-m-d H:i:s")."'"; 
		$result = $this->db->query($request);
		if ($result) {
			$dt=new DateTime();
			$this->row['recnum']=$this->db->insert_id;
			$this->row['type']=$type;
			$this->row['time']=$time->format("Y-m-d H:i:s");
		}
		return $result;
	}
	
	/* 
		Zeit hinzufügen
	*/
	public function add($type) {
		
		// Doppelpost verhindern
		if ($type == $this->getLastEntry()) {
			return;
		}
		$request="insert into `bu_zeit` set `usernr`='".$this->usernr."',`type`='".$type."',`time`= now()"; 
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

	private function getDiff($dt1,$dt2) {
		$diff = $dt2->diff($dt1);		
		$s=(($diff->days*24+$diff->h)*60+$diff->i)*60+$diff->s;
		return $s;
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
		$this->output['AS']="keine";
		$this->output['inPause']=false;
		$this->output['inArbeit']=false;
		$this->output['pause_gesamt']="keine";
		$this->output['pause_jetzt'] = "keine";
		// $this->output['urlaubtage_gesamt']="30";  //---------------------------> 30 aus datei laden
		$this->output['kranktage']="keine";
		$this->output['kranktage_netto']="keine";
		$this->output['urlaubtage']="0";
		$this->output['urlaubtage_netto']="0";
		$this->output['resturlaub']=$this->output['urlaubtage_gesamt'];
		$this->output['jahresstunden'] = "0";
		$this->output['ueberstunden']= "0";

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

		if (empty($start)) {
			return;
		}
		
		// echo "Start=".$start."<br>";
		
		/*
			letztes Arbeitsende finden
		*/
		$request="select max(time) as max from `bu_zeit` where `usernr`='".$this->usernr."' and `type`='AE'";
		$result = $this->db->query($request);
		$row = $result->fetch_assoc();
		$ende = $row['max'];
		// echo "ende=".$ende."<br>";
		// exit;

		if (empty($ende)) {
			$this->output['inArbeit']=true;
		} else 			
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
					$this->output['AS'] = $date2->format("H:i:s"); //d.m.Y
					$ax+=$this->getDiff($date1,$date2);  // AX in Sekunden
				}
				
				/*
					Momentane Pausenzeit : Start
				*/
				if ($row['type'] == "PS") {				
					$pause_start = $row['time'];
					$date1 = new DateTime($pause_start);
					$this->output['PS']=$date1->format("H:i:s"); //d.m.Y
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
					$pause_gesamt+=$this->getDiff($date1,$date2); // in sekunden
					$pause_start=0;
				}
				
			}
			
			// if ($pause_start) {
			// echo "Pause_start:".$pause_start."<br>";		

			/* 
				Pause läuft
			*/				
			if ($pause_start>0) {
				$date1 = new DateTime();
				$date2 = new DateTime($pause_start);
				$diff = $date2->diff($date1);
				//# $pause_teil = $diff->h*60+$diff->i; // in Minuten
				//# $pause_gesamt+=$pause_teil;         // in Minuten
				$pause_gesamt += $this->getDiff($date1,$date2); //##
				// $this->output['pause_jetzt'] = sprintf("%d Stunden, %d Minuten",$diff->h,$diff->i);
				$this->output['pause_jetzt'] = $this->display_time($diff->days,$diff->h,$diff->i);
				$this->output['inPause'] = true;
			/*
				Pause ist beendet
			*/
			} else {
				$this->output['pause_jetzt'] = "keine Pause";
				$this->output['inPause'] = false;
				
				// $this->output['pause_gesamt']=$pause_gesamt;	
			}
			if ($pause_gesamt>0) {
				// $px=$pause_gesamt*60;
				
				$px=$pause_gesamt;
				$mg=$pause_gesamt/60; //minuten gesamt
				$h=(int)$mg/60;       //Stunden
				$m= $mg%60;		      //Minuten
				$d=(int)$h/24;        //Tage
					
				$this->output['pause_gesamt'] =  $this->display_time($d,$h,$m);
			}
		
		}

		/*
			Statistiken  Jahresangaben
			
			- Kranktage
			- Urlaubstage
			- Ueberstunden
		*/
// Hier fehlt noch was

		$ax=0;
		$px=0;
		$ux=0;
		$kx=0;
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
		$this->output['resturlaub']=$this->output['urlaubtage_gesamt'] - $ux_netto;
		
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
	
		
}
?>
