<?php

$addon = function() use ($config, $db) {
    $q = $db->query("SELECT x, y, img, name FROM fw_place");
    $data = $q->fetchAll(PDO::FETCH_OBJ);
    $select = [];

    foreach($data as $row) {
        $row->name = str_replace('"', '', $row->name);

        $sel = ".listusersrow>b+br+br+img[src$=\"{$row->img}\"]~a:after";
        $select[$sel][] = "{$row->name} ({$row->x} / {$row->y})";
    }

    $css = "";

    foreach($select as $s => $c) {
        $content = sizeof($c) > 1 ? "Mögliche Orte: " : "Möglicher Ort: ";
        $content.= implode(', ', $c);

        $css.= "{$s}{content:\"{$content}\"}";
    }

    $css.= ".listusersrow>b+br+br+img~a:after{color:#222;display:block;}";
    $css.= ".listusersrow>b+br+br+img~a:hover{text-decoration:none;}";

    return $css;
};
