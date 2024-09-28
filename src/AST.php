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


        preg_match($this->aPattern['getSpaceBeforeBracket'], $sCalculatedString, $aBracketMatches);
        preg_match_all($this->aPattern['getBracket'], $sCalculatedString, $a);
        $iCount = count($a[0]);


        for($i = 0; $i < $iCount; $i++) {
            preg_match($this->aPattern['getSpaceBeforeBracket'], $sCalculatedString, $aBracketMatches);
            if(!empty($aBracketMatches[0])) {
                $sCalculatedString = str_replace($aBracketMatches[0], $aBracketMatches[1] . '*' . $aBracketMatches[2], $sCalculatedString);
            }
            $sCalculatedString = str_replace($a[0][$i] ,$this->calculation($a[0][$i], true), $sCalculatedString);
        }

        return $this->calculation($sCalculatedString);
    }

    public function calculation($sCalculationString, $bBrackets = false) :string{


        if($bBrackets){;
            $sCalculationString = str_replace('(', '', $sCalculationString);
            $sCalculationString = str_replace(')', '', $sCalculationString);
        }

        preg_match_all($this->aPattern['getEverySign'], str_replace(' ', '', $sCalculationString), $aMatches);



        $aSortedMulti = [];
        $aSortedSimple = [];

        foreach ($aMatches as $aSigns){
            foreach ($aSigns as $sSign) {
                if($sSign == "*" or $sSign == "/"){
                    $aSortedMulti[] = $sSign;
                    #array_unshift($aSortedSigns, $sSign);
                } else {
                    $aSortedSimple[] = $sSign;
                    #$aSortedSigns[] = $sSign;
                }
            }
        }

        $aSortedSigns = array_merge($aSortedMulti, $aSortedSimple);

        foreach ($aSortedSigns as $sSign) {
            $sCalculationString = $this->calculateStep($sCalculationString, $sSign);
        }

        return $sCalculationString;
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
            'getBracket' => "/\(.*?\)/",
            'getSpaceBeforeBracket' => '/(\d+)(\s*\()/',
        ];
    }
}