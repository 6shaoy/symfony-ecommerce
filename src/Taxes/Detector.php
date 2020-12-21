<?php

namespace App\Taxes;

class Detector
{
    protected $seuil;

    public function __construct($seuil)
    {
        $this->seuil = $seuil;
    }
    public function detect(float $amount): bool {
        return $amount > $this->seuil;
    }
}