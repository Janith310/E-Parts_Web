<?php

declare(strict_types=1);

$host = '127.0.0.1';
$db = 'spare_parts_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host; dbname=$db; charset=$charset";

$options = [
    PDO::ATTR_ERRMODE =>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE =>PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES=>false,
];

try{
    $pdo=new PDO($dsn, $user, $pass, $option);
}catch (PDOException $e){
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'status'=>'error',
        'message'=>'Database connection failed: '. $e->getMessage()
    ]);
    exit;
}
