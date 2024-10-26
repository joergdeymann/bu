<?php
	session_start();
	if (empty($_POST['mail'])) $_POST['mail']="";
	if (empty($_POST['subject'])) $_POST['subject']="";
	if (empty($_POST['body'])) $_POST['body']="";
	$msg="";
	if (!empty($_POST['submit']))  {
		include "class/class_phpmailer.php";
		$m=new PHPMailer();
		$m->setFrom($_POST['mail']);
		$m->setTo("support@dd-office.de");
		$m->setSubject($_POST['subject']);
		$m->setMessage($_POST['body']);
		$m->send();

		$_POST['mail']="";
		$_POST['subject']="";
		$_POST['body']="";
		$msg="Die Nachricht wurde versedet!";
		
	}
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


		<!-- div style="text-align:left;width:70%;color:black;background-color:#f5f5dc;padding-left:15%;padding-right:15%;margin-top:-15px;"-->
			<div id="logo">
				<img src="img/dd-office-short-trans.png" ><br>
			</div>
		
			<br><br>Wenn Sie fragen haben können Sie uns hier Kontaktieren.<br> <br>
<?php
	if (!empty($msg)) {
		echo '<div style="width:50%; padding: 20px; border:1px solid lime;">'.$msg."</div><br><br>";
	}
?>

		<div id="borderstyle">
			<div id="logo">
				<img src="img/dd-office-short-trans.png" ><br><br>
				<b style="font-size:2em;color:black;">über die E-Mail</b>
			</div>
			<?php
			if (!isset($_SESSION['firmanr'])) {
				echo "Loggen Sie sich ein um bevorzugt behandelt zu werden.<br>Die Kontaktadresse lautet:<br> ";
				echo '<a href="mailto:mail@dd-office.de?subject=Support">Mail</a>';
			} else {
				echo "Die Kontaktadresse lautet:<br> ";
				echo '<a href="mailto:support@dd-office.de?subject=Kunden%20Support">support@dd-office.de</a> ';
			}
			?>		
		</div><br><br>

		<div id="borderstyle">
			<div id="logo">
				<img src="img/dd-office-short-trans.png" ><br><br>
				<b style="font-size:2em;color:black;">über die Telefonnummer</b>
			</div>
			<?php
			if (!isset($_SESSION['firmanr'])) {
				echo "Loggen Sie sich ein, um die Telefonumer zu sehen";
			} else {
				echo "Die Telefonnumer lautet:<br> ";
				echo '<a href="tel:+4915161046840">+49 1516 10 46 840</a> ';
			}
			?>
		</div><br><br>

		<div id="borderstyle">
			<div id="logo">
				<img src="img/dd-office-short-trans.png" ><br><br>
				<b style="font-size:2em;color:black;">über ein Formular</b>
			</div>
			<form method="POST" action="home-service.php">
				<input    required="required" name="mail"    type="text" placeholder="Ihre Mailadresse" pattern="^[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" title="Email muss ein korrekted Format haben" style="width:600px"><br><br>
				<input    required="required" name="subject" type="text" placeholder="Betreff"          pattern=".{3,}" style="width:600px"                title="Betreff muss mehr als 3 Zeichen enthalten"><br><br>
				<textarea required="required" name="body"                placeholder="Ihr Anliegen"     pattern=".{10,}" style="width:600px;height:10em;"  title="Mailtext muss mindestens 10 Zeichen enthalten"><?php echo $_POST['body'] ?></textarea><br>
				<input    name="submit"  type="submit" value="Senden">
			</form>
			
		</div><br><br>



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