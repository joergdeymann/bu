<?php
    header('Content-Type: application/json');

    $input = file_get_contents("php://input");
    $data = json_decode($input, true); // Konvertiert JSON in ein PHP-Array    
    // $data["query"]="SELECT * FROM `bu_project_jobs`  ORDER BY father; ";

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
        die("Verbindung fehlgeschlagen: " . $db->connect_error);
    }
    $db->set_charset("utf8mb4");

    $request=$data["query"];
    $result = $db->query($request);
    if (strtolower(substr($request,0,6)) == "select") {
        while($row=$result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    // $row = $result->fetch_assoc();
    // echo "Hallo";
    // var_dump($rows);
    // echo "Hallo2";





    // $_POST['key1']="Hallo";
    // $_POST['key2']="Jörg";
    // substr($_POST['query'],4)
    // $response = [
    //     'key1' => $_POST['key1'],
    //     'key2' => $_POST['key2'],
    //     'key3' => $data["query"]
    // ];
    echo json_encode($rows);
?>
