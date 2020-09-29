<?php

class Usuario{
    #email, tipo de usuario, password y foto
    public $correo;
    public $tipo;
    public $clave;
    public $fotoNombre;
    

    public function __construct($correo,$tipo,$clave,$fotoNombre){

        $this->correo = $correo;
        $this->tipo = $tipo;
        $this->clave = $clave;
        $this->fotoNombre = $fotoNombre;
    }
}    