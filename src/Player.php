<?php

namespace Poker;

/**
 * プレイヤークラス
 */
class Player
{
    private $_cards; // 手札

    public function __construct()
    {
        $this->init();
    }

    /**
     * プレイヤー初期化
     */
    public function init()
    {
        $this->_cards = [];
    }

    /**
     * カードを引く
     *
     * @param array $cards
     */
    public function draw($cards)
    {
        $this->_cards = array_merge($this->_cards, $cards);
    }
}
