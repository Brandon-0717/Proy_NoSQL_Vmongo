<?php
require_once "global.php"; // Asegúrate de definir las constantes de conexión
require_once __DIR__ . '/../vendor/autoload.php';

class ConexionMongo
{
    private static $conn = null;

    public static function conectar()
    {
        try {
            if (self::$conn === null) {
                $client = new MongoDB\Client("mongodb://localhost:27017");
                self::$conn = $client->selectDatabase("proyectoMongo");
                //echo "<script>console.log('Conexión a MongoDB exitosa');</script>";
            }
            return self::$conn;
        } catch (MongoDB\Driver\Exception\Exception $ex) {
            die("Error de conexión: " . $ex->getMessage());      
        }
    }
}
//ConexionMongo::conectar();
?>