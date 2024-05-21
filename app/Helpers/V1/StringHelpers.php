<?php

if (!function_exists('extractTextsBasedActions')) {
    function extractTextsBasedActions($inputString)
    {
        $pattern = '/{{[^}]*}}/'; // Regular expression to match {{...}}
        preg_match_all($pattern, $inputString, $matches);
        return $matches[0];
    }
}

if (!function_exists('explodeCommaSeparatedString')) {
    function explodeCommaSeparatedString($inputString)
    {
        if (is_string($inputString)) {
            // Split the input string by commas
            $exploded = explode(',', $inputString);

            // Trim and remove empty elements
            $exploded = array_map('trim', $exploded);
            $exploded = array_filter($exploded);

            return $exploded;
        } elseif (is_array($inputString)) {
            // If it's already an array, trim and remove empty elements
            return array_filter(array_map('trim', $inputString));
        }

        // Return an empty array for other cases (null, objects, etc.)
        return [];
    }
}
if (!function_exists('extractFunctionName')) {
    function extractFunctionName($placeholder)
    {
        $pattern = '/{{(.*?):(.*?)}}/';
        preg_match_all($pattern, $placeholder, $matches, PREG_SET_ORDER);

        $functions = [];

        foreach ($matches as $match) {
            $functions[] = [
                'fullMatch' => $match[0],
                'functionName' => $match[1],
                'args' => explode(':', $match[2]),
            ];
        }

        return $functions;
    }
}
if (!function_exists('replacePlaceholderInTemplate')) {
    function replacePlaceholderInTemplate($template, $placeholder, $replacement)
    {
        return str_replace($placeholder, $replacement, $template);
    }
}
