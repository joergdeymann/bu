<?php
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    header('Content-Type: application/json');
    error_log("Schreib was rein!!!!!!!!");
    // ini_set('error_log', 'php://stderr');

    //$data["query"] = Query of select or update whtaever
    //$data["noreturn"] = if not a SELECT command
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["error" => "Ungültiges JSON-Format"]);
        exit;
    }

    $dsn = "mysql:host=your_host;dbname=your_database;charset=utf8mb4";
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
    try {
        $db->set_charset("utf8mb4");
        $rows=[];
        $request=$data["query"];
        $result = $db->query($request);
        if (!$result) {
            throw new Exception("Datenbankfehler: " . $db->error);
        }
        
        if (!isset($data["noreturn"])) {
            while($row=$result->fetch_assoc()) {
                $rows[] = $row;
            }
        }     
    } catch (Excption $e) {
        error_log("Fehler: " . $e->getMessage());
        error_log("Datei: " . $e->getFile());
        error_log("Zeile: " . $e->getLine());
    }

    // if (strtolower(substr($request,0,6)) == "select") {
    //     while($row=$result->fetch_assoc()) {
    //         $rows[] = $row;
    //     }
    // }
    echo json_encode($rows);








?>

