<?php

declare(strict_types=1);

header('Content Type: application/json; charset=utf-8');
require_once __DIR__.'/../config/db.php';

$action=$_GET['action']??'';

try{
    switch($action){
        case 'makes';
        $stmt=$pdo->query("SELECT DISTINCT make FROM vehicles ORDER BY make ASC");
        exho json_encode(['status'=>'success', 'data'=>$stmt->fetchAll(PDO::FETCH_COLUMN)]);
        break;

        
    }
}