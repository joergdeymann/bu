<?php
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    header('Content-Type: application/json');

    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["error" => "Ungültiges JSON-Format"]);
        exit;
    }

    $dbname = "bu"; 
    $user="php";
    $pw="#php#8.0-..";
    $host="localhost";

    if ($_SERVER['SERVER_NAME'] == 'dd-office.de') {
        $user="k149450_ddbuero";
        $dbname = "k149450_ddbuero"; 
        $pw="diE1.dEy9@jd#73";
        $host="10.35.233.28:3306";
    }

    $db = new mysqli($host, $user, $pw, $dbname);
    if ($db->connect_errno) {
        echo json_encode([error => "Verbindung fehlgeschlagen: " . $db->connect_error]);
        exit;
    }

    $db->set_charset("utf8mb4");
    $rows=[];
    $request=$data["query"];
    $result = $db->query($request);
    if (strtolower(substr($request,0,6)) == "select") {
        while($row=$result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    echo json_encode($rows);

?>

