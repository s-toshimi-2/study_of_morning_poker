<?php

require_once __DIR__ . '/../vendor/autoload.php';

$games = new \Poker\Game(2);
$games->play();
$games->check();
$games->result();
