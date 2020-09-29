<?php
require_once './clases/response.php';

require_once './clases/usuario.php';
require_once './clases/auto.php';


require_once './clases/file.php';
require_once './clases/token.php';

//$decoded = JWT::decode($token, $key, array('HS256'));

/*
 * 
 * METODOS
 * GET: OBTENER RECURSOS.
 * POST: CREAR RECURSOS.
 * PUT: MODIFICAR RECURSOS.
 * DELETE: BORRAR RECURSOS.
 * 
 */

// var_dump($_SERVER);

// URL POST : http://localhost/parcial/index.php/registro


//Métodos mágicos
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'];   //?? 0;

//definicion de variables propias para los archivos
$fileUsuario = './archivos/users.txt';
$fileAuto= './archivos/auto.txt';
$fileProfesores = './archivos/profesores.txt';
$fileMateriasProfesores = './archivos/materias-profesores.txt';

switch ($path) {
   
    case '/registro':
    //1.  (POST) registro. ​Registrar un usuario con los siguientes datos: email, tipo de usuario, password y foto. El tipo de usuario puede ser admin o user. Validar que el mail no esté registrado previamente.

        switch ($method) {
            case 'POST': //Parametros por el body del usuario
                $correo = $_POST['email']?? '';
                $tipo = $_POST['tipo']?? '';
                $clave = $_POST['password']?? '';
                $archivo = $_FILES['imagen']?? '';
                $fotoNombre = '';  
    
                $response = new Response();

                if (isset($correo) && isset($tipo) && isset($clave) && isset($archivo)) {

                    //Json
                    //Verificar si existe usuario
                    $bandera=false;                              // Bandera que esta en falso asumo que no esta
                    $arrayJason=Datos::leerJSON($fileUsuario);
                    
                    if ($arrayJason != null) {
                        foreach ($arrayJason as $value) {
                            if($value->correo==$correo){
                                $bandera=true;
                            }
                        }
                    }            
                    
                    if ($bandera == false) {
                        $nombreImagen = explode('@', $correo);
                        $nombreImagen = $nombreImagen[0];
                        $imagenSubida = Datos::tratarArchivo($archivo,$nombreImagen);
                        if($imagenSubida=="error de size"){
                            $response->status = "No se puede dar Alta ";
                            $response->data = $imagenSubida;
                        }else{
                            $fotoNombre = $imagenSubida;  
                            //encriptar la clave
                            $clave = Datos::encliptarClave($clave);  
                            $datos = new Usuario($correo, $tipo ,$clave, $fotoNombre);
                            $rtaJason = Datos::guardarJSON($fileUsuario,$datos);
                            $response->data = "Se Grabo Usuario";
                        }
                            
                    }else{
                        $response->status = "No se puede dar Alta ";
                        $response->data = "Usuario ya Registrado";                       
                    }
                    echo json_encode($response);                
                }
                else{
                    $response->data = 'Faltan datos'; //se usa en el lugar que hubo una falla
                    $response->status = 'fail';       // falla
                    echo json_encode($response);
                } 
            break;      
        }
       
    break;

    case '/login':
    // (POST) login: ​Los usuarios deberán loguearse y se les devolverá un token con email y tipo en caso de estar registrados, caso contrario se informará el error.      
        switch ($method) {
            case 'POST': //Parametros por el body del usuario
                $correo = $_POST['email']?? '';
                $clave = $_POST['password']?? '';
                
                $response = new Response();
    
                if (isset($correo) && isset($clave)) {
                    //Verificar si existe usuario y clave son correctas
                    $bandera=false;            
                    $arrayJason=Datos::leerJSON($fileUsuario);
                    
                    if ($arrayJason != null) {
                        foreach ($arrayJason as $value) {
                            if($value->correo==$correo){ // && $value->clave==$clave){
                                $generarClave = Datos::encliptarClave($clave); 
                                $obtengoClaveGrabada = $value->clave;
                                if ($generarClave == $obtengoClaveGrabada) {
                                    $bandera=true;
                                }
                            }
                        }
                    }            
                    
                    if ($bandera == true) {
                        $rtaJason = Token::generoToken($correo,$clave);
                        $response->data = $rtaJason;
                    }else{
                        $response->status = "No coincide las credenciales";
                        $response->data = "Usuario o Clave";                       
                    }
                    echo json_encode($response);                
                }
                else{
                    $response->data = 'Faltan datos para Login (Token)'; //se usa en el lugar que hubo una falla
                    $response->status = 'fail';                          // falla
                    echo json_encode($response);
                } 
            break;      
        }
    break;

    case '/ingreso':
        //instancioamos la clase
        $response = new Response();                  
        //Autenticación 
        $headersEnvio = getallheaders(); //Leeo toda mi cabecera
        $respuestaToken = Token::comprobarToken($headersEnvio);
        
        if ($respuestaToken == "Error JWT"){
            $response->data = "Error  JWT";
            $response->status = 'fail';
        }    
        else
        {    
            switch ($method) {
                case 'POST': //Parametros por el body del usuario Autenticar antes de todo
                    
                        $patente = $_POST['patente']?? '';
                        //print_r($respuestaToken);
                        //$correo = $respuestaToken['email'] ;
                        $correo = 'admin@mail.com';
                        $fecha = date('l jS \of F Y h:i:s A');
                        //print_r($fecha);
                        //die();
            
                        if (isset($patente) ) {  
                                //$id = $nombre.time();
                                $datos = new Auto($patente, $correo, $fecha,"","","");
                                $rtaJason = Datos::guardarJSON($fileAuto,$datos);
                                $response->data = "Se Grabo Auto";            
                        }
                        else{
                            $response->data = 'Faltan datos'; //se usa en el lugar que hubo una falla
                            $response->status = 'fail';       // falla
                        } 
                        
                break;
                
            } 
            echo json_encode($response);  
        }          
    break;   

    case '/ingreso':
        //5. (POST) asignacion:​ Recibe legajo del profesor, id de la materia y turno (manana o noche) y lo guarda en el archivo materias-profesores. No se debe poder asignar el mismo legajo en el mismo turno y materia.
        //8. (GET) asignacion: ​Muestra un listado con todas las materias asignadas a cada profesor.

            //instancioamos la clase
        $response = new Response();                  
        //Autenticación 
        $headersEnvio = getallheaders(); //Leeo toda mi cabecera
        $respuestaToken = Token::comprobarToken($headersEnvio);
        
        if ($respuestaToken == "Error JWT"){
            $response->data = "Error  JWT";
            $response->status = 'fail';
        }    
        else
        {    
                switch ($method) {
                    
                    case 'GET':
                        // Leer Jason            
                        $arrayJason=Datos::leerJSON($fileAuto);
                        $ListaDatos = array();
                        print_r($arrayJason);
                        die();
                        foreach ($arrayJason as $value) {
                            array_push($ListaDatos,$value);
                        }
                       
                        $response->data = $ListaDatos;
                    break;    
                }
        } 
        echo json_encode($response);        
    break;
    default:
        //var_dump($path);
        //die();
        $response = new Response();
        $resultado = substr($path, 0, 9);
        //print_r($resultado);
        //die();
        if ($resultado == "/retiro/a") {
            $sacoPatente = array();
            $sacoPatente = explode('/', $path);
           
            $tengoPatente = $sacoPatente[2];
            //print_r($tengoPatente);
            //die();
            
            $arrayJason=Datos::leerJSON($fileAuto);
                 
            if ($arrayJason != null) {
                foreach ($arrayJason as $value) { 
                    if($value->patente == $tengoPatente){
                        $horario = 'menos de 4 horas ';
                        $fecha_egreso = date('l jS \of F Y h:i:s A');
                        $importe = 100*3;

                        $datos = new Auto($tengoPatente,'','', "$horario","$fecha_egreso","$importe");
                        $rtaJason = Datos::guardarJSON($fileAuto,$datos);
                        $response->data = "Se Grabo Auto";            
                        echo json_encode($response);

                    }else{
                        $response->data = "Error patente no existe";
                        $response->status = 'fail'; 
                        echo json_encode($response);
                    }
                }
            } 
            else{
                $response->data = 'No hay datos para guerdar'; //se usa en el lugar que hubo una falla
                $response->status = 'fail';  
                echo json_encode($response);                     
            }      
           
        }
        else{
            $response->data = 'Path erroneo'; //se usa en el lugar que hubo una falla
            $response->status = 'fail'; 
            echo json_encode($response);
        } 
}