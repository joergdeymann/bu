<?php
// ==================================================================
// bu_sendmail
// ==================================================================
/*
	0. recnum
	----------------
	1. firmanr
	2. vorlagenr
	3. mahnstufe
	----------------
	4. subject
	5. content
	6. signature = Wenn leer dann die von der Firma nehmen
	
*/

class db_sendmail {
	
	// Keys
	private $firmanr;
	private $vorlagenr;
	private $mahnstufe;
	
	// Database
	private $db;

	// Fields
	public $row = array();
	
	/*
		Prepare Database
	*/
	public function __construct(&$db) {
		$this->firmanr=$_SESSION['firmanr'];
		$this->vorlagenr=0;
		$this->mahnstufe=0;
		$this->row=array();		
		$this->db = $db;
	}

	public function getContent() {
		return $this->row['content'];
	}

	public function getSubject() {
		return $this->row['subject'];
	}
	public function getSignature() {
		return $this->row['signature'];
	}
	
	/*
		Add necessary Keys for Saving
	*/
	private function append_keys(&$fields) {
		if (empty($fields['firmanr'])) {
			$fields['firmanr']=$this->firmanr;
		}
		if (empty($fields['vorlagenr'])) {
			$fields['vorlagenr']=$this->vorlagenr;
		}
		if (empty($fields['mahnstufe'])) {
			$fields['mahnstufe']=$this->mahnstufe;
		}
		return;
	}

	/* 
		Set Key: Vorlage
	*/
	public function setVorlage($k) {
		// vorlagenr war vorher vorlage
		$this->vorlagenr=$k;
		$this->row['vorlagenr']=$k;
	}
	
	/*
		Set Key: Mahnstufe
	*/		
	public function setMahnstufe($k) {
		$this->mahnstufe=$k;
		$this->row['mahnstufe']=$k;
	}
	
	public function init() {
		/*
			Speziell für die Mahnung ein Text
		*/
		$query = 'select * from bu_sendmail where firmanr="'.$this->firmanr.'" and vorlagenr="'.$this->vorlagenr.'" and mahnstufe="'.$this->mahnstufe.'"';
		$result = $this->db->query($query);
		if ($result) {
			$this->row = $result->fetch_assoc();
			if ($this->row)   return;
			
		}
		/*
			Alternative falls keine für Mahnungen vorhanden
		*/
		$query = 'select * from bu_sendmail where firmanr="'.$this->firmanr.'" and vorlagenr="'.$this->vorlagenr.'" order by mahnstufe';
		$result = $this->db->query($query);
		if ($result) {
			$this->row = $result->fetch_assoc();			
			if ($this->row)   return;
		}

		echo "class_db_sendmail: Keine Vorlage für den Mailversand gefunden!";
		exit;
		return;	
	}
	
	/* 
		If dataset is already loaded , then update else insert		
	*/
	public function save(&$fields) {
		if (isset($fields['recnum']) && $fields['recnum']>0) {
			$this->update($fields);
		} else {
			$this->insert($fields);
		}
		foreach($fields as $k => $v) {
			$this->row[$k]= $v;
		}
	}		

	/*
		Insert new Record
	*/
	public function insert($fields) {
		// INSERT
		$this->append_keys($fields);
		unset($fields['recnum']);

		/* 
			Korrekte und sichere übergabe mit real_escape_string
		*/
		foreach ($fields as $k => $v) {
			$fields[$k]=$this->db->real_escape_string($v);
		}
		$keys   = array_keys($fields);
		
		$request="insert into `bu_sendmail` (`".join("`,`",$keys)."`) values ('".join("','",$values)."')";
		$result = $this->db->query($request);
	}
	
	/*
		Update Record
	*/
	public function update($fields) {
		// UPDATE
		$this->append_keys($fields);

		$set="";
		foreach($fields as $k => $v) {
			$this->row[$k]= $v;
			if ($set) {
				$set.=",";
			}
			$set.="`".$k."`='".$this->db->real_escape_string($v)."'";
		}
		// by RECNUM
		$request = 'update `bu_sendmail` set '.$set.' where recnum='.$this->recnum;		
		$result = $this->db->query($request);
	}
	
		
	
}
?>

