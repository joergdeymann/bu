<?php
    header('Content-Type: application/json');
    $_POST['key1']="Hallo";
    $_POST['key2']="Jörg";
    
    $response = [
        'key1' => $_POST['key1'],
        'key2' => $_POST['key2']
    ];
    echo json_encode($response);
?>
