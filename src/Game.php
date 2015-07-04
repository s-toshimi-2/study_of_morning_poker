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

    /**
     * ゲーム開始
     */
    public function play()
    {
        foreach ($this->_players as $player) {
            // カードを捨てる
            $cards = $player->discard();
            $dealCardNum = count($cards);

            // カードを捨てた枚数分を手札に加える
            $cards = $this->_cards->deal($dealCardNum);
            $player->draw($cards);
        }
    }
}
