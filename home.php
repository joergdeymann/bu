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
	<h2>Home</h2>
	</header>

	<div id="mehrspaltig">
		<nav style="flex:1;">

		</nav>

		<article><div id="menutop">
			<a href="home.php">Home</a>
			<a href="home-produkte.php">Produkte</a>
			<a href="home-service.php">Service</a>
			<a href="home-preise.php">Preise</a>
<?php
	if (empty($_SESSION['firmanr'])) {
		echo '<a href="login.php">Login</a>';
	} else {
		echo '<a href="index.php">Office starten</a>';
	}
?>			
		</div>
		<center>


		<div style="text-align:left;width:70%;color:black;background-color:white;padding-left:15%;padding-right:15%;margin-top:-8px;"><!-- background-color:#f5f5dc -->
			<div id="logo">
				<img src="img/dd-office-short-trans.png" ><br>
			</div>
		
<br>Wenn Sie Fragen haben können Sie uns <a href="home-service.php">hier</a> kontaktieren.<br> <br>


Wir alle kennen es: die sogenannten Komplett-Pakete gibt es überall. <br> 
Man bekommt dann ein Programm mit vielen Features, die man nicht braucht und einen teilweise auch überfordern. <br> 
Doch Features die man wirklich braucht, sucht man oft vergebens. <br> 
<br> 
Wenn man Support benötigt, gibt es oft keine wirklich persönliche Beratung und Hilfe.<br> 
<br> 
Genau hier kommen wir mit <img src="img/dd-office-short-trans.png" height="24px;"> ins Spiel. <br>  
Wir haben maßgeschneiderte Softwarelösungen für ihr Unternehmen. Ohne Schnick-Schnack , dafür aber immer das passende Tool, das Sie benötigen. <br> 
<br> 
Ihre <img src="img/dd-office-short-trans.png" height="24px;"> Vorteile im Überblick:<br>
<ol>

<li>Kein Baukasten System:<br>
<ul>
<li>Alles ist von Hand und nur für Sie programmiert.</li> 
<li>Kein Programm gleicht dem anderen. Denn es ist so einzigartig wie ihr Unternehmen. </li>
<li>Eben Maßgeschneidert !</li><br>
</ul>
</li>


<li>Persönlicher Support.:<br> 
<ul><li>
Keine Callcenter, sondern ein direktes, persönliches Gespräch mit unserem Programmierer. </li><br> 
</ul>
</li>

<li>Einrichtung und Einführung vor Ort in ihrem Betrieb:<br>
<ul>
<li>Wir schulen Sie und ihre Mitarbeiter vor Ort. So können alle das volle Potenzial der Software nutzen.</li> 
<li>Alternativ können Sie auch auf unserer Website alle Bedienungsanleitungen und Video Tutorials einsehen.</li><br>
</ul>
</li>

<li>Sie bezahlen nur für Software die Sie wirklich brauchen.<br>
<ul>
<li>Nicht mehr und nicht weniger!</li>
<li>Dadurch, daß wir für Sie persönlich programmieren, kaufen Sie kein „Gesamtpaket“, in dem Sie auch Features bezahlen müssen, die Sie nicht benötigen.</li><br>
</ul>
</li>

<li>Lebenslange Wartung:<br>
<ul>
<li>Auf Wunsch bekommen Sie lebenslange Wartung und Support.<br> 
Das komplette Rund um Sorglospaket. <br><br>
</li>
</ul>
</li>
</ol>



Wir laden Sie nun herzlich dazu ein, sich auf unserer Website umzuschauen. Es ist ganz bestimmt das passende Produkt für Sie dabei.<br>
<br>
Und wenn sie Fragen haben, freuen wir uns auf ihre Anfragen .: <a href="mailto:mail@dd-office.de">mail@dd-office.de</a><br><br><br>


		<center><div style="display:inline-block;margin:0;padding:0;">
		<div id="logo">
			<img src="img/dd-office-short-trans.png" ><br><br>
			<b style="font-size:2em;color:black;">Module</b>
		</div>
			
		

			<br><?php 

		if (!isset($_SESSION['kdnr'])) {
			echo '<a href="mailto:mail@dd-office.de?subject=Support">Mail</a>';
		} else {
			echo '<a href="mailto:support@dd-office.de?subject=Kunden-Support">support@dd-office.de</a> ';
		}
			?></a><br>
			<a style="margin:0;padding:0;" href="#M1">Rechnung</a><br>
			<a style="margin:0;padding:0;" href="#M2">Zeiterfassung</a><br>
			<a style="margin:0;padding:0;" href="#M3">Projekte</a><br>
			<a style="margin:0;padding:0;" href="#M4">Dokumente</a><br><br>
		</div>		</center>

		
		</div><br><br>


		<a id="M1"></a>
		<div id="borderstyle">
			<div id="logo">
				<img src="img/dd-office-short-trans.png" ><br>
				<b>Rechnung</b><br>
			</div>
		<ul>
			<li>individueller Rechnungslayout nach Ihren Vorgaben</li>
			<li>schnelle Rechnungserstellung mittels vorher eingestellten Artikeln</li>
			<li>Hinweis, wenn Mahnung erstellt werden muss, wenn das Zahlungsziel erreicht ist.<br>
				Mahnung mit 1 Klick erreichbar Mailvorlage kann danach noch geändert werden
			</li>
			<li>Voreingestellte interaktive Mahntexte, die geändert werden können für Rechnung und Mail</li>
			<li>Die Rechnungsliste kann mit Filtern eingeschränkt werden</li>
			<li>Kopieren von alten Rechnungen eins Kunden für die neue Rechnung möglich</li>
			<li>Versand mit einem Klick möglich, als Mail, oder als PDF speicherbar</li>
		</ul>
		<a href="">Jetzt 1 Monat testen</a> danach monatlicher Preis von 15,00 € netto 
		</div><br><br>
		
		<a id="M2"></a>
		<div id="borderstyle">
			<div id="logo">
				<img src="img/dd-office-short-trans.png" ><br>
				<b>Zeiterfassung</b><br>
			</div>
		<ul>
			<li>Nachbearbeitung und Übersicht, Antragsbearbeitung vom PC</li>
			<li>Mitarbeiter können vom Handy oder PC ihre Zeiten und Pausen stempeln.</li>
		</ul>
		<a href="">Jetzt 1 Monat testen</a> danach monatlicher Preis ab 5,00 € netto 
		</div><br><br>
		
		<a id="M3"></a>
		<div id="borderstyle">
			<div id="logo">
				<img src="img/dd-office-short-trans.png" ><br>
				<b>Projekte</b><br>
			</div>
		<ul>
			<li>Projektverwaltung als Projektersteller oder Teilnehmer eines Projektes</li>
			<li>Nutzung über PC / Handy / Tablet möglich</li>
			<li>Teilnehmer können Informationen hinzufügen</li>
			<li>Planungen der Projekte könnnen im Kalender eingesehen werden</li>
			<li>Projektteilnehmer wie Mitarbeiter, Freelancer oder Firmen können individuelle Informationen und Aufgaben einsehen</li>
			<li>Eingeteilte Projektgruppen für z.B. Technischer Leiter, Projektleiter</li>
			<li>Direkte Rechnungserstellung über die Projektverwaltung geplant, somit weniger Arbeit und Zeitersparnis</li>
		</ul>
		<a href="">Jetzt 1 Monat testen</a> danach monatlicher Preis ab 15,00 € netto 
		</div><br><br>
		
		<a id="M4"></a>
		<div id="borderstyle">
			<div id="logo">
				<img src="img/dd-office-short-trans.png" ><br>
				<b>Dokumente</b><br>
			</div>
		<ul>
			<li>Erstellung von Dokumenten für Individuell für jeden Kunden oder Mitarbeiter</li>
			<li>Filialpool auswählbar</li>
			<li>Kundenpool auswöhlbar</li>
			<li>Zugriffsrechte für jeden einzelnen Mitarbeiter möglich</li>
			<li>Unterteilung der Zugriffsrechte in Ansehen, Lesen und Schreiben, Gruppen</li>
			<li>Umstellung der Dokumente erledigen wir für Sie und ist inclusive
		</ul>
		<a href="">Jetzt 1 Monat testen</a> danach monatlicher Preis ab 30,00 € netto 
		</div><br><br>

		<a id="M5"></a>
		<div id="borderstyle">
			<div id="logo">
				<img src="img/dd-office-short-trans.png" ><br>
				<b>Wartung</b><br>
			</div>
		<ul>
			<li>Wartungsvertrag für jedes Modul möglich</li>
			<li>bevorzugte Behandlung in allen Angelegenheiten</li>
			<li>Änderungs- und Anpassungswünsche auch nach dem 1. Jahr</li>			
		</ul>
		<a href="">Jetzt 1 Monat testen</a> danach monatlicher Preis ab 10,00 € netto 
		</div>
		
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