<?php

include './vendor/autoload.php';

$t = new \CliEngine\IO\Terminal();

$t->cols = 60;
$t->rows = 20;

$c = new \CliEngine\Draw\Canvas($t);

$c->build();
$c->live();