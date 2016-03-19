<?php

namespace App\Library;

use \Request;

class ViewHelper{
    
    public static function setActive($route) {
        return (Request::path() === $route ? "active" : '');
    }
   
}
