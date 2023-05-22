<?php


class PgnGameParser{

    private $pgnGame;
    private $moveBuilder;
    private $defaultFen = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';

    private $gameData = array();

    private $specialMetadata = array(
        'event','site','white','black','result','plycount','eco','fen',
        'timecontrol','round','date','annotator','termination'
    );

    public function __construct($pgnGame = null){
        if(isset($pgnGame)){
            $this->pgnGame = trim($pgnGame);
            $this->moveBuilder = new MoveBuilder();
        }
    }

    public function setPgn($pgnGame){
        $this->pgnGame = trim($pgnGame);
        $this->gameData = array();
        $this->moveBuilder = new MoveBuilder();
    }

    public function getParsedData(){
        $this->gameData = $this->getMetadata();
        $this->gameData[CHESS_JSON::MOVE_MOVES] = $this->getMoves();
        return $this->gameData;
    }

    private function getMetadata(){
        $ret = array(
            CHESS_JSON::GAME_METADATA=>array()
        );
        // TODO set lastmoves property by reading last 3-4 moves in moves array
        $lines = explode("\n", $this->pgnGame);
        foreach($lines as $line){
            $line = trim($line);
            if(substr($line, 0, 1) === '[' && substr($line, strlen($line)-1, 1) === ']'){
                $metadata = $this->getMetadataKeyAndValue($line);
                if(in_array($metadata['key'], $this->specialMetadata)){
                    $ret[$metadata['key']] = $metadata['value'];
                }else{
                    $ret[CHESS_JSON::GAME_METADATA][$metadata['key']] = $metadata['value'];
                }
            }
        }
        if(!isset($ret[CHESS_JSON::FEN])) {
            $ret[CHESS_JSON::FEN] = $this->defaultFen;
        }

        return $ret;
    }

    private function getMetadataKeyAndValue($metadataString){
        $metadataString = preg_replace("/[\[\]]/s", "", $metadataString);
        $metadataString = str_replace('"', '', $metadataString);
        $tokens = explode(" ", $metadataString);

        $key = $tokens[0];
        $value = implode(" ", array_slice($tokens, 1));
        $ret = array('key' => $this->getValidKey($key),  'value' => $value );
        return $ret;
    }

    private function getValidKey($key){
        $key = strtolower($key);
        return $key;
    }

    private function getMoves(){
        $parts = $this->getMovesAndComments();
        for($i=0, $count = count($parts); $i<$count; $i++){
            $move = trim($parts[$i]);

            switch($move){
                case '{':
                    if($i==0){
                        $this->moveBuilder->addCommentBeforeFirstMove($parts[$i+1]);
                    }else{
                        $this->moveBuilder->addComment($parts[$i+1]);
                    }
                    $i+=2;
                    break;
                default:
                    $moves = $this->getMovesAndVariationFromString($move);
                    foreach($moves as $move){
                        switch($move){
                            case '(':
                                $this->moveBuilder->startVariation();
                                break;
                            case ')':
                                $this->moveBuilder->endVariation();
                                break;
                            default:
                                $this->moveBuilder->addMoves($move);
                        }
                    }
            }
        }


        return $this->moveBuilder->getMoves();
    }

    private function addGameComment($comment){
        $this->gameData[CHESS_JSON::GAME_METADATA][CHESS_JSON::MOVE_COMMENT] = $comment;
    }

    private function getMovesAndComments(){
        $ret = preg_split("/({|})/s", $this->getMoveString(), 0, PREG_SPLIT_DELIM_CAPTURE);
        if(!$ret[0]){
            $ret = array_slice($ret, 1);
        }
        return $ret;
    }

    private function getMovesAndVariationFromString($string){
        $string = " ". $string;

        $string = preg_replace("/[0-9]+?\./s", "", $string);
        $string = str_replace(" ..", "", $string);
        $string = str_replace("  ", " ", $string);
        $string = trim($string);

        return preg_split("/(\(|\))/s", $string, 0, PREG_SPLIT_DELIM_CAPTURE);
    }

    private function getMoveString() {
        $tokens = preg_split("/\]\n\n/s", $this->pgnGame);
        if(count($tokens) < 2){
            return "";
        }
        $gameData = $tokens[1];
        $gameData = str_replace("\n", " ", $gameData);
        $gameData = preg_replace("/(\s+)/", " ", $gameData);
        return trim($gameData);
    }
}
