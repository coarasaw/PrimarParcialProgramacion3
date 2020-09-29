<?php

class Datos {

    public static function guardarTxt($archivo, $datos) {
        $file = fopen($archivo, 'a');
        $rta = fwrite($file, $datos .PHP_EOL);
        fclose($file);
        return $rta;
    }

    public static function leerTxt($archivo) {
        $file = fopen($archivo, 'r');
        $listaDatos = array();

        while (!feof($file)) {
            $linea = fgets($file);
            $datos = explode('*', $linea);

            if (count($datos)  > 1) {
                $nuevoAuto = new Auto($datos[0], $datos[1], $datos[2], $datos[3], $datos[4]);
                array_push($listaDatos, $nuevoAuto);
            }
        }

        fclose($file);
        return $listaDatos;
    }

    public static function guardarSerialize($archivo, $datos) {
        $file = fopen($archivo, 'a');
        $rta = fwrite($file,serialize($datos) .PHP_EOL);
        fclose($file);
        return $rta;
    }

    public static function leerSerialize($ruta) {
        $lista = array();
        $file = fopen($ruta, 'r');
            while(!feof($file)){
                $objeto=unserialize(fgets($file));
                if($objeto!=null){
                    array_push($lista,$objeto);
                }
            }
        fclose($file);
        return $lista;
    }

    /* **************************************************************************** */

    public static function guardarJSON($ruta, $objeto)
    {
        $array=Datos::leerJSON($ruta);
        $ar=fopen($ruta,"w");

        if (isset($array)) {
           array_push($array,$objeto);
           fwrite($ar,json_encode($array));
        }else{
            $array2=array();
            array_push($array2,$objeto);
            fwrite($ar,json_encode($array2));
        }
        fclose($ar);
    }


    static public function leerJSON($ruta) {

        if (file_exists($ruta)) {
            $ar = fopen($ruta, 'r');
            $lista=json_decode(fgets($ar));
            fclose($ar);

            if (isset($lista)) {
                return $lista;
            }else{
                //echo "La lista esta vacia ";
            }
        }else{
            //echo "El archivo no exixte, se creara";
        }   
    }

    //static public function tratarArchivo($archivoTrabajar,$id){ // $id este caso es si quiero usar un campo por ej DNI
    static public function tratarArchivo($archivoTrabajar,$nombre){ 
        
        
        if ($archivoTrabajar['size'] > 35840) {
            $nombreTrabajos = "error de size";
        }else{
            $tmp_name = $archivoTrabajar['tmp_name'];
            $nombreTratar = $nombre;
            $name = $nombre;
            $extension = $archivoTrabajar['name'];
            $extension = explode('.', $extension);
            $extension = $extension[1];
            //$nombre = $id.'_'.$name[0].time() . '.' . explode('.', $name)[1];  // Investigar explode
            $nombreTrabajos = $name.time() . '.' . $extension; 
            $folder = 'imagenes/';
            move_uploaded_file($tmp_name, $folder . $nombreTrabajos);
        }
        return $nombreTrabajos;
    }

    static public function moverArchivo($archivoTrabajar,$nombreImagen){
        
        if ($archivoTrabajar['size'] > 35840) {
            $banderaNombre = "error de size";
        }else{

            $origen = "imagenes/$nombreImagen";
            $destino = "backup/$nombreImagen";
            if (copy($origen, $destino))
                unlink($origen);

            $tmp_name = $archivoTrabajar['tmp_name'];
            //$name = $archivoTrabajar['name'];
            //$nombre = $id.'_'.$name[0].time() . '.' . explode('.', $name)[1];  // Investigar explode
            $nombre = $nombreImagen;
            $folder = 'imagenes/';
            move_uploaded_file($tmp_name, $folder . $nombre);
            $banderaNombre = $nombre;
        }
        return $banderaNombre;
    }

    static public function encliptarClave($clave){
        $claveATratar = $clave;
	    $salt = "f#@V)Hu^%Hgfds";
        $devolver_clave = sha1($salt.$claveATratar);
        return $devolver_clave;
    }
}