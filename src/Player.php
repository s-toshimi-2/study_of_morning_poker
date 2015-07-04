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

    /**
     * 指定カードを捨てる
     *
     * @param array $indexes 捨てるカードの番号
     *
     * @return array 捨てるカード
     */
    public function discard($indexes)
    {
        $cards = [];

        foreach ($indexes as $index) {
            $cards = array_merge($cards, array_splice($this->_cards, $index, 1));
        }

        return $cards;
    }

    /**
     * 手札を表示
     */
    public function show()
    {
        foreach ($this->_cards as $k => $v) {
            printf("index : %d, type : %10s, number : %2d\n", $k, $v['type'], $v['number']);
        }
    }
}
