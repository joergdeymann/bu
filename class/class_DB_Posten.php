<?php

class DB_Posten {
	private $db;
	private $result;
	
	private $fields = array();
	public $typ=0; // 0 = rechnung, 1=Angebot
	public function __construct(&$db) {
		$this->db=$db;
	}
	
	public function set($k,$v) {
		$this->fields[$k]=$v;
	}
	
	public function start() {
		$request='select * from `bu_re_posten` where renr="'.$_POST['renr'].'" and firmanr="'.$_SESSION['firmanr'].'" and typ="'.$this->typ.'" order by `pos`';
		// echo $request."<br>";	
		$this->result = $this->db->query($request) or die(mysql_fehler());
		// echo '<pre>'.var_dump($this->result).'</pre>'; 
	}
	/* 
		Weitere Felder aus der Artikelliste holen
	*/
	public function startExtend() {
		$request ='SELECT * FROM `bu_re_posten` LEFT JOIN `bu_artikel` ON bu_re_posten.artikelnr = bu_artikel.artikelnr WHERE renr="'.$_POST['renr'].'" and firmanr="'.$_SESSION['firmanr'].'" and bu_re_posten.typ="'.$this->typ.'" order by `pos`';
		// echo $request."<br>";	
		$this->result = $this->db->query($request) or die(mysql_fehler());
	}	
	
	public function getNext() {
		$this->fields = $this->result->fetch_assoc();
		return $this->fields;
	}
	
	/*
		Höchste Position herausfinden
	*/
	
	private function getMaxPos(&$fields) {
		$request ='SELECT max(POS) as mx from bu_re_posten where renr="'.$fields['renr'].'" and firmanr="'.$_SESSION['firmanr'].'" and typ="'.$this->typ.'"';
		$result = $this->db->query($request) or die(mysql_fehler());
		$row=$result->fetch_assoc();
		return $row['mx'];
	}

		
	// public function load($posten) [
	// }
	
	/* 
		Neuer Posten erstellen
		$fields muss Artikelnummer enthalten
	*/
	public function insert(&$fields) {
		$fields["pos"] = (int)$this->getMaxPos($fields)+1;
		$fields["typ"] = (int)$this->typ;
		$keys="";
		$values="";
		foreach($fields as $k => $v) {
			if ($keys == "") {
				$keys   = $k;
				$values = $v;
			} else {
				$keys    .= '`,`'.$k;
				$values  .= '","'.$v;
			}
		}
		
		$request ='INSERT INTO `bu_re_posten` (`'.$keys.'`) VALUES ("'.$values.'")'; 
		$result = $this->db->query($request) or die(mysql_fehler());		
	}

	/*
		Where aus den Feldern vorbereiten
	*/
	private function getWhere(&$fields) {
		/*
			Benutzte eingeloggte Firma automatisch
		*/
		if (empty($fields['firmanr']) && !empty($_SESSION['firmanr'])) {
			$fields['firmanr']=$_SESSION['firmanr'];
		}

		/*
			Index
		*/
		if (isset($fields['recnum'])) {
			$where='WHERE `recnum` = '.$fields['recnum'];
		} else 	
		if (isset($fields['renr']) && isset($fields['firmanr'])) {
			$where ='WHERE `renr` ="'.$fields['renr'].'"';
			$where.=' AND `firmanr` = "'.$fields['firmanr'].'"';
			$where.=' AND `pos` = "'.$fields['pos'].'"';
		}
		$where.=' AND `typ`="'.$this->typ.'"';
		return $where;
	}
	
	public function update(&$fields) {
		$where = $this->getWhere($fields);

		$r="";
		foreach ($fields as $k => $v) {
			$v = str_replace('"','\"',$v); // Bei Texten Anfuehrungszeichen maskieren
			if ($r !=  "") {
				$r.=",";
			}
			$r .= '`'.$k.'`= "'.$v.'"';
		}
		
		$request='UPDATE `bu_re_posten` SET '.$r.' '.$where;
		$result = $this->db->query($request) or die(mysql_fehler());		
			
			
	}
	/*
		Löschen bestimmter Posten
	*/
	public function delete(&$fields) {
		$where = $this->getWhere($fields);

		$request='DELETE FROM `bu_re_posten` '.$where;
		// echo $request;
		$result = $this->db->query($request) or die(mysql_fehler());		

	}		
}
?>
