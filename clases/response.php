<?php

class Response {

    public $status;
    public $data;

    public function __construct()
    {
        $this->status = 'Success(Exito)';
    }
}