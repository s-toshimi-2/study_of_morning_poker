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

    /**
     * 数値順に手札をソート
     *
     * @param array $cards 手札
     *
     * @return array
     */
    public function sortNumber($cards)
    {
        usort($cards, function($a, $b) {
            return $a['number'] > $b['number'];
        });

        return $cards;
    }

    /**
     * 役   : フラッシュ
     * 条件 : 同種札が5枚揃う
     *
     * @param array $cards
     *
     * @return bool
     */
    private function isFlush($cards)
    {
        $mark = null;
        $result = true;
        foreach ($cards as $card) {
            if (is_null($mark)) {
                $mark = $card['mark'];
                continue;
            }

            if ($card['mark'] !== $mark) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * 役   : フォア・カード
     * 条件 : 同位札が4枚揃ったもの
     *
     * @param array $cards
     *
     * @return bool
     */
    private function isFourCard($cards)
    {
        for($i = 0; $i < 2; $i++) {
            $result = true;
            $tmpCards = array_slice($cards, $i, 4);

            for ($j = 0; $j < 3; $j++) {
                if (!$this->isPair($tmpCards[$j], $tmpCards[$j+1])) {
                    $result = false;
                    break;
                }
            }

            if ($result) break;
        }

        return $result;
    }

    /**
     * ペアかどうか調べる
     *
     * @param array $cardA
     * @param array $cardB
     *
     * @return bool
     */
    private function isPair($cardA, $cardB)
    {
        return $cardA['number'] === $cardB['number'];
    }
}
