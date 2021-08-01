<?php

define("IV", hex2bin('34857d973953e44afb49ea9d61104d8c'));
const KEY = "c'est pas bien de tricher.";
const CIPHER = "AES-256-CBC";
const LOGFILE = "save.log";
const MAPX = 21;
const MAPY = 21;
const CLOSETARGET = [
    MAPX, MAPX + 1, MAPX + 2, MAPX - 1, MAPX - 2,
    MAPX * 2,(MAPX * 2) - 2, (MAPX * 2) - 1, (MAPX * 2) + 1, (MAPX * 2) + 2,
    -MAPX, -MAPX - 1, -MAPX - 2, -MAPX + 1, -MAPX + 2,
    -MAPX * 2, (-MAPX * 2) - 2, (-MAPX * 2) - 1, (-MAPX * 2) + 1, (-MAPX * 2) + 2
];

function save($map)
{
    fclose(fopen(LOGFILE,'w'));
    $mapEncrypt = openssl_encrypt($map, CIPHER, KEY, 0, IV);
    file_put_contents(LOGFILE, $mapEncrypt, FILE_APPEND);
}

function getMap()
{
    $mapEncrypt = file_get_contents(LOGFILE, false);

    return openssl_decrypt($mapEncrypt, CIPHER, KEY, 0, IV);
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

function move()
{
    $map = getMap();
    $position = strpos($map, '2');
    $target = null;
    if (strpos($map, '3')) {
        $target = strpos($map, '3');
    } elseif (strpos($map, "4")) {
        $target = strpos($map, '4');
    } elseif (strpos($map, '5')) {
        $target = strpos($map, '5');
    }

    if ($_REQUEST['action'] === 'up') {
        if (
            $map[$position - MAPX] === '1'
            && $position - MAPX >= 0
            && $position - MAPX <= MAPX * MAPY
        ) {
            $map[$position] = '1';
            $map[$position - MAPX] = '2';
        }
    } else if ($_REQUEST['action'] === 'down') {
        if (
            $map[$position + MAPX] === '1'
            && $position + MAPX >= 0
            && $position + MAPX <= MAPX * MAPY
        ) {
            $map[$position] = '1';
            $map[$position + MAPX] = '2';
        }
    } else if ($_REQUEST['action'] === 'right') {
        if (
            $map[$position + 1] === '1'
            && ($position + 1) % 21 != 0
            && $position + MAPX <= MAPX * MAPY
        ) {
            $map[$position] = '1';
            $map[$position + 1] = '2';
        }
    } else if ($_REQUEST['action'] === 'left') {
        if (
            $map[$position - 1] === '1'
            && $position - 1 >= 0
            && $position % 21 != 0
        ) {
            $map[$position] = '1';
            $map[$position - 1] = '2';
        }
    }

    save($map);
    $position = strpos($map, '2');
    if (
        ($position - $target <= 2 && $position - $target >= -2)
        || in_array($position - $target, CLOSETARGET)
    ) {
        $data = [
            "position" => [
                "x" => ($position % 21) + 1,
                "y" => floor(($position / 21))
            ],
            "target" => [
                "x" => ($target % 21) + 1,
                "y" => floor(($target / 21))
            ]
        ];
    } else {
        $data = [
            "position" => [
                "x" => ($position % 21) + 1,
                "y" => floor(($position / 21))
            ],
            "target" => null
        ];
    }

    echo json_encode($data);
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