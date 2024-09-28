<?php
require_once "vendor/autoload.php";


use Calculate\AST;


$oCalculate = new AST();
$iCalculatedNumber = $oCalculate->calculate("(4*12)/4(3*4)");
echo $iCalculatedNumber;