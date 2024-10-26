<?php
	session_start();
?>
<!doctype html>
<html lang="de">

<head>
<meta charset="utf-8">
<link rel="stylesheet" href="menu.css">
<link rel="stylesheet" href="home.css">
</head><body>
<div id="wrapper">
	<header>
	<h1>DD-Office</h1>
	<h2>Produkte</h2>
	</header>

	<div id="mehrspaltig">
		<nav style="flex:1;">

		</nav>

		<article><div id="menutop">
			<a href="../index.php">Home</a>
			<a href="home-produkte.php">Produkte</a>
			<a href="home-service.php">Service</a>
			<a href="home-preise.php">Preise</a>
<?php
	if (empty($_SESSION['firmanr'])) {
		echo '<a href="login.php">Login</a>';
	} else {
		echo '<a href="start.php">Office starten</a>';
	}
?>			
		</div>
		<center>



		<div id="borderstyle">
			<div id="logo">
				<img src="img/dd-office-short-trans.png" ><br>
				<b></b><br>
			</div>
		<span style="font-size: 2em;">Preise allgemein</span><br>
		<ul>
			<li><b>Programmierung-Tagessatz (8 Stunden):</b> 400,00 €</li>
			<li><b>Anfahrtskosten:</b> 0,36 € / km, aber mindestens 50,00 €</li>
			<li><b>Installation vor Ort / Tag:</b> 250,00 € + Unterkunft</li>
			<li><b>Installations-Überstunden (ab 9 Stunden):</b> 50,00 € / Stunde</li>
			<li><b>Fernwartung (max 10 Stunden):</b> 30,00 € / Stunde  </li>
		</ul>
		</div><br><br>
		
		<div id="borderstyle">
			<div id="logo">
				<img src="img/dd-office-short-trans.png" ><br>
				<b></b><br>
			</div>
		<span style="font-size: 2em;">Fertige Module</span><br>

		<ul>
			<li><b>Rechnungsprogramm:</b> 15,00 € / Monat</li>
			<li><b>Zeiterfassung:</b> 5 € / Monat</li>
			<li><b>Projekte:</b> 15,00 € / Monat</li>
			<li><b>Dokumente:</b> 30,00 € / Monat</li>
			<li><b>Wartungsvertrag:</b> 10,00 € / Monat</li>
		</ul>

		</div><br><br>
		
		Alle Angaben sind Nettopreise.
		</center>
		
		
		</article>

		<aside>
		<!-- Zusatz -->
		</aside>

	</div>

	<footer><!-- Fußzeile-->&nbsp;</footer>
</div>
</body></html>


<?php
	echo '
	';
	echo '
	';

?>