<?php
set_error_handler('exceptions_error_handler');
$filename = "data.json";
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "semana_aniversario_asistencias";
$connect;
$connectionString = "mysql:host=" . $servername . ";dbname=" . $dbname . ";charset=utf8";
try {
    $connect = new PDO($connectionString, $username, $password);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . " en la linea" . $e->getLine();
}
function exceptions_error_handler($severity, $message, $filename, $lineno)
{
    if (error_reporting() == 0) {
        return;
    }
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $filename, $lineno);
    }
}


//no deberias de andar por aqui, aun así Hola
//hecho por Arturo :) si ocupas algo puedes contactarme a arcoal2002@gmail.com
