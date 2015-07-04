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
     * 役   : フォア・カード
     * 条件 : 同位札が4枚揃ったもの
     *
     * @param array $cards
     *
     * @return bool
     */
    private function isFourCard($cards)
    {
        return $this->countPair($cards) === 3;
    }


    /**
     * 役   : フルハウス
     * 条件 : 同位札が3枚と, 同位札が2枚
     *
     * @param array $cards
     *
     * @return bool
     */
    private function isFullHouse($cards)
    {
        $result = false;
        foreach([[0, 3], [2, 0]] as $i) {
            $threeCards = array_slice($cards, $i[0], 3);
            $twoCards = array_slice($cards, $i[1], 2);

            if ($this->countPair($threeCards) === 2 && $this->countPair($twoCards) === 1) {
                $result = true;
                break;
            }
        }

        return $result;
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
     * 役   : スリーカード
     * 条件 : 同位札が3枚揃ったもの
     *
     * @param array $cards
     *
     * @return bool
     */
    private function isThreeCard($cards)
    {
        $result = false;
        for($i = 0; $i < 3; $i++) {
            $tmpCards = array_slice($cards, $i, 3);

            if ($this->countPair($tmpCards) == 2) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * 役   : ツー・ペア
     * 条件 : 2枚ずつの同位札が2組
     *
     * @param array $cards
     *
     * @return bool
     */
    private function isTwoPair($cards)
    {
        return $this->countPair($cards) === 2;
    }

    /**
     * 役   : ワン・ペア
     * 条件 : 同位札が2枚揃ったもの
     *
     * @param array $cards
     *
     * @return bool
     */
    private function isOnePair($cards)
    {
        return $this->countPair($cards) === 1;
    }

    /**
     * ペアの数を数える
     *
     * @param array $cards
     *
     * @return int
     */
    private function countPair($cards)
    {
        $count = 0;
        $length = count($cards)-1;
        for($i = 0; $i < $length; $i++) {
            if ($this->isPair($cards[$i], $cards[$i+1])) $count++;
        }

        return $count;
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
