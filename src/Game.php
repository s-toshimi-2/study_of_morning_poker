<?php

namespace Poker;

/**
 * カードをコントロールするクラス
 */
class Game
{
    private $_cards;
    private $_players;

    public function __construct($countPlayers)
    {
        $this->_cards = new Cards();

        $this->_players = [];
        for ($i = 0; $i < $countPlayers; $i++) {
            $this->_players[] = new Player();
        }

        $this->init();
    }

    /**
     * ゲーム初期化
     */
    public function init()
    {
        $this->_cards->init();

        foreach ($this->_players as $player) {
            $cards = $this->_cards->deal(5);
            $player->draw($cards);
        }
    }
}
