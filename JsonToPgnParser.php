<?php

/**
 * Created by IntelliJ IDEA.
 * User: alfmagne1
 * Date: 13/03/2017
 * Time: 19:00
 */
class JsonToPgnParser
{

    private $games;

    public function __construct()
    {
        $this->games = array();
    }

    /**
     * @param string $jsonString
     */
    public function addGame($jsonString)
    {
        $this->addGameObject(json_decode($jsonString, true));
    }

    /**
     * @param array $json
     */
    public function addGameObject($json)
    {
        $this->games[] = $json;
    }


    public function asPgn()
    {
        $ret = array();
        foreach ($this->games as $game) {
            $ret[] = $this->gameToPgn($game);

        }
        return implode("\n\n", $ret);
    }

    private function gameToPgn($game)
    {

        $moves = array();
        $metadata = array();

        foreach ($game as $key => $value) {
            switch ($key) {
                case "moves":
                    $moves = $this->movesToPgn($value, $this->getStartMove($game));
                    break;
                default:
                    if (is_string($value)) {
                        $metadata[] = '[' . ucfirst($key) . ' "' . $value . '"]';
                    }
            }


        }
        return implode("\n", $metadata) . "\n\n" . $moves;

    }

    private function getStartMove($game)
    {
        if (empty($game["fen"])) return 1;
        $tokens = explode(" ", $game["fen"]);
        $ret = array_pop($tokens);
        if ($tokens[1] == "b") $ret += .5;
        return $ret;

    }

    private function movesToPgn($moves, $startMove)
    {
        $ret = array();

        if ($startMove != floor($startMove)) {
            $ret[] = floor($startMove) . "...";
        }

        foreach ($moves as $move) {
            if(!empty($move["m"])){
                if ($startMove == floor($startMove)) {
                    $ret[] = $startMove . ".";
                }
                $ret[] = str_replace("..", "", $move["m"]);
            }
            if (!empty($move["comment"])) {
                $ret[] = '{' . $move["comment"] . "}";
            }

            if(!empty($move["variations"])){
                foreach($move["variations"] as $variation){
                    if(!empty($variation)){
                        $ret[] = "(" . $this->movesToPgn($variation, $startMove);
                    }
                }
            }
            if(!empty($move["m"])) {
                $startMove += .5;
            }
        }

        return implode(" ", $ret);

    }
}