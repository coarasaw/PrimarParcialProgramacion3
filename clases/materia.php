<?php

class Materia{
    //materia:​ Recibe nombre, cuatrimestre y lo guarda en el archivo materias.xxx. Agregar un id único para cada materia.
    public $id;
    public $nombre;
    public $cuatrimestre;

    public function __construct($id,$nombre,$cuatrimestre){

        $this->id = $id;
        $this->nombre = $nombre;
        $this->cuatrimestre = $cuatrimestre;
    }
}    