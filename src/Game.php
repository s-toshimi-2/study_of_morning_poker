<?php

namespace Poker;

/**
 * カードをコントロールするクラス
 */
class Game
{
    private $_cards;
    private $_players;
    private $_result;

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
        foreach ($this->_players as  $k => $player) {
            // プレイヤーが捨てるカードを決める
            printf("player%sの番です. 捨てるカードの番号を入力してください. (捨てない場合はEnterを押してください)?\n", $k);
            printf("入力例) 1 2 4\n", $k);
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
     * 役確認
     */
    public function check()
    {
        $this->_result = [];
        foreach ($this->_players as $player) {
            $cards = $player->getCards();
            $cards = $this->sortNumber($cards);

            if ($this->isRoyalStraightFlush($cards)) {
                $this->_result[] = 'ロイヤルストレート・フラッシュ';
                continue;
            }

            if ($this->isStraightFlush($cards)) {
                $this->_result[] = 'ストレート・フラッシュ';
                continue;
            }

            if ($this->isFourCard($cards)) {
                $this->_result[] = 'フォア・カード';
                continue;
            }

            if ($this->isFullHouse($cards)) {
                $this->_result[] = 'フルハウス';
                continue;
            }

            if ($this->isFlush($cards)) {
                $this->_result[] = 'フラッシュ';
                continue;
            }

            if ($this->isStraight($cards)) {
                $this->_result[] = 'ストレート';
                continue;
            }

            if ($this->isThreeCard($cards)) {
                $this->_result[] = 'スリーカード';
                continue;
            }

            if ($this->isTwoPair($cards)) {
                $this->_result[] = 'ツー・ペア';
                continue;
            }

            if ($this->isOnePair($cards)) {
                $this->_result[] = 'ワン・ペア';
                continue;
            }

            $this->_result[] = '役なし';
        }
    }

    /**
     * 最終結果
     */
    public function result()
    {
        printf("-----------------------------------\n");
        printf("             結果発表              \n");
        printf("-----------------------------------\n");
        foreach ($this->_result as $k => $v) {
            printf("player%d : %s\n", $k, $v);
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
     * 手札の数字のみを返す
     *
     * @param array $cards
     *
     * @return array
     */
    private function getNumberList($cards)
    {
        $list = [];
        foreach ($cards as $card) {
            $list[] = $card['number'];
        }

        return $list;
    }

    /**
     * 手札の絵柄のみを返す
     *
     * @param array $cards
     *
     * @return array
     */
    private function getMarkList($cards)
    {
        $list = [];
        foreach ($cards as $card) {
            $list[] = $card['mark'];
        }

        return $list;
    }

    /**
     * 役   : ロイヤルストレート・フラッシュ
     * 条件 : 同種札かつ10->11(J)->12(Q)->13(K)->1(A)
     *
     * @param array $cards
     *
     * @return bool
     */
    private function isRoyalStraightFlush($cards)
    {
        $list = $this->getNumberList($cards);
        return $list === [1, 10, 11, 12, 13] && $this->isFlush($cards);
    }


    /**
     * 役   : ストレート・フラッシュ
     * 条件 : ストレートかつフラッシュ
     *
     * @param array $cards
     *
     * @return bool
     */
    private function isStraightFlush($cards)
    {
        return $this->isStraight($cards) && $this->isFlush($cards);
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
        $list = $this->getMarkList($cards);
        return count(array_unique($list)) === 1;
    }

    /**
     * 役   : ストレート
     * 条件 : 5枚のカードの数字が続いている
     *        これはOK, 1(A) -> 2 -> 3 -> 4 -> 5, 10 -> 11(J) -> 12(Q) -> 13(K) -> 1(A)
     *        これはNG,  11(J) -> 12(Q) -> 13(K) -> 1(A) -> 2
     *
     * @param array $cards
     *
     * @return bool
     */
    private function isStraight($cards)
    {
        $list = $this->getNumberList($cards);

        // 最初が1(A)の場合
        if ($list[0] === 1 && $list === [1, 10, 11, 12, 13]) {
            return true;
        }

        $result = true;
        for ($i = 0; $i < 4; $i++) {
            if ( ($list[$i+1] - $list[$i]) !== 1) {
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
