<?php

class PgnParser
{

    private $pgnFile;
    private $pgnContent;
    private $pgnGames;
    private $gameParser;
    private $pgnGameParser;
    private $_fullParsing = true;

    public function __construct($pgnFile = "", $fullParsing =true)
    {
        if ($pgnFile) {
            $this->pgnFile = $pgnFile;
        }
        $this->_fullParsing = $fullParsing;
        $this->gameParser = new GameParser();
        $this->pgnGameParser = new PgnGameParser();
    }



    public function setPgnContent($content)
    {
        $this->pgnContent = $content;
    }

    private function cleanPgn()
    {
        $c = $this->pgnContent;
        $c = preg_replace("/\\$[0-9]+/s", "", $c);
        $c = preg_replace("/{([^\[]*?)\[([^}]?)}/s", '{$1-SB-$2}', $c);
        $c = preg_replace("/\r/s", "", $c);
        $c = preg_replace("/\t/s", "", $c);
        $c = preg_replace("/\]\s+\[/s", "]\n[", $c);
        $c = str_replace(" [", "[", $c);
        $c = preg_replace("/([^\]])(\n+)\[/si", "$1\n\n[", $c);
        $c = preg_replace("/\n{3,}/s", "\n\n", $c);
        $c = str_replace("-SB-", "[", $c);
        return $c;
    }

    public static function getArrayOfGames($pgn)
    {
        return self::getPgnGamesAsArray($pgn);
    }

    private function splitPgnIntoGames($pgnString)
    {
        return $this->getPgnGamesAsArray($pgnString);
    }

    private function getPgnGamesAsArray($pgn)
    {
        $ret = array();
        $content = "\n\n" . $pgn;
        $games = preg_split("/\n\n\[/s", $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        for ($i = 1, $count = count($games); $i < $count; $i++) {
            array_push($ret, trim("[" . $games[$i]));
        }
        return $ret;
    }

    public function getGamesAsJSON()
    {
        return json_encode($this->getGames());
    }

    private function fullParsing()
    {
        return $this->_fullParsing;
    }

    public function getUnparsedGames()
    {
        if (!isset($this->pgnGames)) {
            if ($this->pgnFile && !isset($this->pgnContent)) {
                $this->pgnContent = file_get_contents($this->pgnFile);
            }
            $this->pgnGames = $this->splitPgnIntoGames($this->cleanPgn($this->pgnContent));
        }
        return $this->pgnGames;
    }

    public function getFirstGame()
    {
        return $this->getGameByIndex(0);
    }

    public function getGameByIndex($index){
        $games = $this->getUnparsedGames();
        if (count($games) && count($games) > $index) {
            return $this->getParsedGame($games[$index]);
        }
        return null;
    }

    public function getGames()
    {
        $games = $this->getUnparsedGames();
        $ret = array();
        for ($i = 0, $count = count($games); $i < $count; $i++) {
            $ret[] = $this->getParsedGame($games[$i]);
        }
        return $ret;
    }

    private function getParsedGame($unParsedGame){
        $this->pgnGameParser->setPgn($unParsedGame);
        $ret = $this->pgnGameParser->getParsedData();
        if ($this->fullParsing()) {
            $ret = $this->gameParser->getParsedGame($ret);
        }
        return $ret;
    }
}
