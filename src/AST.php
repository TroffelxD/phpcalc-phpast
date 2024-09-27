<?php

namespace Calculate;

class AST {

    private array $aPattern;

    public function __construct() {

        $this->aPattern = $this->pattern();
    }


    /**
     * @param string $sCalculatedString Ein mathematischer Ausdruck als String.
     * @return int Das berechnete Ergebnis als Ganzzahl.
     */

    public function calculate(string $sCalculatedString) :int {

        //TODO Werte in Klammern werden zuerest ausgerechnet!
        // Wurzelrechnung, Hochrechnung, ...



        preg_match_all($this->aPattern['getEverySign'], str_replace(' ', '', $sCalculatedString), $aMatches);

        $aSortedSigns = [];

        foreach ($aMatches as $aSigns){
            foreach ($aSigns as $sSign) {
                if($sSign == "*" or $sSign == "/"){
                    array_unshift($aSortedSigns, $sSign);
                } else {
                    $aSortedSigns[] = $sSign;
                }
            }
        }


        foreach ($aSortedSigns as $sSign) {
            $sCalculatedString = $this->calculateStep($sCalculatedString, $sSign);
        }

        return $sCalculatedString;
    }

    private function calculateStep ($sCalculation, $Sign) :string{

        preg_match($this->aPattern["getNumberBefore$Sign"], $sCalculation, $aFirstNumb);
        preg_match($this->aPattern["getNumberAfter$Sign"], $sCalculation, $aSecondNumb);
        preg_match($this->aPattern["get$Sign"], $sCalculation, $sSign);


        $aNew = str_replace($aFirstNumb[0].$sSign[0].$aSecondNumb[0], '' ,$sCalculation);


        return match ($Sign) {
            "+" => str_replace($aFirstNumb[0].$sSign[0].$aSecondNumb[0], (int)$aFirstNumb[0] + (int)$aSecondNumb[0] ,$sCalculation),
            "-" => str_replace($aFirstNumb[0].$sSign[0].$aSecondNumb[0], (int)$aFirstNumb[0] - (int)$aSecondNumb[0] ,$sCalculation),
            "*" => str_replace($aFirstNumb[0].$sSign[0].$aSecondNumb[0], (int)$aFirstNumb[0] * (int)$aSecondNumb[0] ,$sCalculation),
            "/" => str_replace($aFirstNumb[0].$sSign[0].$aSecondNumb[0], (int)$aFirstNumb[0] / (int)$aSecondNumb[0] ,$sCalculation),
            default => false,
        };
    }

    public function pattern () :array {

        return [
            'get+' => '/\+/',
            'get-' => '/\-/',
            'get*' => '/\*/',
            'get/' => '/\//',
            'getEverySign' => '/[+\-*\/]/',
            'getNumberBefore+' => '/\d+(?=\+)/',
            'getNumberAfter+' => '/(?<=\+)\d+/',
            'getNumberBefore-' => '/\d+(?=\-)/',
            'getNumberAfter-' => '/(?<=\-)\d+/',
            'getNumberBefore*' => '/\d+(?=\*)/',
            'getNumberAfter*' => '/(?<=\*)\d+/',
            'getNumberBefore/' => '/\d+(?=\/)/',
            'getNumberAfter/' => '/(?<=\/)\d+/',
            'getBracket' => "/\((.*?)\)/",
        ];
    }
}