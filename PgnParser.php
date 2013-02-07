<?php

class PgnParser {

    private $pgnFile;
    private $pgnContent;
    private $pgnGames;
    private $games;
    private $gameParser;

    public function __construct($pgnFile = ""){
        if($pgnFile){
            $this->pgnFile = $pgnFile;
        }
        $this->gameParser = new GameParser();
    }

    public function setPgnContent($content){
        $this->pgnContent = $content;
    }

    private function cleanPgn(){
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
        $this->pgnContent = $c;
    }

    public static function getArrayOfGames($pgn) {
        return self::getPgnGamesAsArray($pgn);
    }
    private function splitPgnIntoGames(){
        $this->pgnGames = $this->getPgnGamesAsArray($this->pgnContent);
    }

    private function getPgnGamesAsArray($pgn){
        $ret = array();
        $content = "\n\n" . $pgn;
        $games = preg_split("/\n\n\[/s", $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        for($i=1, $count = count($games); $i<$count; $i++){
            array_push($ret, trim("[". $games[$i]));
        }
        return $ret;
    }

    public function getGamesAsJSON(){
        return json_encode($this->getGames());
    }

    private function isLazy(){
        return false;
    }

    public function getUnparsedGames() {
        if(!isset($this->pgnGames)){
            if($this->pgnFile && !isset($this->pgnContent)){
               $this->pgnContent = file_get_contents($this->pgnFile);
           }
           $this->cleanPgn();
           $this->splitPgnIntoGames();
        }
        return $this->pgnGames;
    }

    public function getFirstGame(){
        $games = $this->getGames();
        if(count($games)){
            return $games[0];
        }
        return null;
    }

    public function getGames(){

        $games = $this->getUnparsedGames();
        $this->games = array();
        for($i=0, $count = count($games);$i<$count; $i++){
            $gameParser = new PgnGameParser($games[$i]);
            $this->games[$i] = $gameParser->getParsedData();

            if(!$this->isLazy()){
                $this->games[$i] = $this->gameParser->getParsedGame($this->games[$i]);
            }
        }
        return $this->games;
    }
}
