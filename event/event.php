<?php

require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../functions.php";

$db = "mysql:host={$config['db']['hostname']};";
$db.= "dbname={$config['db']['database']};charset=utf8";
$db = new PDO($db, $config['db']['username'], $config['db']['password']);

if(isset($_GET['event'], $_GET['world']) && is_string($_GET['event']) && is_string($_GET['world'])) {
    $event = $_GET['event'];
    $world = $_GET['world'];

    if(empty($world)) {
        exit;
    }

    $q = $db->prepare("INSERT INTO fw_flatlight_event (world, event, time) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE time = ?");
    $q->execute([$world, $event, time(), time()]);
}
