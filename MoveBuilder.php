<?php

class MoveBuilder
{
    private $moves = array();
    private $moveReferences = array();
    private $pointer = 0;
    private $currentIndex = 0;

    public function __construct()
    {
        $this->moveReferences[0] =& $this->moves;
    }

    public function addMoves($moveString)
    {
        $moves = explode(" ", $moveString);
        foreach ($moves as $move) {
            $this->addMove($move);
        }
    }

    private function addMove($move)
    {
        if (!$this->isChessMove($move)) {
            return;
        }
        $move = preg_replace("/^([a-h])([18])([QRNB])$/", "$1$2=$3", $move);
        $this->moveReferences[$this->pointer][] = array(CHESS_JSON::MOVE_NOTATION => $move);
        $this->currentIndex++;
    }

    private function isChessMove($move)
    {
        if ($move == '--') return true;
        return preg_match("/([PNBRQK]?[a-h]?[1-8]?x?[a-h][1-8](?:\=[PNBRQK])?|O(-?O){1,2})[\+#]?(\s*[\!\?]+)?/s", $move);
    }

    public function addCommentBeforeFirstMove($comment)
    {
        $comment = trim($comment);
        if (!strlen($comment)) {
            return;
        }
        $this->moveReferences[$this->pointer][] = array();
        $this->addComment($comment);
    }

    public function addComment($comment)
    {
        $comment = trim($comment);
        if (!strlen($comment)) {
            return;
        }
        #$index = max(0,count($this->moveReferences[$this->pointer])-1);
        $index = count($this->moveReferences[$this->pointer]) - 1;


        if (strstr($comment, '[%clk')) {
            $clk = preg_replace('/\[%clk[^0-9]*?([0-9\:]+?)[\]]/si', '$1', $comment);
            $comment = str_replace('[%clk ' . $clk . ']', '', $comment);
            $this->moveReferences[$this->pointer][$index][CHESS_JSON::MOVE_CLOCK] = $clk;
        }

        $actions = $this->getActions($comment);
        if (!empty($actions)) {
            if (empty($this->moveReferences[$this->pointer][$index][CHESS_JSON::MOVE_ACTIONS])) {
                $this->moveReferences[$this->pointer][$index][CHESS_JSON::MOVE_ACTIONS] = array();
            }
            foreach ($actions as $action) {
                $this->moveReferences[$this->pointer][$index][CHESS_JSON::MOVE_ACTIONS][] = $action;
            }
        }

        $comment = preg_replace('/\[%' . CHESS_JSON::PGN_KEY_ACTION_ARROW . '[^\]]+?\]/si', '', $comment);
        $comment = preg_replace('/\[%' . CHESS_JSON::PGN_KEY_ACTION_CLR_ARROW . '[^\]]+?\]/si', '', $comment);
        $comment = preg_replace('/\[%' . CHESS_JSON::PGN_KEY_ACTION_HIGHLIGHT . '[^\]]+?\]/si', '', $comment);
        $comment = preg_replace('/\[%' . CHESS_JSON::PGN_KEY_ACTION_CLR_HIGHLIGHT . '[^\]]+?\]/si', '', $comment);
        $comment = trim($comment);

        if (empty($comment)) return;

        if ($index === -1) {
            $index = 0;
            $this->moveReferences[$this->pointer][$index][CHESS_JSON::MOVE_COMMENT] = $comment;
            $this->currentIndex++;
        } else {
            $this->moveReferences[$this->pointer][$index][CHESS_JSON::MOVE_COMMENT] = $comment;

        }

    }

    private function getActions($comment)
    {
        $ret = array();
        if (strstr($comment, '[%' . CHESS_JSON::PGN_KEY_ACTION_ARROW)) {
            $arrow = preg_replace('/.*?\[%' . CHESS_JSON::PGN_KEY_ACTION_ARROW . ' ([^\]]+?)\].*/si', '$1', $comment);
            $arrows = explode(",", $arrow);

            foreach ($arrows as $arrow) {
                $tokens = explode(";", $arrow);
                if (strlen($tokens[0]) == 4) {
                    $action = array(
                        "from" => substr($arrow, 0, 2),
                        "to" => substr($arrow, 2, 2)
                    );
                    if (count($tokens) > 1) {
                        $action["color"] = $tokens[1];
                    }
                    $ret[] = $this->toAction("arrow", $action);
                }
            }
        }


        if (strstr($comment, '[%' . CHESS_JSON::PGN_KEY_ACTION_CLR_ARROW)) {
            $arrow = preg_replace('/.*?\[%' . CHESS_JSON::PGN_KEY_ACTION_CLR_ARROW . ' ([^\]]+?)\].*/si', '$1', $comment);
            $arrows = explode(",", $arrow);

            foreach ($arrows as $arrow) {

                $len = strlen($arrow);
                $color = "G";
                if ($len === 5) {
                    $color = substr($arrow, 0, 1);
                    $arrow = substr($arrow, 1);

                }

                if (strlen($arrow) === 4) {
                    $action = array(
                        "from" => substr($arrow, 0, 2),
                        "to" => substr($arrow, 2, 2)
                    );
                    $action["color"] = $color;
                    $ret[] = $this->toAction("arrow", $action);
                }

            }
        }


        if (strstr($comment, '[%' . CHESS_JSON::PGN_KEY_ACTION_HIGHLIGHT)) {
            $arrow = preg_replace('/.*?\[%' . CHESS_JSON::PGN_KEY_ACTION_HIGHLIGHT . ' ([^\]]+?)\].*/si', '$1', $comment);
            $arrows = explode(",", $arrow);

            foreach ($arrows as $arrow) {
                $tokens = explode(";", $arrow);
                if (strlen($tokens[0]) == 2) {
                    $action = array(
                        "square" => substr($arrow, 0, 2)
                    );
                    if (count($tokens) > 1) {
                        $action["color"] = $tokens[1];
                    }
                    $ret[] = $this->toAction("highlight", $action);
                }
            }
        }

        if (strstr($comment, '[%' . CHESS_JSON::PGN_KEY_ACTION_CLR_HIGHLIGHT)) {
            $arrow = preg_replace('/.*?\[%' . CHESS_JSON::PGN_KEY_ACTION_CLR_HIGHLIGHT . ' ([^\]]+?)\].*/si', '$1', $comment);
            $arrows = explode(",", $arrow);

            foreach ($arrows as $arrow) {
                $color = "G";
                if (strlen($arrow) === 3) {
                    $color = substr($arrow, 0, 1);
                    $arrow = substr($arrow, 1);
                }

                if (strlen($arrow) === 2) {

                    $action = array(
                        "square" => substr($arrow, 0, 2)
                    );
                    $action["color"] = $color;
                    $ret[] = $this->toAction("highlight", $action);
                }
            }
        }


        return $ret;
    }

    /**
     * @param string $key
     * @param array $val
     *
     * @return array
     */
    private function toAction($key, $val)
    {
        $val["type"] = $key;
        return $val;
    }

    public function startVariation()
    {
        $index = count($this->moveReferences[$this->pointer]) - 1;
        if (!isset($this->moveReferences[$this->pointer][$index][CHESS_JSON::MOVE_VARIATIONS])) {
            $this->moveReferences[$this->pointer][$index][CHESS_JSON::MOVE_VARIATIONS] = array();
        }
        $countVariations = count($this->moveReferences[$this->pointer][$index][CHESS_JSON::MOVE_VARIATIONS]);
        $this->moveReferences[$this->pointer][$index][CHESS_JSON::MOVE_VARIATIONS][$countVariations] = array();
        $this->moveReferences[] =& $this->moveReferences[$this->pointer][$index][CHESS_JSON::MOVE_VARIATIONS][$countVariations];
        $this->pointer++;
    }

    public function endVariation()
    {
        array_pop($this->moveReferences);
        $this->pointer--;
    }

    public function getMoves()
    {
        return $this->moves;
    }
}
