<?php

if (!function_exists('banglaNumber')) {
    function banglaNumber($number)
    {
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bangla = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($english, $bangla, $number);
    }
}

if (!function_exists('getYoutubeTitle')) {
    function getYoutubeTitle($videoId)
    {
        try {
            $json = file_get_contents("https://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=" . $videoId . "&format=json");
            $data = json_decode($json, true);
            return $data['title'] ?? 'ভিডিও';
        } catch (\Exception $e) {
            return 'ভিডিও';
        }
    }
}
