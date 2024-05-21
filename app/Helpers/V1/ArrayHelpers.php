<?php

if (!function_exists('extractArgs')) {
    function extractArgs($array)
    {
        $result = [];

        foreach ($array as $index => $value) {
            $result["var_$index"] = $value;
        }

        return $result;
    }
}
