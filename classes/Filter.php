<?php

class Filter {
    public static function onlyAlphaNumeric($string) {
        return preg_replace("/[^a-zA-Z0-9]+/", "", $string);
    }
}
