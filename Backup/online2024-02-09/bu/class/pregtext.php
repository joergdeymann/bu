<?php
class x2 {
	public $output_re="";
	public $o; // Pointer auf $output_re;
	function __construct () {
		$this->o = &$output_re;
	}
	
	public function pointer(&$re) {
		$this->output_re = &$re; 
		$re="Hallo";
	}
	public function ret() {
		return $this->o;
		
		$o = &$this->output_re;
		return $o;
	}
	
}

$y2 = new x2();
$var=&$y2->output_re;
$var="ABC";
echo "y->output_re=".$y2->output_re."<br>";
$y2->output_re="Morgen";
echo "var=".$var."<br>";
var_dump($var);
var_dump($y2->output_re);

exit;


$var2 = $y2->ret();
$var2="QQQ";
echo "<br>y->output_re=".$y2->output_re;

exit;

class x {
	public $output_re="";
	public function pointer(&$re) {
		$this->output_re = &$re; 
		$re="Hallo";
	}
	
}

$y = new x();
$var="ABC";
$y->pointer($var);
echo "VAR=".$var."<br>";
echo "y->output_re=".$y->output_re;
$var="Besen";
echo "y->output_re=".$y->output_re;

exit;





$content = "Hier ist der \$abs['name'] mit der Tel \$abs['tel']";
$pre ="abs";

$replace=array(
'name' => "Joerg Deymann",
'tel'  => "05932 1419",
'xyz'  => "ist"
);

 replaceContent($content,$pre,$replace);
 
 echo $content."<br>";
 
	function replaceContent(&$content,&$pre,&$replace) {
		foreach($replace as $k => $v) {
			$s = "/\\\$".$pre."\['".$k."'\]"."/is";
			$content=preg_replace($s,$v,$content);
		}
	}
	
?>
