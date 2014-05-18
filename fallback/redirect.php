<?php

if(isset($_GET['l']) && is_string($_GET['l'])) {
    $location = $_GET['l'];
    header("Location: {$location}", true, 301);
}
