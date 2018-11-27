<?php
// Incluir os dados do composer
require 'vendor/autoload.php';

use \MyApp\LoopController;

$loopController = new LoopController();
$loopController->iniciaServico();
