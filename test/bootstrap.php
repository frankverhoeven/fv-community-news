<?php

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo 'You need to install the project dependencies using Composer';
    exit(1);
}

require __DIR__ . '/../vendor/autoload.php';
