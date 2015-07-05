<?php

require_once __DIR__ . '/../vendor/autoload.php';

$countPlayers = 2;  // プレイ人数

$games = new \Poker\Game($countPlayers);
$games->play();
$games->check();
$games->result();
