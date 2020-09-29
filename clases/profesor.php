<?php

class Profesor{
    //profesor:​ Recibe nombre, legajo (validar que sea único)
    
    public $nombre;
    public $legajo;

    public function __construct($nombre,$legajo){

        $this->nombre = $nombre;
        $this->legajo = $legajo;
    }
}    