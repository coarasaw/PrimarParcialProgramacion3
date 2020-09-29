<?php
require __DIR__ .'/..//vendor/autoload.php'; 
use \Firebase\JWT\JWT;

class Token{

    public static function generoToken($correo,$clave){
        // llave
        $key = "pro3-parcial";  
        //Generamos el Payload-CargaDatos
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,               //vencimiento del token
            "nbf" => 1357000000,
            "email" => $correo,
            "clave" => $clave
        );
        // Se Genera el Token y devuelve
        return $jwt = JWT::encode($payload, $key);
    }

    public static function comprobarToken($headersEnvio){
        try {
            // llave
            $key = "pro3-parcial";  
            $headers = $headersEnvio; //getallheaders(); //Leeo toda mi cabecera
            $miToken = $headers["token"] ?? 'No mando Token'; // Si se genero el Token aca lo obtengo de la cabecera
            if (isset($miToken)){
                $decoded = JWT::decode($miToken, $key, array('HS256'));
                return $decoded;
            }
            
        } 
        catch (\Throwable $th) {
            //echo $th->getMessage() . " Error JWT";
            return "Error JWT";
        }
    }
}