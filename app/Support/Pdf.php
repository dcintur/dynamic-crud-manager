<?php

namespace App\Support;

use Barryvdh\DomPDF\Facade\Pdf as DomPdf;

class Pdf
{
    public static function __callStatic($method, $args)
    {
        return DomPdf::$method(...$args);
    }

    public static function loadHTML($html)
    {
        return DomPdf::loadHTML($html);
    }
}