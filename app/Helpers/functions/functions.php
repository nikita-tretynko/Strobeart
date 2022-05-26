<?php

function soCode($value): string
{
    $secure = 19537508;
    return dechex(($secure + $value * 2) * 3);
}

function deSoCode($value){
    $secure = 19537508;
    return ((hexdec($value) / 3) - $secure) / 2;
}
