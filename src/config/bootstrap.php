<?php
/*
Este arquivo tem como utilidade configurar nossa dependência de dotenv para utilizar 
variáveis de ambiente deixando o código mais seguro.
*/


require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotevn = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotevn->load();