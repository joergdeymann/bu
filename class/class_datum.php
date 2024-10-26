<?php
// class datum extends Datum {
// }

class Datum {
	public $datum;

	public $jahr;
	public $monat;
	public $tag;
	
	public $woche;  // Woche im Jahr
	
	
	public $stunde;
	public $minute;
	public $sekunde;

	public $stunden=0;
	public $minuten=0;
	public $sekunden=0;
	
	// function __construct($datum=new DateTime()) {
	function __construct($datum="") {
		if (empty($datum)) $datum=(new DateTime())->format("Y-m-d H:s");
		$this->datum=new DateTime($datum);
		$this->woche=$this->datum->format("W");
		$this->jahr=$this->datum->format("Y");
		$this->monat=$this->datum->format("m");
		
		$this->stunde=$this->datum->format("H");
		$this->minute=$this->datum->format("i");
		$this->sekunde=$this->datum->format("s");
	}		

	/*
		Datumrückgabe leichter machen
	*/
	public function getDatum($f="d.m.Y") {
		return $this->datum->format($f);
	}
	
	/* 
		Woche Jahr -> Datum
	*/
	public function setWoche($w,$j) {
		
		$w = sprintf("%02d",$w);
		$t = strtotime("$j-W$w"); // Maximal 52 Kalenderwochen

		$dt=new DateTime(date('d.m.Y', $t));

		// Wenn Woche 1, dann wird ein Datum im letzten Jahr erstellt
		$jahr=$dt->format("Y");
		$monat=$dt->format("m");
		if ($dt->format("m") . $dt->format("W") == "1201") {
			$jahr++;
			$monat=1;
		}
		
		$this->datum=$dt;
		$this->jahr=$jahr;
		$this->woche=$w;
		$this->monat=$monat;
		
		return $this->getDatum();		
	}
	
	/* 
		Monat Jahr -> Datum
	*/
	public function setMonat($m,$j) {
		$d = sprintf("%04d-%02d-01",$j,$m);

		$dt=new DateTime($d);

		$this->datum=$dt;
		$this->jahr=$dt->format("Y");
		$this->woche=$dt->format("W");
		$this->monat=$dt->format("m");
		
		return $this->getDatum();
	}
	
	private function calcSekunden($diff) {
		return (($diff->days*24+$diff->h)*60+$diff->i)*60+$diff->s;
	}
	
	public function sub($datum = "",$datum2 ="") {
		if (empty($datum)) $datum=new DateTime();
		
		// 2Parameter
		if (!empty($datum2)) {
		    $diff=$datum->diff($datum2);
			$this->sekunden-=$this->calcSekunden($diff);
			return;
		}

		// 1 Parameter
		if (gettype($datum) == "number") {
			$this->sekunden-=$datum;
			return;
		} else {
			$this->sub($this->datum,$datum);
			return;
		}		
		
	}
	
	public function add($datum = "",$datum2 ="") {
		if (empty($datum)) $datum=new DateTime();
		// 2Parameter
		if (!empty($datum2)) {
		    $diff=$datum->diff($datum2);
			$this->sekunden+=$this->calcSekunden($diff);
			return;
		}

		//	 1 Parameter
		if (gettype($datum) == "number") {
			$this->sekunden+=$datum;
			return;
		} else {
			$this->add($this->datum,$datum);
			return;
		}		
		
	}

	public function getTime($typ) {
		$rest=$this->sekunden;
		$s=$rest % 60;


		// echo "<br><br>Datum.getTime: S:$rest/$s<br>";

		$rest=(int)($rest / 60);
		$m=$rest % 60;
		// echo "Datum.getTime: M:$rest/$m<BR>";

		$rest=(int)($rest / 60);
		$h=$rest % 24;
		// echo "Datum.getTime: H: $rest/$h<BR>";
		
		$rest=(int)($rest / 24);
		$d=$rest;

		$value=0;
		if ($typ == "s") {
			$value=$s;
		}
		if ($typ == "i") {
			$value=$m;
		}
		if ($typ == "H") {
			$value=$h;
		}
		//echo sprintf("%5d - %02d:%02d:%02d Value=%02d",$this->sekunden,$h,$m,$s,$value)."<br>";
		return sprintf("%02d",$value);		
	}
}
/*
echo date('d.m.Y', $w01), "<br>";
echo "woche:".$dt->format("W")."<br>";
echo $dt->format("m") . $dt->format("W") . "<br>";
echo "Jahr:$jahr<br>";
exit;
*/

/*
$d=new DateTime("11.05.2023+20 days");
echo $d->format("d.m.Y");
exit;

*/

?>
