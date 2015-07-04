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
            // プレイヤーが捨てるカードを決める
            printf("どの手札を捨てますか?\n");
            $player->show();
            $line = trim(fgets(STDIN));

            // 捨てるカードの番号取得
            $indexes = explode(' ', $line);
            $indexes = array_unique($indexes);

            // 不要な要素を削除
            $deleteIndex = array_search('', $indexes);
            if ($deleteIndex !== false) {
                array_splice($indexes, $deleteIndex, 1);
            }
            arsort($indexes);

            // カードを捨てる
            $cards = $player->discard($indexes);
            $dealCardNum = count($cards);

            // カードを捨てた枚数分を手札に加える
            $cards = $this->_cards->deal($dealCardNum);
            $player->draw($cards);

            // 手札表示
            $player->show();
        }
    }
}
