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

    $dbname = "bu"; 
    $user = "php";
    $pw = "#php#8.0-..";
    $host = "localhost";
    
    if ($_SERVER['SERVER_NAME'] == 'dd-office.de') {
        $user = "k149450_ddbuero";
        $dbname = "k149450_ddbuero"; 
        $pw = "diE1.dEy9@jd#73";
        $host = "10.35.233.28:3306";
    }
    
    try {
        // Verbindung zur Datenbank herstellen
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Fehler als Exceptions werfen
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Ergebnisse als assoziative Arrays zurückgeben
            PDO::ATTR_EMULATE_PREPARES => false, // Native Prepared Statements nutzen
            PDO::MYSQL_ATTR_MULTI_STATEMENTS => true // Erlaubt mehrere Abfragen
        ];
        $pdo = new PDO($dsn, $user, $pw, $options);
    
        // Abfrage ausführen
        $rows = [];
        $request = $data["query"];
        $return= !isset($data["noreturn"]);
    
        try {
            // SQL-Abfrage ausführen
            if (substr($request,0,4) == "DROP") {
                $stmt = $pdo->exec($request);
                $return=false;
            } else {
                $stmt = $pdo->query($request);
            }
            
            // Ergebnisse abrufen, wenn "noreturn" nicht gesetzt ist
            if ($return) {
                $rows = $stmt->fetchAll();
            }
        } catch (PDOException $e) {
            // Fehler in die Fehlerlogdatei schreiben
            error_log("Datenbankabfrage-Fehler: " . $e->getMessage());
            error_log("Datei: " . $e->getFile());
            error_log("Zeile: " . $e->getLine());
            echo json_encode([
                "error" => "Datenbankfehler bei der Abfrage",
                "message" => $e->getMessage(),
                "request" => $request,
                "line" =>  $e->getLine(),
                "file" =>  $e->getFile()
            ]);
            exit;
        }
    } catch (PDOException $e) {
        // Fehler bei der Verbindung in die Fehlerlogdatei schreiben
        error_log("Verbindungsfehler: " . $e->getMessage());
        error_log("Datei:" . $e->getFile());
        error_log("Zeile: " . $e->getLine());
        echo json_encode(["error" => "Verbindung zur Datenbank fehlgeschlagen"]);
        exit;
    }
    
    // Ergebnisse zurückgeben
    echo json_encode($rows);
?>

