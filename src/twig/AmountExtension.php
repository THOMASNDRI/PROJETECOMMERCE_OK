<?php

namespace App\twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountExtension extends AbstractExtension {
    public function getFilters(){
        return [ new TwigFilter('amount', [$this, 'amount'])];
    }
    public function amount($value, string $symbol ='€', string $descap = ',', string $thousandsep =' '){
        // 19223 => 192,23 €
        $finalvalue = $value / 100;
        // 192.23
        $finalvalue = number_format($finalvalue, 2, $descap, $thousandsep);
        // 192,23
        return $finalvalue . ' ' . $symbol;
    }
}