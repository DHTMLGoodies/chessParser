<?php


class MoveParser {

    const STRICT_VALIDATION = true;

    private static $debugMode = false;
    private $fenParser;
    private $fenBuilder;
    private $squareParser;

    private $pieceTypes = array(
        'K' => 'king',
        'Q' => 'queen',
        'R' => 'rook',
        'B' => 'bishop',
        'N' => 'knight'
    );

    public function __construct(){
        $this->fenParser = new FenParser();
        $this->fenBuilder = new FenBuilder();
        $this->squareParser = new SquareParser();
    }

    public function isCheckMate(){
        $positionParser = new PositionParser($this->getFen());
        return $positionParser->isCheckMate($this->fenParser->getWhoToMove());

    }

    public function getFen(){
        return $this->fenParser->getFen();
    }

    public function newGame($fen){
        $this->emptyCache();
        $this->setFen($fen);

    }

    public function setFen($fen){
        $this->fenParser->setFen($fen);
        $this->fenBuilder->setFen($fen);
    }

    public function getParsed($move){

        $moveProperties = $this->getMoveProperties($move);

        $move[CHESS_JSON::MOVE_FROM] = $moveProperties[CHESS_JSON::MOVE_FROM];
        $move[CHESS_JSON::MOVE_TO] = $moveProperties[CHESS_JSON::MOVE_TO];

        $this->fenBuilder->move($moveProperties);
        $move[CHESS_JSON::FEN] = $this->fenBuilder->getFen();

        $this->fenParser->setFen($move[CHESS_JSON::FEN]);
        return $move;
    }

    public function isValid($move){
        if(!is_array($move)){
            $move = array( CHESS_JSON::MOVE_NOTATION => $move);
        }
        $notation = $this->getNotation($move[CHESS_JSON::MOVE_NOTATION]);
        $success = $this->getMovedPiece($notation, self::STRICT_VALIDATION);

        return $success ? true : false;
    }

    public function emptyCache(){
        $this->fenParser->emptyCache();
    }

    private function getMoveProperties($move){
        $notation = $this->getNotation($move[CHESS_JSON::MOVE_NOTATION]);

        if(isset($move[CHESS_JSON::MOVE_FROM])){
            $movedPiece = $move[CHESS_JSON::MOVE_FROM];
        }else{
            $movedPiece = $this->getMovedPiece($notation);
        }

        if(isset($move[CHESS_JSON::MOVE_TO])){
            $destinationSquare = $move[CHESS_JSON::MOVE_TO];
        }else{
            $destinationSquare = $this->getDestinationSquare($notation);
        }
        $ret = array(
            CHESS_JSON::MOVE_FROM => $movedPiece['square'],
            CHESS_JSON::MOVE_TO => $destinationSquare,
            CHESS_JSON::MOVE_NOTATION => $move[CHESS_JSON::MOVE_NOTATION],
            CHESS_JSON::MOVE_CAPTURE => $this->getCaptureSquare($notation),
            CHESS_JSON::MOVE_PROMOTE_TO => $this->getPromoteTo($notation),
            CHESS_JSON::MOVE_CASTLE => $this->getCastle($notation)
        );
        return $ret;
    }

    private function getNotation($notation){
        return preg_replace("/[!\?+\s]/s", "", $notation);
    }

    private $castleMoves = array('O-O' => 'O-O', 'O-O-O' => 'O-O-O');
    private function getCastle($notation){
        if(isset($this->castleMoves[$notation])){
            return $this->castleMoves[$notation];
        }
        return false;
    }

    private function getCaptureSquare($notation){
        if(strstr($notation, 'x')){
            $square = $this->getDestinationSquare($notation);

            $piece = $this->fenParser->getPieceOnSquare($square);
            if($piece){
                return $square;
            }
            if($square === $this->fenParser->getEnPassantSquare()){
                $ret = substr($square, 0, 1);
                $rank = substr($square, 1,1);
                if($this->fenParser->getWhoToMove() === 'white'){
                    $ret.= $rank-1;
                }else{
                    $ret.= $rank+1;
                }
                return $ret;
            }
        }
        return '';
    }

    private function getPromoteTo($notation){
        if(preg_match("/=[QRBN]/", $notation)){
            $pos = strpos($notation, '=');
            return substr($notation, $pos+1,1);
        }
        return '';
    }

    private function isCaptureMove($notation){
        return strstr($notation, 'x') ? true : false;
    }

    private function getMovedPiece($notation, $strictValidation = false){
        $availablePieces = $this->getAvailablePiecesForMove($notation);

        if($strictValidation){
            $availablePieces = $this->getPiecesAfterStrictValidation($availablePieces, $notation);
        }

        if(count($availablePieces) === 1){
            return $availablePieces[0];
        }

        if(count($availablePieces) > 1){
            $availablePieces = $this->getAvailablePiecesAfterStrippingPaths($availablePieces, $notation);

            if(count($availablePieces) === 1){
                return $availablePieces[0];
            }

            $availablePieces = $this->removePinnedPieces($availablePieces);
            if(count($availablePieces) === 1){
                return $availablePieces[0];
            }

            if(self::$debugMode){
                $pieceType = $this->getPieceTypeFromMove($notation);

                $fromFile = $this->getFromFile($notation);
                $fromRank = $this->getFromRank($notation);

                echo "<h1>To many pieces : $notation - $fromFile | $fromRank</h1>";
                echo $this->fenParser->getFen()."<br>";;

                echo "From file: ". $fromFile."<br>";
                echo "From rank: ". $fromRank."<br>";
                echo "Piece type: ". $this->getPieceTypeFromMove($notation)."<br>";


                outputTime();
                die();
            }

            return false;
        }

        if(count($availablePieces) === 0){
            if(self::$debugMode){
                $fromFile = $this->getFromFile($notation);
                $fromRank = $this->getFromRank($notation);
                echo "<h1>No pieces for move - $notation - ". $this->fenParser->getWhoToMove() . "</h1>";
                echo $this->fenParser->getFen()."<br>";;
                echo "Destination square: ". $this->getDestinationSquare($notation)."<br>";
                echo "Piece type: ". $this->getPieceTypeFromMove($notation)."<br>";
                echo "From file: ". $fromFile."<br>";
                echo "From rank: ". $fromRank."<br>";
                echo "Capture: ". $this->isCaptureMove($notation)."<br>";

                die();
            }
            return false;
        }
    }

    private function getPiecesAfterStrictValidation($availablePieces, $notation){
        $availablePieces = $this->getAvailablePiecesAfterStrippingPaths($availablePieces, $notation);
        $availablePieces = $this->removePinnedPieces($availablePieces);
        $availablePieces = $this->removeInvalidPawnPieces($availablePieces, $notation);
        $availablePieces = $this->removeInvalidKingMoves($availablePieces, $notation);
        return $availablePieces;
    }

    private function removeInvalidPawnPieces($availablePieces, $notation){
        $toSquare = $this->getDestinationSquare($notation);
        $pieceOnSquare = $this->fenParser->getPieceOnSquare($toSquare);
        if(!$pieceOnSquare){
            return $availablePieces;

        }
        $ret = array();
        $file = $toSquare[0];
        foreach($availablePieces as $piece){
            if($piece['type'] === 'pawn'){
                if($piece['square'][0] !== $file){
                    $ret[] = $piece;
                }
            }
        }
        return $ret;
    }

    private function removeInvalidKingMoves($availablePieces, $notation){
        if(count($availablePieces)==0 || $availablePieces[0]['type']!='king'){
            return $availablePieces;
        }
        $toSquare = $this->getDestinationSquare($notation);
        $piece = $availablePieces[0];


        $fenBuilder = new FenBuilder();
        $fenBuilder->setFen($this->getFen());
        $fenBuilder->move(array(
            'from' => $piece['square'],
            'to' => $toSquare,
            'capture' => false,
            'castle' => null,
            'promoteTo' => null,
        ));

        $pieces = $fenBuilder->getPiecesOfAColor($fenBuilder->getWhoToMove());
        $king = $fenBuilder->getKing($this->fenParser->getWhoToMove());

        $moveParser = new MoveParser();
        $moveParser->setFen($fenBuilder->getFen());


        $countPieces = 0;

        foreach($pieces as $piece){
            $not = $this->getShortPieceType($piece) . $piece['square'][0] . $piece['square'][1] . 'x' . $king['square'];

            $fromFile = $this->getFromFile($not);
            $fromRank = $this->getFromRank($not);
              $move = array(
                'm' => $not,
                'from' => $piece['square'],
                'to' => $king['square']
            );

            $tmpPieces = $moveParser->getAvailablePiecesAfterStrippingPaths(array($piece), $not, $king['square']);
            if(count($tmpPieces)){
                return array();
            }
            $countPieces+= count($tmpPieces);
            $isValid = $moveParser->isValid($move);

        }

        return $availablePieces;
    }

    private $pieceTypesShort = array(
        'king' => 'K',
        'queen' => 'Q',
        'rook' => 'R',
        'bishop' => 'B',
        'knight' => 'N',
        'pawn' => ''
    );
    private function getShortPieceType($piece){
        return $this->pieceTypesShort[$piece['type']];
    }

    private function removePinnedPieces($pieces){

        $ret = array();
        foreach($pieces as $piece){
            if(!$this->isPinned($piece)){
                $ret[] = $piece;
            }
        }
        return $ret;
    }

    private function isPinned($piece){

        $topParser = new FenParser("0" . $this->fenParser->getFen());
        $piece = $topParser->getPieceOnSquare($piece['square']);

        $fen = $topParser->getFenAfterRemovingPiece($piece);


        $parser = new FenParser($fen);
        $king = $parser->getKing($piece['color']);


        $otherColor = $parser->getPassiveColor();

        $pieces = $parser->getPiecesOfAColor($otherColor);
        $piecesToCheck = array();
        foreach($pieces as $piece){
            $squares = $this->squareParser->getAllSquares($piece);
            if(in_array($king['square'], $squares)){
                $piecesToCheck[] = $piece;
            }
        }

        foreach($piecesToCheck as $piece){
            // Pawn on same file is not a threat
            if($piece['type'] === 'pawn' && $piece['square'][0] == $king['square'][0]){
                continue;
            }
            $squares = $this->getSquaresOfStrippedPaths($piece, $parser);

            if(in_array($king['square'], $squares)){
                return true;
            }
        }

        return false;
    }

    private function getAvailablePiecesAfterStrippingPaths($pieces, $notation, $toSquare = null){
        if(!$toSquare){
            $toSquare = $this->getDestinationSquare($notation);
        }
        $ret = array();

        $fromFile = $this->getFromFile($notation);
        $fromRank = $this->getFromRank($notation);

        foreach($pieces as $piece){
            if($fromFile && $piece['square'][0] !== $fromFile){
                continue;
            }
            if($fromRank && $piece['square'][1] !== $fromRank){
                continue;
            }

            $squares = $this->getSquaresOfStrippedPaths($piece);
            if(in_array($toSquare, $squares)){
                $ret[] = $piece;
            }
        }
        return $ret;
    }

    private function getAvailablePiecesForMove($notation){
        $ret = array();

        $pieceType = $this->getPieceTypeFromMove($notation);

        $whoToMove = $this->fenParser->getWhoToMove();



        if($pieceType === 'king'){
            return array($this->fenParser->getKing($whoToMove));
        }

        $toSquare = $this->getDestinationSquare($notation);

        $isCapture = $this->isCaptureMove($notation);

        $pieces = $this->fenParser->getColoredPiecesOfAType($whoToMove, $pieceType);


        foreach($pieces as $piece){
            if($piece['type'] === 'pawn' && !$isCapture){
                $squares = $this->squareParser->getSquaresInFirstPath($piece);
            }else{
                $squares = $this->squareParser->getAllSquares($piece);
            }

            if(in_array($toSquare, $squares)){
                $ret[] = $piece;
            }
        }
        return $ret;
    }

    private function getSquaresOfStrippedPaths($piece, $fenParser = null){
        $ret = array();
        $paths = $this->squareParser->getAllPaths($piece);

        if(!$fenParser){
            $fenParser = $this->fenParser;
        }
        foreach($paths as $path){
            for($i=0, $count = count($path); $i<$count; $i++){
                $pieceOnSquare = $fenParser->getPieceOnSquare($path[$i]);
                $ret[] = $path[$i];
                if($pieceOnSquare){
                    break;
                }
            }
        }
        return $ret;
    }

    private static $fromFileCache = array();

    private function getFromFile($notation){
        if(!isset(self::$fromFileCache[$notation])){
            if(preg_match("/.*?[a-h].*[a-h]/s", $notation)){
                self::$fromFileCache[$notation] = preg_replace("/^.*?([a-h]).+?$/s", '$1', $notation);
            }else{
                self::$fromFileCache[$notation] = '';
            }
        }
        return self::$fromFileCache[$notation];
    }

    private static $fromRankCache = array();
    private function getFromRank($notation){
        if(!isset(self::$fromRankCache[$notation])){
            if(preg_match("/.*?[1-8].*[1-8]/s", $notation)){
                self::$fromRankCache[$notation] = preg_replace("/^.*?([1-8]).*?$/s", '$1', $notation);
            }else{
                self::$fromRankCache[$notation] = '';
            }
        }
        return self::$fromRankCache[$notation];
    }

    private function getStartFen(){
        return $this->game['metadata'][CHESS_JSON::FEN];
    }

    private static $pieceTypeCache = array();
    private function getPieceTypeFromMove($move){
        if(!isset(self::$pieceTypeCache[$move])){
            if(strstr($move, 'O-O')){
                self::$pieceTypeCache[$move] = 'king';
            }else{
                $firstChar = substr($move, 0, 1);
                if(isset($this->pieceTypes[$firstChar])){
                    self::$pieceTypeCache[$move] = $this->pieceTypes[$firstChar];
                }else{
                    self::$pieceTypeCache[$move] = 'pawn';
                }
            }
        }
        return self::$pieceTypeCache[$move];
    }

    private static $destinationSquareCache = array();

    private function getDestinationSquare($move){
        $key = $move."_". $this->fenParser->getWhoToMove();
        if(!isset(self::$destinationSquareCache[$key])){
            if(strlen($move) === 2){
                self::$destinationSquareCache[$key] = $move;
            }
            else if($move === 'O-O'){
                self::$destinationSquareCache[$key] = 'g' . ($this->fenParser->getWhoToMove() === 'white' ? '1' : '8');
            }
            else if($move === 'O-O-O'){
                self::$destinationSquareCache[$key] = 'c' . ($this->fenParser->getWhoToMove() === 'white' ? '1' : '8');
            }else{
                self::$destinationSquareCache[$key] = preg_replace("/^.*?([a-h][1-8]).*?$/s", "$1", $move);
            }
        }

        return self::$destinationSquareCache[$key];
    }

}