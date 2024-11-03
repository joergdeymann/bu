<?php
    header('Content-Type: application/json');

    $input = file_get_contents("php://input");
    $data = json_decode($input, true); // Konvertiert JSON in ein PHP-Array    
    
    $_POST['key1']="Hallo";
    $_POST['key2']="Jörg";
    // substr($_POST['query'],4)
    $response = [
        'key1' => $_POST['key1'],
        'key2' => $_POST['key2'],
        'key3' => $data["query"]
    ];
    echo json_encode($response);
?>
