<?php
/*
	Farben: Ton, Blau Hell, Dunkel, Mittel
	        Licht: Gelb Hell Dunkel, Mittel
			An+Abreise: Grün oder Lila
			
			
	+ Button: Projekt anlegen, Arbeitszeit, Nachrichten
*/		
	
class Projekt_kalender {
	public $cellwidth="44px";
	public $cellheight="44px";
	private $event=array();
	
	private $select_start=""; 
	private $select_ende="";
	
	public $bordercolor_selection="#FF7777";
	public $backgroundcolor_selection="RGBA(40,40,40,0.5)";
	public $backgroundcolor_reisetag="#66FF66";
	// public function switchTon() {
	// public function switchLicht() {
	
	public $usage=0; //0 = select mehrere Felder //1=nur ein Feld direkt änderen 
		
	public function setMobile() {
		// Handy 320 bzw 360

		$this->cellwidth="44px";
		$this->cellheight="44px";		
	}

	public function setPC() {
		$this->cellwidth="100px";
		$this->cellheight="100px";		
	}
	
	
	public function addEvent($event) {		

		if (empty($event['ende']) and empty($event['start'])) return;
				
		if (empty($event['ende'])) {
			$event['ende']=$event['start'];
		}
		if (empty($event['start'])) {
			$event['start']=$event['ende'];
		}
		$dt_von=new DateTime($event['start']);
		$dt_bis=new DateTime($event['ende']);
		
		if ($dt_von == $dt_bis)  {
			if (empty($this->event[$dt_von->format("Y-m-d")]['style'])) {
				$this->event[$dt_von->format("Y-m-d")]['style']="border-radius:25px;";
			}
		} else {
			if (empty($this->event[$dt_von->format("Y-m-d")]['style'])) {
				$this->event[$dt_von->format("Y-m-d")]['style']="border-top-left-radius:25px;border-bottom-left-radius:25px;";
			}
			if (empty($this->event[$dt_bis->format("Y-m-d")]['style'])) {
				$this->event[$dt_bis->format("Y-m-d")]['style']="border-top-right-radius:25px;border-bottom-right-radius:25px;";
			}
		}
		$dt=$dt_von;
	
		// while ($dt->format("Y-m-d") <= $dt_bis->format("Y-m-d")) {
		while ($dt <= $dt_bis) {
			$date=$dt->format("Y-m-d");
			// echo $count++.":".$dt->format("Y-m-d")." ".$dt_bis->format("Y-m-d")."##";
			foreach($event as $k => $v) {
				// echo $k;
				//if ($k == "color" and !empty($this->event[$date][$k])) {
				if (empty($this->event[$date][$k])) { 	
					$this->event[$date][$k]=$v;
				}
			}
			$dt->modify("+1 day");
		}
		
	}
	private function setSelect() {
		
		if (empty($_POST['kalender_tag'])) {
			return;
		}
		if (!empty($_POST['kalender_select_start'])) {
			if ($_POST['kalender_tag'] == $_POST['kalender_select_start']) {
				$_POST['kalender_select_start']="";
				return;
			}
		}
			
		if (!empty($_POST['kalender_select_ende'])) {
			if ($_POST['kalender_tag'] == $_POST['kalender_select_ende']) {
				$_POST['kalender_select_ende']="";
				return;
			} 				
		}
				
		
		if (empty($_POST['kalender_select_start'])) {
			$_POST['kalender_select_start']=$_POST['kalender_tag'];
		} else 
		if (empty($_POST['kalender_select_ende'])) {
			$_POST['kalender_select_ende']=$_POST['kalender_tag'];
		} else {
			$_POST['kalender_select_start']=$_POST['kalender_tag'];
			$_POST['kalender_select_ende']="";
		}
	}
	private function select() {
		$this->setSelect();
		// if (empty($_POST['kalender_select_start']) and empty($_POST['kalender_select_ende'])) {
		// 	return;
		// }
		if (!empty($_POST['kalender_select_start']) and !empty($_POST['kalender_select_ende'])) {
			if ((new DateTime($_POST['kalender_select_start'])) > (new DateTime($_POST['kalender_select_ende']))) {
				$p=$_POST['kalender_select_start'];
				$_POST['kalender_select_start']=$_POST['kalender_select_ende'];
				$_POST['kalender_select_ende']=$p;
			}
			
			$dt=new DateTime($_POST['kalender_select_start']);
			$dt_von=new DateTime($_POST['kalender_select_start']);
			$dt_bis=new DateTime($_POST['kalender_select_ende']);
			while ($dt <= $dt_bis)  {
				$this->event[$dt->format("Y-m-d")]['border']=true;//$this->bordercolor_selection;		
				$dt->modify("1 day");
			}
		} else 
		if (!empty($_POST['kalender_select_start']) and empty($_POST['kalender_select_ende'])) {
			$this->event[$_POST['kalender_select_start']]['border']=true; // $this->bordercolor_selection;		
		} else 
		if (empty($_POST['kalender_select_start']) and !empty($_POST['kalender_select_ende'])) {
			$this->event[$_POST['kalender_select_ende']]['border']=true; // $this->bordercolor_selection;		
		}
	}
	
	public function show() {
		if ($this->usage==0) {			
			$this->select();
		}
		
		$width=&$this->cellwidth;
		$height=&$this->cellheight;

		$dt=new DateTime();
		if (empty($_POST['kalender_datum'])) {
			$dt=new DateTime();
			$_POST['kalender_datum']=$dt->format("Y-m-d");
		} else {
			$dt=new DateTime($_POST['kalender_datum']);
		}
		if (isset($_POST['monat_weiter'])) {
			$dt->modify("+1 month");
			$_POST['kalender_datum']=$dt->format("Y-m-d");
		}
		if (isset($_POST['monat_vor'])) {
			$dt->modify("-1 month");
			$_POST['kalender_datum']=$dt->format("Y-m-d");
			// echo "vor";
		}
		

		$monat=$dt->format("n");
		$monatstage=$dt->format("t");
		$monat_text=array("","Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember");
		$jahr=$dt->format("Y");
		$first_dt=new DateTime($jahr."-".$monat."-01 00:00:00");
		$dt=$first_dt;
		$first_day=$first_dt->format("N"); // Wochentag = 1 Minatg 7 Sonntag
		$go=false;
		$tag=1;
		if (empty($_POST['kalender_select_start'])) $_POST['kalender_select_start']="";
		if (empty($_POST['kalender_select_ende'])) $_POST['kalender_select_ende']="";
		
		$html ="";
		$html.= '<link rel="stylesheet" href="projekt_kalender.css">';
		// $html.= '<form method="POST" action="projekt_kalender.php">';
		$html.= '<input type="hidden" name="kalender_datum" value="'.$_POST['kalender_datum'].'">';
		$html.= '<input type="hidden" name="kalender_select_start" value="'.$_POST['kalender_select_start'].'">';
		$html.= '<input type="hidden" name="kalender_select_ende" value="'.$_POST['kalender_select_ende' ].'">';

		$html.= '<table id="kalender" cellspacing=0 cellpadding=0 border=0 style="table-layout:fixed;empty-cells:show;border:1px solid blue;">';
		$html.= '<tr>';
		$html.= '<th><button type="submit" name="monat_vor" style="border:0px solid transparent;margin:0;padding:0; width:38px;height:38px;background-color:white;cursor:pointer;"><img src="../img/pfeil-links.png"  height="38px"></button></th>';
		$html.= '<th colspan=5>'.$monat_text[$monat]." ".$jahr.'</th>';
		$html.= '<th><button type="submit" name="monat_weiter" style="border:0px solid transparent;margin:0;padding:0; width:38px;height:38px;background-color:white;cursor:pointer;"><img src="../img/pfeil-rechts.png" height="38px"></button></th>';
		$html.= '</tr>';
		$html.= '<tr><th>Mo</th><th>Di</th><th>Mi</th><th>Do</th><th>Fr</th><th>Sa</th><th>So</th></tr>';
		do {
			$html.= '<tr>';
			for ($i=1;$i<=7;$i++) {
				if ($i==$first_day) {
					$go=true;
				}
				$color="#000000";
				$bgcolor="#DDDDDD";
				$bordercolor="grey";

				$border_radius="";
				if (!empty($this->event[$dt->format("Y-m-d")]['style'])) {
					$border_radius=$this->event[$dt->format("Y-m-d")]['style']; // ="border-radius-top-left:25;";
				}
				// echo $dt->format('Y-m-d')."<br>";
				if (!empty($this->event[$dt->format('Y-m-d')]['color'])) {
					$bgcolor=$this->event[$dt->format('Y-m-d')]['color'];
					// echo "hier";
				}
				if (!empty($this->event[$dt->format('Y-m-d')]['border'])) {
					$bgcolor=$this->backgroundcolor_selection;
					$bordercolor=$this->bordercolor_selection; //$this->event[$dt->format('Y-m-d')]['border'];
				}
				$html.= '<td style="background-color:#DDDDDD;">';
				// Gut: $html.= '<button id="kalendertag" type="submit" name="kalender_tag" value="'.$dt->format('Y-m-d').'" style="border:1px solid '.$bordercolor.';font-weight:bold;margin:0;padding:0; width:'.$width.';height:'.$height.';background-color:'.$bgcolor.';cursor:pointer;">';
				
				if ($tag <= $monatstage and $go == true) {
					$button="submit";
				} else {
					$button="button";
				}
				$html.= '<button id="kalendertag" type="'.$button.'" name="kalender_tag" value="'.$dt->format('Y-m-d').'" style="position:relative;border:1px solid '.$bordercolor.';'.$border_radius.'font-weight:bold;margin:0;padding:0; width:'.$width.';height:'.$height.';background-color:'.$bgcolor.';cursor:pointer;">';
				// $html.= '<'.$button.' id="kalendertag" type="submit" name="kalender_tag" value="'.$dt->format('Y-m-d').'" style="position:relative;border:1px solid '.$bordercolor.';'.$border_radius.'font-weight:bold;margin:0;padding:0; width:'.$width.';height:'.$height.';background-color:'.$bgcolor.';cursor:pointer;">';

				
				// $html.='<div style="margin:0; padding:0; position:relative;width:40px;height:40px;background-color:magenta;">';
				/* $html.= '<div style="color:'.$color.';
								 background-color:'.$bgcolor.';
								 font-weight:bold;
								 display:table-cell;	
								 width:calc('.$width.' - 2px);
								 height:calc('.$height.' - 2px);
								 text-align:center;
								 vertical-align:middle;
								 ">';
								 
*/
				$bg=$this->backgroundcolor_reisetag;
				if (!empty($this->event[$dt->format('Y-m-d')]['left'])) {
					$size="10px";
					if (strlen($this->event[$dt->format('Y-m-d')]['left'])>2) {
						$size="20px";
					}
					$html.='<div style="margin:0; padding:0; display:inline-block; position:absolute;bottom:0;left:0;width:'.$size.';height:10px;background-color:'.$bg.';border: black solid 1px;font-size:8px;white-space:nowrap;">'.$this->event[$dt->format('Y-m-d')]['left'].'</div>';
				}
				
				if (!empty($this->event[$dt->format('Y-m-d')]['right'])) {
					$size="10px";
					if (strlen($this->event[$dt->format('Y-m-d')]['right'])>2) {
						$size="20px";
					}
					$html.='<div style="margin:0; padding:0; display:inline-block; position:absolute;bottom:0;right:0;height:10px;width:'.$size.';background-color:'.$bg.';border: black solid 1px;font-size:8px;white-space:nowrap;"><nobr>'.$this->event[$dt->format('Y-m-d')]['right'].'</nobr></div>';
				}

				if ($tag <= $monatstage and $go == true) {
					$html.= $tag;
					$dt->modify("1 day");
					$tag++;
				}
				
				// $html.='<div style="margin:0; padding:0; display:inline-block; position:absolute;bottom:0;left:0;width:10px;height:10px;background-color:transparent;border: black solid 1px;font-size:8px;">A</div>';
				// $html.= '</div>';
				$html.= '</button>';
				$html.= '</td>';
				
			}
			$html.= '</tr>';
		} while ($tag <= $monatstage);

		$html.= "</table>";
		// $html.= "</form>";
		return $html;
	}
}
?>
					 
					 
	