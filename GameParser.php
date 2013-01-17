<?php


class GameParser {

    private $game;
    private $fen;


    public function __construct(){
        $this->moveParser = new FenParser0x88();
    }

    public function getParsedGame($game){
        $this->game = $game;
        $this->fen = $this->getStartFen();

        $this->moveParser->newGame($this->fen);

        $this->parseMoves($this->game[CHESS_JSON::MOVE_MOVES]);
        $this->addParsedProperty();
        return $this->game;
    }

    private function addParsedProperty(){
        $this->game[CHESS_JSON::GAME_METADATA][CHESS_JSON::MOVE_PARSED] = 1;
    }

    private function parseMoves(&$moves){
        foreach($moves as &$move){
            $this->parseAMove($move);
        }
    }

    private function parseAMove(&$move){
        if(!isset($move[CHESS_JSON::MOVE_NOTATION]) || (isset($move[CHESS_JSON::FEN]) && isset($move[CHESS_JSON::MOVE_FROM]) && isset($move[CHESS_JSON::MOVE_TO]))){
            return;
        }

        if(isset($move[CHESS_JSON::MOVE_VARIATIONS])){
            $fen = $this->moveParser->getFen();
            $this->parseVariations($move[CHESS_JSON::MOVE_VARIATIONS]);
            $this->moveParser->setFen($fen);
        }
        $move = $this->moveParser->getParsed($move);
    }

    private function parseVariations(&$variations){
        foreach($variations as &$variation){
            $fen = $this->moveParser->getFen();
            $this->parseMoves($variation);
            $this->moveParser->setFen($fen);
        }
    }

    private function getStartFen(){
        return $this->game[CHESS_JSON::GAME_METADATA][CHESS_JSON::FEN];
    }
}