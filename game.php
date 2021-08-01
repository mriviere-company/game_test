<?php

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