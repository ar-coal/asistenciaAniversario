<?php

require_once("dataBase.php");

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: POST");
$data = json_decode(file_get_contents('php://input'), true);
$table = 'asistencia';

switch ($data['comando']) {
    case ('registrar'):
        try {

            $sql = "INSERT INTO ".$table."(numero_control,curso,horas) VALUES (?,?,?)";
            $stmt = $connect->prepare($sql);
            $stmt->execute([
                $data['numero_control'],
                $data['curso'],
                $data['horas']
            ]);
            echo (100);
        } catch (Exception $e) {
            array_push($error_log, $e);
            print_r($error_log);

        }
        break;
    case ('consultar_horas'):
        try {

            $statement = $connect->query("SELECT numero_control,SUM(horas) AS 'total' FROM ".$table." GROUP BY numero_control HAVING SUM(horas) ".$data['operador']." ".$data['limite'].";");
            $statement->execute();
            $res = $statement->fetchAll(PDO::FETCH_ASSOC);
            $json = json_encode($res);
            // file_put_contents($filename, $json);
            // echo (100);
            print_r($json);
        } catch (Exception $e) {
            array_push($error_log, $e);
            print_r($error_log);

        }
        break;
        case ('consultar_estudiante'):
            try {
    
                $statement = $connect->query("SELECT numero_control,curso,horas FROM ".$table." WHERE numero_control = \"".$data['numero_control']."\" ;");
                $statement->execute();
                $res = $statement->fetchAll(PDO::FETCH_ASSOC);
                $json = json_encode($res);
                // file_put_contents($filename, $json);
                // echo (100);
                print_r($json);
            } catch (Exception $e) {
                array_push($error_log, $e);
                print_r($error_log);
    
            }
            break;
    default:
        echo ('Este error no deberia de ser visible favor de comunicarse con desarrollo academico');
        break;
}