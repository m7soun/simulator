<?php

if (!function_exists('getCurrentTimeInDubaiGST')) {
    function getCurrentTimeInDubaiGST() {
        $timezone = new DateTimeZone('Asia/Dubai');
        $currentTime = new DateTime('now', $timezone);
        return $currentTime->format('Y-m-d H:i:s');
    }
}
