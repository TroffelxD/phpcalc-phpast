<?php
require_once "vendor/autoload.php";


use Calculate\AST;


$oCalculate = new AST();
$iCalculatedNumber = $oCalculate->calculate("2*3*(5-6)");

echo $iCalculatedNumber;