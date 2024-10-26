<?php
/*
	Ausgaben 
*/
class Output {
	// Eingabefelder könnete man zu stdio.class hinzufügen
	public $selection; // Array mit Daten die angezeigt und übergeben werden soll 
	public $select;    // Vor-Auswahl der obigen Daten
	public $format;    // Ausgabeformat für Dateien / Eingaben
	public $layout="";    // Momentanes Layout für eine Zeile Eingaben
	public $formStart;    // <form method="POST" action="'.$_SERVER['SCRIPT_NAME'].'">';
	public $formEnd;      //</form>';
	public $formAction;   // Prefered Action
	
	
	
	
	// Anderes
	private $title;
	public function __construct() {
		$this->title="";
		
		$this->setFormAction();
		// $this->formStart='<form method="POST" action="'.$_SERVER['SCRIPT_NAME'].'">';
		$this->formEnd='</form>';
	}
	


	// Wenns geht den Referer anspringen sonst die Angabe/SELF nehmen
	public function setFormListAction($action="")  {
		if (empty($action)) {
			$action=$_SERVER['SCRIPT_NAME'];
		}

		if ((count($_POST) > 0) and !empty($_SERVER['HTTP_REFERER'])) {
			$action=$_SERVER['HTTP_REFERER'];
		}
		$this->formStart='<form method="POST" action="'.$action.'">';
	}
	
	// Wenn Angabe gegeben ist dann diese nehemn ansonsten Script Name nehemn, bei Aufruf auf ander Datei getSubmit() mit 3 Parametern nutzen
	public function setFormAction($action="")  {
		if (empty($action)) {
			$action=$_SERVER['SCRIPT_NAME'];
		}

		$this->formStart='<form method="POST" action="'.$action.'">';
		return $this->formStart;
	}
	
	public function header($title="") {
		if (empty($title)) {
			$title=$this->title;
		} else {
			$this->title=$title;
		}
		
		$html ='<!doctype html>';
		$html.='<html lang="de">';

		$html.='<head>';
		$html.='<meta charset="utf-8">';
		$html.='<link rel="icon" type="image/vnd.microsoft.icon" href="favicon.ico">';
		
		$html.="<title>".$title."</title>";

		$html.='<link rel="stylesheet" href="standart.css">';
		$html.='<link rel="stylesheet" href="menu.css">';
		$html.='</head><body>';
		
		
		return $html;
	}
	
	public function kopf($text="") {
		$html="";
		$html.= '<div style="margin-left:1%; margin-right:1%;"><div style="text-align:right;">';
		$html.= '<img alt="Logo" src="img/logo.png" style="height:100px;">';
		$html.= '</div>';
		$html.= '<h1 style="margin-top:0px;">$text</h1>';
		$html.= '</div>';
		return $html;
	}

	//
	// Header für den Download
	//
	public function header_csv($filename="adressen.csv") {
		
		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=\"".$filename."\"");
	    // readfile($dir.$file);
		// echo "Hier kommt der Inhalt hin";
    }

	// UTF Dokumentencode senden
	public function utf() {
		return chr(239) . chr(187) . chr(191);
	}

	public function print_header() {
		$html ='<!doctype html>';
		$html.='<html lang="de">';

		$html.='<head>';
		$html.='<meta charset="utf-8">';
		// $html.='<link rel="stylesheet" href="print_doku.css">';
		$html.='<link rel="stylesheet" href="print_standart.css">';
		$html.='<link rel="stylesheet" href="print_arbeitsvertrag.css">'; //  Hier ist der FF Fehler
		$html.='<link rel="stylesheet" href="print_dokumente.css">';	
		$html.='<link rel="stylesheet" href="print_raw.css">';
		$html.='</head>';		
		return $html;
	}
	
	public function print_body($body,$auto=true) {

		// $html ='<body id="vorlage" onload="window.print()">';
		if ($auto == true) {
			$html ='<body onload="window.print()">';
		} else {
			$html ='<body>';
		}
		// $html='<body>';
		$html.=$body;
		$html.='</body>';
		return $html;
	}
	
	public function print_footer() {
		$html="</html>";
		return $html;
	}
	
	public function print_newpage() {
		// Dektiviert erstmal generell bis ich de fehler finde
		$html='<div style="page-break-after:always !important;height:0;margin:0;padding:0;"></div>';
		return $html;
	}
	
	public function footer() {
		$html="</body></html>";
		return $html;
	}
	
	public function msg($msg,$err) {
		if (empty($msg)) {
			return "";
		}
		
		if ($err) {
			return '<h2 id="red">'.$msg.'</h2>';
		} else {
			return '<h2 id="green">'.$msg.'</h2>';
		}
	}
	
	public function DateTime($date="",$format="d.m.Y") {
		if (empty($date)) {
			return "";
		}
		return (new DateTime($date))->format($format);
	}

	public function getDateTime($fieldname,$fixdate="") {			
		if (!empty($_POST[$fieldname])) {
			if (!empty($_POST[$fieldname.'time']))  {
				$dt=new DateTime($_POST[$fieldname.'time']);
				$time=$dt->format("H:i:s");			
			}
			$dt=new DateTime($_POST[$fieldname]);
			$date=$dt->format("Y-m-d");
		} else {
			if (empty($fixdate)) {
				$date="";
				$time="";
			} else if ($fixdate == "NOW") {
				$dt=new DateTime();
				$date=$dt->format("Y-m-d");
				$time=$dt->format("H:i:s");
			} else {
				$dt=new DateTime($fixdate);
				$date=$dt->format("Y-m-d");
				$time=$dt->format("H:i:s");
			}
			// $date="";
		}
		$html='<label><input type="date" name="'.$fieldname.'"  value="'.$date.'"></label><label><input type="time" name="'.$fieldname.'time"  value="'.$time.'"></label>';
		return $html;
	}

	public function getDate($fieldname,$fixdate="") {
		if (!empty($_POST[$fieldname])) {
			$dt=new DateTime($_POST[$fieldname]);
			$date=$dt->format("Y-m-d");
		} else {
			if (empty($fixdate)) {
				$date="";
			} else if ($fixdate == "NOW") {
				$dt=new DateTime();
				$date=$dt->format("Y-m-d");
			} else {
				$dt=new DateTime($fixdate);
				$date=$dt->format("Y-m-d");
			}
			// $date="";
		}
		$html='<label><input type="date" name="'.$fieldname.'"  value="'.$date.'"></label>';
		return $html;
	}

	public function getTime($fieldname,$fixdate="") {
		if (!empty($_POST[$fieldname])) {
			$dt=new DateTime($_POST[$fieldname]);
			$date=$dt->format("H:i");
		} else {
			if (empty($fixdate)) {
				$date="";
			} else {
				$dt=new DateTime($fixdate);
				$date=$dt->format("H:i");
			}
			$date="";
		}
		$html='<label><input type="time" name="'.$fieldname.'"  value="'.$date.'"></label>';
		return $html;
	}

	public function getRadio($fieldname,$content,$select="") {
		if (empty($_POST[$fieldname])) {
			$_POST[$fieldname]="";
			if (!empty($select)) {
				$_POST[$fieldname]=$select;
			}
		}
		$html="";
		if (is_array($content)) {
			foreach($content as $k => $v) {
				if (!empty($content[0])) {
					$k++;
				}
				if (($_POST[$fieldname]) == $k) {
					$checked="checked";
				} else {
					$checked="";
				}
				$html.='<label><input type="radio" name="'.$fieldname.'" '.$checked.' value="'.$k.'">'.$v.'</label><br>';
			}
		} else {
			// Nicht getestet
			if (!empty($_POST[$fieldname])) {
				$checked="checked";
				$k=1;
			} else {
				$checked="";
				$k=1; // 0
			}
			$html='<label><input type="radio" name="'.$fieldname.'" '.$checked.' value="'.$k.'">'.$info.'</label><br>';
			return $cb;
		}
		// echo htmlspecialchars($html);
		return $html;
	}

	public function getCheckbox($fieldname,$info) {
		if (is_array($info)) {
			
			// foreach($checked_text as $k => $v) {
				// if ($POST[$field][$k]
				// echo htmlspecialchars('<label><input type="checkbox" name="'.$field.'['.$k.']" '.$checked[$k].' value="'.$k.'">'.$v.'</label><br>');	
			// 	echo '<label><input type="checkbox" name="'.$fieldname.'['.$k.']" '.$checked[$k].' value="'.$k.'">'.$v.'</label><br>';
			// }
		} else {
			if (!empty($_POST[$fieldname])) {
				$checked="checked";
				$k=1;
			} else {
				$checked="";
				$k=1; // 0
			}
			$cb='<label><input type="checkbox" name="'.$fieldname.'" '.$checked.' value="'.$k.'">'.$info.'</label><br>';
			return $cb;
		}
	}
		
		
		
		
	public function getSelection($fieldname,$option="",$option2="") {
		if (($this->select == "") and (!empty($_POST[$fieldname])) ) {
			$this->select=$_POST[$fieldname];
		}
		if (is_array($option)) {
			$this->selection=$option;
			$option=$option2;
		}  
		if (strtoupper($option) == "SUBMIT")   {
			$opt='<select name="'.$fieldname.'" onchange="this.form.submit()">';
		} else {
			$opt='<select name="'.$fieldname.'">';
		}
			
			
		$this->selection=array("" => "-- Bitte Auswählen --") + $this->selection;
		foreach($this->selection as $k => $v) {
			$selected="";
			if ($k == $this->select) {
				$selected="SELECTED";
			}
			$opt .= '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
		}
		// $opt .= '</button>';
		$opt .= '</select>';
		return $opt;
	}

	public function getTextarea($fieldname,$width="60em",$height="5em") {
		if (empty($_POST[$fieldname])) {
			$_POST[$fieldname]="";
		}
		if (substr_count($width,"style") > 0) {
			$style=$width;
		} else {
			$style='style="width:'.$width.';height:'.$height.'"';
		}
		$html='<textarea name="'.$fieldname.'" '.$style.'>'.$_POST[$fieldname].'</textarea>';
		// echo htmlspecialchars($html);
		return $html;
		
	}
	public function getText($fieldname,$width="40em") {
		if (empty($_POST[$fieldname])) {
			$_POST[$fieldname]="";
		}
		if (substr_count($width,"style") > 0) {
			$style=$width;
		} else {
			$style='style="width:'.$width.';"';
		}
		
		$html='<input name="'.$fieldname.'" type="text" value="'.$_POST[$fieldname].'" '.$style.'>';
		return $html;
	}


	// Sinnvoll ?
	public	function getHidden($fieldname="",$fromcontent="") {			
		if (empty($fieldname)) {
			$fieldname="recnum";
		}
		if (!empty ($fromcontent)) {
			$_POST[$fieldname]=$fromcontent;
		}
		if (empty($_POST[$fieldname])) {
			$_POST[$fieldname]="";
		}
		$html='<input name="'.$fieldname.'" type="hidden" value="'.$_POST[$fieldname].'">';
		return $html;	
	}
	
	public function getNumber($fieldname,$width="10em") {
		if (empty($_POST[$fieldname])) {
			$_POST[$fieldname]="";
		}
		if (substr_count($width,"style") > 0) {
			$style=$width;
		} else {
			$style='style="width:'.$width.';"';
		}
		$html='<input name="'.$fieldname.'" type="number" step="1" value="'.$_POST[$fieldname].'" pattern="^\d*(\.\d{0,2})?$" '.$style.'>';
		return $html;
	}


	public function getEuro($fieldname,$width="10em",$placeholder="0.00",$pattern="^\d*(\.\d{0,2})?$") {
		if (empty($_POST[$fieldname])) {
			$_POST[$fieldname]="";
		}
		if (substr_count($width,"style") > 0) {
			$style=$width;
		} else {
			$style='style="width:'.$width.';"';
		}
		$html='<input name="'.$fieldname.'" type="text" placeholder="'.$placeholder.'" pattern="'.$pattern.'" value="'.$_POST[$fieldname].'" '.$style.'>';
		return $html;
	}
	
	// hier: Eingabe:
	// 1. Feldname wo der recnum der Tabelle gespeichert ist
	// 2. Anzeige der Schaltfläche-> bei Array: insert/update text z.B.: array("XXX hinzufügen","XXX ändern")
	// - Die Namanen sind abhängig davon, ob der Datensatz neu ist (insert) oder geladen ist (update)
	// 
	public function getAutoButton($recnum_fieldname="",$display="") {
		if (empty($recnum_fieldname)) {
			$recnum_fieldname="recnum";
		}
		if (empty($display)) {
			if (empty($_POST[$recnum_fieldname])) {
				$display="hinzufügen";
			} else {
				$display="ändern";
			}
		}  else 
		// Benutzerdeefinierte Schaltfläche
		if (is_array($display)) {
			if (empty($_POST[$recnum_fieldname])) {
				$display=$display[0];
			} else {
				$display=$display[1];
			}
		}
		

		if (empty($_POST[$recnum_fieldname])) {
			$name="insert";
		} else {
			$name="update";
		}
		
		
		
		$html='<input name="'.$name.'" type="submit"  value="'.$display.'">';
		// echo htmlspecialchars($html);
		
	
		return $html;
		
	}

	public function getSubmit($fieldname,$value="",$filename="") {
		if (empty($fieldname)) {
			$fieldname="submit";
		}
		if (empty($value)) {
			$value=$fieldname;
		}
		if (empty($filename)) {
			$html='<input name="'.$fieldname.'" type="submit"  value="'.$value.'">';
		} else {
			$html='<input name="'.$fieldname.'" type="submit"  value="'.$value.'" formmethod="POST" formaction="'.$filename.'">';
		}
		return $html;
	}


	public function getActionButton($fieldname,$value="",$filename="") {
		if (empty($fieldname)) {
			$fieldname="submit";
		}
		if (empty($value)) {
			$value=$fieldname;
		}
		if (empty($filename)) {
			$fieldname=$_SERVER['SCRIPT_NAME'];
		}
		$html='<input name="'.$fieldname.'" type="submit"  value="'.$value.'" formmethod="POST" formaction="'.$filename.'">';
		return $html;
	}


	/*
		$blueprint.= '<tr><th>$label</th><td>';
		$blueprint.= '$command';
		$blueprint.= '</td></tr>';
		$out->printField($artikel->format,$blueprint,"vorname");
	*/
	public function printField($fieldname,$text="",$button="") {
		
		$v=$this->format[$fieldname];
		
		if (!empty($v['style'])) {
			$style='style="'.$v['style'].'"';
		} else {
			$style="";
		}

		switch ($v['typ']) {
			case "hidden":
				$command=$this->getHidden($fieldname);
				return $command;
				break;
				
			case "string":    	
				$command=$this->getText($fieldname,$style);
				break;
			
			case "textarea":    	
				$command=$this->getTextarea($fieldname,$style);
				break;
			
			case "int":    	
				$command=$this->getNumber($fieldname,$style);
				break;
			
			case "euro":     
				$command=$this->getEuro($fieldname,$style);
				break;

			case "datetime":
				if (empty($v['wahl'])) {
					$command=$this->getDateTime($fieldname);
				} else {
					$command=$this->getDateTime($fieldname,$v['wahl']);
				}
				break;

			case "datum":
			case "date":
				if (empty($v['wahl'])) {
					$command=$this->getDate($fieldname);
				} else {
					$command=$this->getDate($fieldname,$v['wahl']);
				}
				break;
				
			case "radio":    
				//echo "RADIO";
				if (empty($v['select'])) {
					$command=$this->getRadio($fieldname,$v['wahl']);
				} else {
					$command=$this->getRadio($fieldname,$v['wahl'],$v['select']);
				}
				break;

			case "selection":    
				if (empty($v['select'])) {
					$command=$this->getSelection($fieldname,$v['wahl']);
				} else {
					// $command=$this->getSelection($fieldname,$v['wahl'],$v['select']);
					$command=$this->getSelection($fieldname,$v['wahl']);
				}
				break;
			
			case "checkbox": // noch nicht fertig
				$command=$this->getCheckbox($fieldname,$wahl);
				break;
			
		}
		// echo "COMMAND:".htmlspecialchars($command)."<br>";
		// echo "Blueprint:".htmlspecialchars($blueprint)."<br>";
		// echo "<hr>".htmlspecialchars($this->layout)."<br>";		
		$html=str_replace('$label',$v["label"],$this->layout);
		// echo "Step1:".htmlspecialchars($html)."<br>";		

		
		if (!empty($button)) {
			//echo "Button<br>";
			$command=$button.$this->getHidden($fieldname);
			if (!empty($text)) {
				$command=$text." ".$command;
			}
		}
		$html=str_replace('$command',$command,$html);
		// echo htmlspecialchars($html);
		// echo "<br>";
		// echo "Step2:".htmlspecialchars($html)."<br>";		
		
		// echo "HTML:".htmlspecialchars($html)."<br>";
		return $html;	
	}

	public function printFields($fieldname="") {
		$html="";
		if (empty($fieldname)) {
			foreach($this->format as $fieldname => $v) {
				$html.=$this->printField($fieldname);
			}
		} else {
				$html.=$this->printField($fieldname);
		}
		return $html;
			
	}
	public function setLayout(&$layout) {
		$this->layout=$layout;
	}
	public function setFormat(&$format) {
		$this->format=$format;
	}

	// Nur ein Alias
	public function printAutoButton($recnum_fieldname="",$display="") {
		return $this->getAutoButton($recnum_fieldname,$display);
	}
	public function savePOST($pre="") {
		if (!empty($pre)) $pre="_".$pre;
		$html="";
		foreach($_POST as $k => $v) {
			$html.='<input type="hidden" value="'.$v.'" name="'.$pre."_".$k.'">';
		}
		return $html;
	}

}
?>
