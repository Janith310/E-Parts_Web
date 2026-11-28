<?php

declare(strict_types=1);

header('Content Type: application/json; charset=utf-8');
require_once __DIR__.'/../config/db.php';

$action=$_GET['action']??'';

try{
    switch($action){
        case 'makes':
            $stmt=$pdo->query("SELECT DISTINCT make FROM vehicles ORDER BY make ASC");
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_COLUMN)]);
            break;

        case 'models':
            $make=trim($_GET['make']??'');
            $stmt = $pdo->prepare("SELECT DISTINCT model FROM vehicles WHERE make = :make ORDER BY model ASC");
            $stmt->execute([':make' => $make]);
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_COLUMN)]);
            break;

        case 'chassis':
            $make  = trim($_GET['make'] ?? '');
            $model = trim($_GET['model'] ?? '');
            $stmt  = $pdo->prepare("
                SELECT id, make, model, chassis_code, engine_code, year_from, year_to 
                FROM vehicles 
                WHERE make = :make AND model = :model 
                ORDER BY year_from DESC, chassis_code ASC
            ");
            $stmt->execute([':make' => $make, ':model' => $model]);
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
            break;

        case 'autocomplete':
            $rawQuery = strtoupper(trim($_GET['q'] ?? ''));
            if ($rawQuery === '') {
                echo json_encode(['status' => 'success', 'data' => []]);
                break;
            }
            $chassisPrefix = explode('-', $rawQuery)[0];

            $stmt = $pdo->prepare("
                SELECT id, make, model, chassis_code, engine_code, year_from, year_to
                FROM vehicles
                WHERE chassis_code LIKE :prefix 
                   OR CONCAT(make, ' ', model, ' ', chassis_code) LIKE :full
                ORDER BY 
                    CASE WHEN chassis_code = :exact THEN 0 ELSE 1 END,
                    chassis_code ASC
                LIMIT 10
            ");
            $stmt->execute([
                ':prefix' => $chassisPrefix . '%',
                ':full'   => '%' . $rawQuery . '%',
                ':exact'  => $chassisPrefix
            ]);
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
            break;

        case 'parts':
            $vehicleId = (int) ($_GET['vehicle_id'] ?? 0);
            $category  = trim($_GET['category'] ?? 'All');

            if ($vehicleId <= 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Valid vehicle_id is required']);
                break;
            }

            $vStmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = :id");
            $vStmt->execute([':id' => $vehicleId]);
            $vehicle = $vStmt->fetch();

            if (!$vehicle) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Vehicle not found']);
                break;
            }

            $sql = "
                SELECT 
                    p.id,
                    p.part_number,
                    p.name,
                    p.category,
                    p.brand,
                    p.price,
                    p.stock_qty,
                    pf.fitment_notes
                FROM part_fitment pf
                INNER JOIN parts p ON pf.part_id = p.id
                WHERE pf.vehicle_id = :vehicle_id
            ";
            $params = [':vehicle_id' => $vehicleId];

            if ($category !== '' && $category !== 'All') {
                $sql .= " AND p.category = :category";
                $params[':category'] = $category;
            }

            $sql .= " ORDER BY p.category ASC, p.name ASC";

            $pStmt = $pdo->prepare($sql);
            $pStmt->execute($params);

            echo json_encode([
                'status'  => 'success',
                'vehicle' => $vehicle,
                'parts'   => $pStmt->fetchAll()
            ]);
            break;

        default:
        http_response_code(400);
        echo json_encode(['status'=>'error', 'message'=>'Invalid API action']);
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}