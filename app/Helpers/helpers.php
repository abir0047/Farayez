<?php

if (!function_exists('banglaNumber')) {
    function banglaNumber($number)
    {
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bangla = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($english, $bangla, $number);
    }
}
