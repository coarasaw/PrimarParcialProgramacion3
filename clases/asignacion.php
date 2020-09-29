<?php

class Asignacion{
    //asignacion:​ Recibe legajo del profesor, id de la materia y turno (manana o noche)
    
    public $legajo;
    public $id;
    public $turno;

    public function __construct($legajo,$id,$turno){

        $this->legajo = $legajo;
        $this->id = $id;
        $this->turno = $turno;
    }
}    