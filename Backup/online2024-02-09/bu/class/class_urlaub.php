<?php
/*
Urlaub
----------
Recnum
Mitarbeiternr   Die Reale Mitarbeiternummer (BU_Mitarbeiter.nr)
Firmanr
Datumzeit von 
Datumzeit bis	
Art              0=Urlaub, 1=Krank, 2=unbezahlter Urlaub
Status           0=Beantragt, 1=Genehmigt, 2=abgelehnt (nur bei Urlaub)
                 0=Krankenschein fehlt, 1=Krankenschein vorhanden (bei Krank)
info             z.B. Urlaub abgelehnt aufgrund hoher Arbeitsaufkommen
gelesen          0=gelesen, 1=ungelesen (für die App)
=============================================================================
*/

class Urlaub {
	// Datenbank
	private $db;             // Datenbank
	public $result;          // Datenbank->Zeiger
	public $row=array();     // Entschlüsselte Reihe
	
	// Keys
	private $recnum;
	private	$mitarbeiternr;
	private $firmanr;
	
	

	// Fixed
	private $status_text=array(
		array("beantragt","genehmigt","abgelehnt"), // Urlaub
		array("Krankenschein fehlt","Krankenschein eingereicht") // Krank
	);
	private $art_text=array("Urlaub","Krank","unbezahlter Urlaub");
	
	// Vorbereitungen
	public function __construct($db) {
		$this->db=$db;
		$this->recnum=0;
		$this->mitarbeiternr=0;
		$this->firmanr=$_SESSION['firmanr'];
	}
	
	public function setMitarbeiternr($nr) {
		$this->row['mitarbeiternrnr']=$nr;
		$this->mitarbeiternr=$nr;
	}

	public function loadByRecnum($recnum) {
		$request="select * from `bu_urlaub` where `recnum` = '".$recnum."'";
		$this->result = $this->db->query($request);
		$this->row=$this->result->fetch_assoc();
		return $this->row;
	}
	public function loadByUrlaubZeitraum($von,$bis) {
		$where="(`von` between '".$von."' and '".$bis."') or (`bis` between '".$von."' and '".$bis."')";
		return $this->load(0,"",$where);
	}

	public function loadByKrankZeitraum($von,$bis) {
		$where="(`von` between '".$von."' and '".$bis."') or (`bis` between '".$von."' and '".$bis."')";
		return $this->load(1,"",$where);
	}
	
	
	public function loadUrlaub($status) {
		if (isset($status)) {
			$this->load(0,$status);
		} else {		
			$this->load(0);
		}
		return $this->result;
		
	}
	public function loadKrank($status) {
		if (isset($status)) {
			$this->load(1,$status);
		} else {		
			$this->load(1);
		}
		return $this->result;
	}
	
	// art: 0 = Urlaub, 1=Krank
	// status: Urlaub: 0=beantragt,1=genehmigt, 2=nicht genehmigt
	//
	private function load($art,$status="",$where="") {
		if ($art == 1) {
			$where1=" and `art`='1'";
		} else {
			$where1=" and (`art`='0' or `art`='2')";
		}
		if (!empty($where)) {
			$where1.=" and ($where)";
		}			
		
		$request="select * from `bu_urlaub` where `mitarbeiternr` = '".$this->mitarbeiternr."' and `firmanr`='".$this->firmanr."' ".$where1;
		if (!empty($status)) {
			$request.=" and `status`='".$status."'";
		}	
		$request.=" order by `von`";

		$this->result = $this->db->query($request);
		return $this->result;
	}
	
	public function next() {
		$this->row=$this->result->fetch_assoc();
		return $this->row;
	}
		
	
	
	// 
	// Ausgaben
	// 
	public function getUrlaubVon() {		
		return DateTime($this->row['von']);
	}
	public function getUrlaubBis() {		
		return DateTime($this->row['bis']);
	}
	public function getStatusText() {
		$art=$this->row['art'];
		return $this->status_text[$art];
	}



	/*
		Automatsich änderen oder neu anlegen
	*/
	public function save($row) {
		if (isset($row['recnum'])) {
			return $this->update($row);
		} else {
			return $this->insert($row);
		} 		
	}
	

	/*
		Urlaub verändern
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
		
		$request="update bu_urlaub set $set where `recnum`='".$recnum."'";	
		$result = $this->db->query($request);
		
		$this->row['recnum']=$recnum;
		return $result; // Arrayoffset = null ?
		
	}
	/*
		Urlaub hinzufürgen einfügen
	*/
	public function insert($row) {
		unset ($row['recnum']);  // zur Sicherheit

		$values="";
		$keys="";
		foreach($row as $k => $v) {
			if ($values != "") {
				$values.=",";
				$keys.=",";
			}
			$values.= "'".$this->db->real_escape_string($v)."'";
			$keys  .= "`".$k."`";
		}
		
		$request="insert into `bu_urlaub` ($keys) values ($values)";	
		$result = $this->db->query($request);
		if ($result) {
			$this->row['recnum']=$this->db->insert_id;
		} 
		return $result;
	}

	public function del($recnum) {
		$request="delete from `bu_urlaub` where `recnum` = '$recnum'";	
		$result = $this->db->query($request);
		return $result;
	}
		
}



?>
