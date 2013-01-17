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

    private function readPgn(){
        $fh = fopen($this->pgnFile, 'r');
        $this->pgnContent = fread($fh, filesize($this->pgnFile));
        fclose($fh);
    }

    private function cleanPgn(){
        $c = $this->pgnContent;
        $c = preg_replace("/\\$[0-9]+/s", "", $c);
        $c = preg_replace("/{(.*?)\[(.*?)}/s", '{$1-SB-$2}', $c);
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
        if($this->pgnFile){
            $this->readPgn();
        }
        $this->cleanPgn();
        $this->splitPgnIntoGames();
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
        if($this->pgnFile){
            $this->readPgn();
        }
        $this->cleanPgn();
        $this->splitPgnIntoGames();

        for($i=0, $count = count($this->pgnGames);$i<$count; $i++){
            $gameParser = new PgnGameParser($this->pgnGames[$i]);
            $this->games[$i] = $gameParser->getParsedData();


            if(!$this->isLazy()){
                $this->games[$i] = $this->gameParser->getParsedGame($this->games[$i]);
            }
        }
        return $this->games;
    }
}

/*
error_reporting(E_ALL);
ini_set('display_errors','on');

require_once("GameParser.php");
require_once("FenParser0x88.php");
require_once("PgnGameParser.php");
require_once("MoveBuilder.php");
require_once("Board0x88Config.php");
require_once("../CHESS_JSON.php");

$parser = new PgnParser('file.pgn');
$games = $parser->getGames();
echo json_encode($games[0]);
echo "<br><br>";
#outputTime();

*/

