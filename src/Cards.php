<?php

namespace Poker;

/**
 * カードの準備, 配布などをコントロールするクラス
 */
class Cards
{
    private $_deck; // 山札
    private $_marks = ['clover', 'diamond', 'heart', 'spade']; // カードの絵柄

    public function __construct()
    {
        $this->init();
    }

    /**
     * カード初期化
     */
    public function init()
    {
        // 山札を空にする
        $this->_deck = [];

        foreach ($this->_marks as $mark) {
            for ($i = 1; $i <= 13; $i++) {
                $this->_deck[] = [
                    'mark'   => $mark,
                    'number' => $i
                ];
            }
        }

        // カードシャッフル
        shuffle($this->_deck);
    }

    /**
     * 指定枚数を配る
     *
     * @param int $num 配布枚数
     *
     * @return array
     */
    public function deal($num)
    {
        if (count($this->_deck) === 0) {
            return [];
        }

        if (count($this->_deck) >= $num) {
            return array_splice($this->_deck, 0, $num);
        }

        $cards = $this->_deck;
        $this->_deck = [];

        return $cards;
    }
}
