<?php

define("IV", hex2bin('34857d973953e44afb49ea9d61104d8c'));
const KEY = "c'est pas bien de tricher.";
const CIPHER = "AES-256-CBC";
const LOGFILE = "save.log";
const MAPX = 21;
const MAPY = 21;

function save($map)
{
    fclose(fopen(LOGFILE,'w'));
    $mapEncrypt = openssl_encrypt($map, CIPHER, KEY, 0, IV);
    file_put_contents(LOGFILE, $mapEncrypt, FILE_APPEND);
}

function initialize()
{
    if (file_exists(LOGFILE)) {
        unlink(LOGFILE);
    }

    $target = ['x' => rand(0, 20), 'y' => rand(0, 20)];
    if ($target['x'] === round(MAPX / 2) && $target['y'] === round(MAPY / 2))
        $target = ['x' => rand(0, 20), 'y' => rand(0, 20)];
    $map = null;
    $x = 0;
    $y = 0;

    while ($y != MAPX) {
        while ($x != MAPY) {
            $map .= '1';

            if ($x === round(MAPX / 2) && $y === round(MAPY / 2))
                $map .= '2';
            if ($x === $target['x'] && $y === $target['y'])
                $map .= '3';

            $x++;
        }

        $y++;
        $x = 0;
    }

    save($map);
}

$request = $_SERVER['REQUEST_URI'];

switch ($request) {
    case '/start' :
        initialize();
        break;
    case '/map' :
        map();
        break;
    case (bool)preg_match('/\/move.*/', $request) :
        move();
        break;
    case (bool)preg_match('/\/shoot.*/', $request) :
        shoot();
        break;
    default:
        break;
}