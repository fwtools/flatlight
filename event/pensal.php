<?php

$event = function() use ($db) {
    $world = $_GET['world'];

    if(!is_string($world)) {
        return "";
    }

    $q = $db->prepare("SELECT * FROM fw_flatlight_event WHERE event = 'pensal-available' && world = ?");
    $q->execute([$world]);

    if($row = $q->fetch(PDO::FETCH_OBJ)) {
        $eventMin = ((int) date('i', $row->time));
        $currMin  = ((int) date('i'));

        $eventDiff = $eventMin - $eventMin % 30;
        $currDiff = $currMin - $currMin % 30;

        if($row->time > time() - 30 * 60 && $eventDiff == $currDiff) {
            return '.positiontext:after { content: "\f134"; }';
        }
    }

    return "";
};
