<?php
/**
 * Feeds to Social Media
 * Copyright (c) 2018 Demiurge Ash
 *
 * The MIT License
 */

require __DIR__.'/vendor/autoload.php';

new Feeds2Social\Config();

try{

    if ($_GET['mode'] == 'install') {
        $install = new Feeds2Social\Install();
        echo 'Installation is complete';
        exit;
    }

    $start = new Feeds2Social\Stream();

}catch (\Exception $e) {
    echo 'Error: ' .  $e->getMessage() . PHP_EOL;
}