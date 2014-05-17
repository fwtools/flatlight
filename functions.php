<?php

function getWorldByReferer() {
    $ref = $_SERVER['HTTP_REFERER'];

    if(!preg_match("~http://(www.)?welt(\d+)\.(freewar|intercyloon)\.de/.*~", $ref, $match)) {
        return "";
    }

    return "de{$match[2]}";
}
