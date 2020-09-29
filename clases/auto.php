<?php

class Auto{
    //patente, fecha_ingreso (dia y hora) y el email
    public $patente;
    public $fecha_ingreso;
    public $email;  
    public $horario;
    public $fecha_egreso;
    public $importe;

    public function __construct($patente,$fecha_ingreso,$email,$horario,$fecha_egreso,$importe){

        $this->patente = $patente;
        $this->fecha_ingreso = $fecha_ingreso;
        $this->email = $email;
        $this->horario = $horario;
        $this->fecha_egreso = $fecha_egreso;
        $this->importe = $importe;
    }
}    