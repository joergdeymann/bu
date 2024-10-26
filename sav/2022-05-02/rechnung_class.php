class Rechnung {
	global $db;

	private $renr="";        // Rechnungsnummer
	private $layout=0;       // Layout Nummer
	private $mahnstufe=0;    // Mahnstiufe Nummer
	
	private $row_re=new Array();
	private $row_layout_standart=new Array();
	private $row_layout=new Array();
	
	public setReNr($renr) {
		$this->renr=$renr;
	}

	public setLayout($layout=0) {
		$this->layout=$layout;
	}
	
	public setMahnstufe($mahnstufe=0) {
		$this->mahnstufe=$layout;
	}

	public reload() {
	}
	
	private loadRechnung() {
		$request="SELECT * from bu_re where renr='".$this->renr."'";
		$result = $ths->db->query($request);
		$this->row_re = $result->fetch_assoc();
		if ($this->row_re) {
			$this->layout=row_re['layout'];
		}
	}
	private loadStandartLayout() {
		$request="SELECT * from bu_re_layout where prio=1";
		$result = $ths->db->query($request);
		$this->row_layout_standart = $result->fetch_assoc();
		if ($this->row_layout_standart) {
			$this->layout=row__layout_standart['nr'];
		}
	}
	
	private loadLayout() {
		$request="SELECT * from bu_re_layout where nr='".$this->layout."' and mahnstufe='".$this->mahnstufe."'";
		$result = $ths->db->query($request);
		$this->row_layout = $result->fetch_assoc();		
	}

	private findeRechnungsLayout() {
		if (!empty($this->renr)) {
			return;
		}
		
		$this->loadRechnung();
		if ($this->layout == 0) {
			$this->loadStandardLayout();
		}
		
		$this->loadLayout();
		
		
		if (if (isempty($renr
	}
	
	
	
	
	private function get() {
	}
	
	
}
