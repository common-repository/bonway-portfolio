<?php
function bonwaybsp_cuttext($text, $maxLength = 256, $suffix = "...") {
    if(strlen($text) > $maxLength){
        //Strip any HTML tags from the text
        $cutText = strip_tags($text);

        //Shorten the string
        preg_match('/^.{0,' . $maxLength. '}(?:.*?)\b/iu', $cutText, $matches);
        return trim($matches[0]) . $suffix;
    } else {
        return $text;
    }
}