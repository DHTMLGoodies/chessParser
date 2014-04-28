<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alf Magne
 * Date: 03.11.12
 * Time: 19:50
 *
 */

require_once(__DIR__."/../../autoload.php");


class ParserTest extends PHPUnit_Framework_TestCase
{

    private function getNumericSquare($square)
    {
        return isset(Board0x88Config::$mapping[$square]) ? Board0x88Config::$mapping[$square] : null;
    }

    /**
     * @test
     */
    /**
     * @test
     */
    public function shouldCreateParser()
    {
        // given
        $parser = $this->getParser();

        // when
        $pieces = $parser->getPiecesOfAColor('white');

        // then
        $this->assertEquals(16, count($pieces));

        // when
        $pieces = $parser->getPiecesOfAColor('black');

        // then
        $this->assertEquals(16, count($pieces));
    }

    /**
     * @test
     */
    public function shouldFindEnPassantSquare()
    {
        // given
        $fen = '5k2/8/8/3pP3/8/8/8/7K w - d6 0 1';
        $parser = $this->getParser($fen);

        // then
        $this->assertEquals('d6', $parser->getEnPassantSquare());
    }

    /**
     * @test
     */
    public function shouldFindFullMoves()
    {
        // given
        $fen = '5k2/8/8/3pP3/8/8/8/7K w - d6 0 25';
        $parser = $this->getParser($fen);

        // then
        $this->assertEquals('25', $parser->getFullMoves());
    }

    /**
     * @test
     */
    public function shouldFindHalfMoves()
    {
        // given
        $fen = '5k2/8/8/3pP3/8/8/8/7K w - d6 12 25';
        $parser = $this->getParser($fen);

        // then
        $this->assertEquals('12', $parser->getHalfMoves());
    }

    /**
     * @test
     */
    public function shouldDetermineIfTwoSquaresAreOnSameRank()
    {
        // given
        $parser = $this->getParser();

        $this->assertTrue($parser->isOnSameRank($this->getNumericSquare('a1'), $this->getNumericSquare('h1')));
        $this->assertFalse($parser->isOnSameRank($this->getNumericSquare('a1'), $this->getNumericSquare('a2')));
    }

    /**
     * @test
     */
    public function shouldDetermineIfTwoSquaresAreOnSameFile()
    {
        // given
        $parser = $this->getParser();

        $this->assertFalse($parser->isOnSameFile($this->getNumericSquare('a1'), $this->getNumericSquare('h1')));
        $this->assertTrue($parser->isOnSameFile($this->getNumericSquare('a1'), $this->getNumericSquare('a2')));
    }

    /**
     * @test
     */
    public function shouldNotBeAbleToCastleInInvalidPositions(){
        // given
        $fen = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';

        // when
        $parser = $this->getParser($fen);
        $legalMoves = $parser->getValidMovesAndResult("white");
        $moves = $legalMoves['moves'];

        // then
        $this->assertEquals(array(), $moves[4]);


    }

    /**
     * @test
     */
    public function shouldSetCastleCode(){
        // given
        $fen = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
        // when
        $parser = $this->getParser($fen);

        // then
        $this->assertEquals(8+4+2+1, $parser->getCastleCode());
    }

    /**
     * @test
     */
    public function shouldFindCastle()
    {
        // given
        $fen = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
        // when
        $parser = $this->getParser($fen);
        // then



        $this->assertTrue($parser->canCastleKingSide('white') ? true : false, 'Castle options: ' . $parser->getCastleCode());
        $this->assertTrue($parser->canCastleKingSide('black') ? true : false, 'Castle options: ' . $parser->getCastleCode());
        $this->assertTrue($parser->canCastleQueenSide('white') ? true : false, $parser->getCastle());
        $this->assertTrue($parser->canCastleQueenSide('black') ? true : false, $parser->getCastle());

        // given
        $fen = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w Kq - 0 1';
        // when
        $parser = $this->getParser($fen);
        // then
        $this->assertTrue($parser->canCastleKingSide('white') ? true : false);
        $this->assertFalse($parser->canCastleKingSide('black') ? true : false);
        $this->assertFalse($parser->canCastleQueenSide('white') ? true : false);
        $this->assertTrue($parser->canCastleQueenSide('black') ? true : false);
    }

    /**
     * @test
     */
    public function shouldFindColorToMove()
    {
        // given
        $fen = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
        // when
        $parser = $this->getParser($fen);
        // then
        $this->assertEquals('white', $parser->getColor());

    }

    private function getValidMovesForSquare($moves, $square)
    {
        return $moves[Board0x88Config::$mapping[$square]];
    }

    /**
     * @test
     */
    public function shouldFindLegalPawnMoves()
    {

        // given
        $fen = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
        // when
        $parser = $this->getParser($fen);
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];

        $pawnMoves = $this->getValidMovesForSquare($pLegal, 'a2');
        // then
        $this->assertEquals(2, count($pawnMoves));

        // when
        $parser = $this->getParser($fen);
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];

        $pawnMoves = $this->getValidMovesForSquare($pLegal, 'a7');
        // then
        $this->assertEquals(2, count($pawnMoves));

        $parser = $this->getParser('6r1/4pk2/8/8/8/5p2/6P1/6K1 b - - 0 1');
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        $pawnMoves = $this->getValidMovesForSquare($pLegal, 'e7');
        // then
        $this->assertEquals(2, count($pawnMoves));

        $parser = $this->getParser('7k/7p/7P/8/8/8/8/3K2R1 b - - 0 1');
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        $pawnMoves = $this->getValidMovesForSquare($pLegal, 'h7');
        // then
        $this->assertEquals(0, count($pawnMoves));

        $parser = $this->getParser('r1bq1rk1/ppppbppp/2n2n2/4p3/2B1P3/2N2N1P/PPPP1PP1/R1BQ1RK1 b - - 0 1');
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        $pawnMoves = $this->getValidMovesForSquare($pLegal, 'h7');
        // then
        $this->assertEquals(2, count($pawnMoves));

        $parser = $this->getParser('rnbq1rk1/pppp1pp1/5n1p/2b1p3/2BPP3/2P2N2/PP3PPP/RNBQ1RK1 b - - 0 6');
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        $pawnMoves = $this->getValidMovesForSquare($pLegal, 'e5');
        // then
        $this->assertEquals(1, count($pawnMoves));

        $parser = $this->getParser('r1bq3r/ppp3pp/1b6/n2nk3/2B5/B1P2Q2/P2P1PPP/RN4K1 w - - 0 14');
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];
        $pawnMoves = $this->getValidMovesForSquare($pLegal, 'd2');
        $expectedSquares = array('d3', 'd4');

        // then
        $this->assertHasSquares($expectedSquares, $pawnMoves);

        $parser = $this->getParser('6r1/2p1kp1p/p1Bp1p2/bp6/4P3/5bB1/Pp3P1P/R4RK1 b - - 3 20');
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        $pawnMoves = $this->getValidMovesForSquare($pLegal, 'b2');
        $expectedSquares = array('a1', 'b1');

        // then
        $this->assertHasSquares($expectedSquares, $pawnMoves);


    }

    private function assertHasSquares($expectedSquares, $moves)
    {
        $originalMoves = $moves;
        if(is_array($moves))$moves = implode(",",$moves);
        if (strstr($moves, ',')) {
            $newMoves = explode(",", $moves);
            $moves = array();
            foreach($newMoves as $move){
                if(isset($move) && strlen($move)){
                    $moves[] = $move;
                }
            }
        }
        for ($i = 0; $i < count($expectedSquares); $i++) {
            $this->assertTrue($this->isSquareInPaths($expectedSquares[$i], $moves), $expectedSquares[$i] . ' is not in path(' . $this->getReadableSquares($moves)."), expected squares: ". implode(",", $expectedSquares));
        }

        $this->assertEquals(count($expectedSquares), count($moves));
    }


    private function isSquareInPaths($square, $paths)
    {
        for ($i = 0, $count = count($paths); $i < $count; $i++) {
            if (isset($paths[$i]) && $paths[$i] == Board0x88Config::$mapping[$square]) {
                return true;
            }

        }
        return false;
    }

    private function getReadableSquares($squares){
        if(!isset($squares) || !is_array($squares))return $squares;
        $ret = array();
        foreach($squares as $square){
            $ret[] = isset(Board0x88Config::$numberToSquareMapping[$square]) ? $square.":". Board0x88Config::$numberToSquareMapping[$square] : 'Wrong:' . $square;
        }
        return implode(", ", $ret);
    }

    /**
     * @test
     */
    public function shouldFindLegalCapturePawnMoves()
    {
        // given
        $fenWithPawnOnF2AndOpponentPieceOnG3 = '6k1/8/8/8/8/6p1/5P2/6K1 w - - 0 1';
        $parser = $this->getParser($fenWithPawnOnF2AndOpponentPieceOnG3);
        // when
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];
        $pawnMoves = $this->getValidMovesForSquare($pLegal, 'f2');

        // then
        $this->assertEquals(3, count($pawnMoves));

        $this->assertTrue($this->isSquareInPaths('f3', $pawnMoves));

    }

    /**
     * @test
     */
    public function shouldFindLegalBishopMoves()
    {

        // given
        $fenWithBishopOnC2OwnPawnOnB3AndOpponentPieceOnG6 = '6k1/8/6p1/8/8/1P6/2B5/5K2 w - - 0 1';
        $parser = $this->getParser($fenWithBishopOnC2OwnPawnOnB3AndOpponentPieceOnG6);

        // when
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];
        $bishopMoves = $this->getValidMovesForSquare($pLegal, 'c2');

        // then

        $this->assertTrue($this->isSquareInPaths('b1', $bishopMoves));
        $this->assertTrue($this->isSquareInPaths('d1', $bishopMoves));
        $this->assertTrue($this->isSquareInPaths('d3', $bishopMoves));
        $this->assertTrue($this->isSquareInPaths('e4', $bishopMoves));
        $this->assertTrue($this->isSquareInPaths('f5', $bishopMoves));
        $this->assertTrue($this->isSquareInPaths('g6', $bishopMoves));
        $this->assertFalse($this->isSquareInPaths('h7', $bishopMoves));

        # $this->assertEquals(6, $bishopMoves.flatten().length)

    }

    /**
     * @test
     */
    public function shouldFindLegalBlackBishopMoves()
    {

        // given
        $fenWithBishopOnC2OpponentPawnOnB3AndOwnPieceOnG6 = '6k1/8/6p1/8/8/1P6/2b5/5K2 w - - 0 1';
        $parser = $this->getParser($fenWithBishopOnC2OpponentPawnOnB3AndOwnPieceOnG6);

        // when
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        $bishopMoves = $this->getValidMovesForSquare($pLegal, 'c2');

        // then

        $this->assertTrue($this->isSquareInPaths('b1', $bishopMoves));
        $this->assertTrue($this->isSquareInPaths('b3', $bishopMoves));
        $this->assertTrue($this->isSquareInPaths('d1', $bishopMoves));
        $this->assertTrue($this->isSquareInPaths('d3', $bishopMoves));
        $this->assertTrue($this->isSquareInPaths('e4', $bishopMoves));
        $this->assertTrue($this->isSquareInPaths('f5', $bishopMoves));
        $this->assertFalse($this->isSquareInPaths('g6', $bishopMoves));


        #$this->assertEquals(6, $bishopMoves.flatten().length)

    }


    /**
     * @test
     */
    public function shouldFindLegalRookMoves()
    {

        $fenWithRookOnC2BlackOna2g3WhiteOnC6 = '6k1/8/2P5/8/8/8/p1R3p1/6K1 w - - 0 1';
        $parser = $this->getParser($fenWithRookOnC2BlackOna2g3WhiteOnC6);

        // when
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];
        $rookMoves = $this->getValidMovesForSquare($pLegal, 'c2');
        $expectedSquares = array('b2', 'a2', 'd2', 'e2', 'f2', 'g2', 'c1', 'c3', 'c4', 'c5');

        // then
        $this->assertHasSquares($expectedSquares, $rookMoves);
    }

    /**
     * @test
     */
    public function shouldFindLegalBlackRookMoves()
    {

        $fen = '3p2k1/1p1r1p2/8/3P4/8/8/8/6K1 b - - 0 1';
        $parser = $this->getParser($fen);

        // when
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        $rookMoves = $this->getValidMovesForSquare($pLegal, 'd7');
        $expectedSquares = array('c7', 'e7', 'd6', 'd5');

        // then
        $this->assertHasSquares($expectedSquares, $rookMoves);
    }

    /**
     * @test
     */
    public function shouldFindLegalKnightSquares()
    {

        $fen = '6k1/8/8/8/2P1p3/8/3N4/6K1 w - - 0 1';
        $parser = $this->getParser($fen);
        // when
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];
        $knightMoves = $this->getValidMovesForSquare($pLegal, 'd2');
        $expectedSquares = array('b1', 'f1', 'b3', 'f3', 'e4');

        // then
        $this->assertHasSquares($expectedSquares, $knightMoves);

        // given
        $fen = 'rnb1qrk1/ppp3pp/3b4/3pN1BN/3Pp1n1/8/PPPQ1P1P/R3KB1R w KQ - 0 12';
        $parser = $this->getParser($fen);
        // when
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];

        $knightMoves = $this->getValidMovesForSquare($pLegal, 'e5');
        $expectedSquares = array('d7', 'f7', 'g6', 'g4', 'f3', 'd3', 'c6', 'c4');


        // then
        $this->assertHasSquares($expectedSquares, $knightMoves);
    }

    /**
     * @test
     */
    public function shouldFindLegalBlackKnightSquares()
    {

        $fen = '6k1/8/2P5/5p2/3n4/8/2P5/6K1 w - - 0 1';
        $parser = $this->getParser($fen);
        // when
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        $knightMoves = $this->getValidMovesForSquare($pLegal, 'd4');
        $expectedSquares = array('c2', 'e2', 'b3', 'f3', 'b5', 'c6', 'e6');
        // then
        $this->assertHasSquares($expectedSquares, $knightMoves);
    }

    /**
     * @test
     */
    public function shouldFindLegalKingMoves()
    {
        $fen = '5k2/8/8/8/8/8/5P2/6K1 w - - 0 1';
        $parser = $this->getParser($fen);
        // when
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];
        $kingMoves = $this->getValidMovesForSquare($pLegal, 'g1');
        $expectedSquares = array('f1', 'g2', 'h1', 'h2');

        // then
        $this->assertHasSquares($expectedSquares, $kingMoves);

        $fen = 'Rbkq4/1p6/1BP4p/4p3/4B3/1QPP1P2/6rP/6K1 w - - 0 29';
        $parser = $this->getParser($fen);
        // when
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];
        $kingMoves = $this->getValidMovesForSquare($pLegal, 'g1');
        $expectedSquares = array('f1', 'g2', 'h1');

        // then
        $this->assertHasSquares($expectedSquares, $kingMoves);

    }

    /**
     * @test
     */
    public function shouldFindLegalBlackKingMoves()
    {
        $fen = '8/5k2/5p2/8/8/8/5P2/6K1 b - - 0 1';
        $parser = $this->getParser($fen);
        // when
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        $kingMoves = $this->getValidMovesForSquare($pLegal, 'f7');
        $expectedSquares = array('e8', 'e7', 'e6', 'f8', 'g8', 'g7', 'g6');

        // then
        $this->assertHasSquares($expectedSquares, $kingMoves);
    }

    /**
     * @test
     */
    public function shouldFindLegalCastleMoves()
    {
        $fen = '8/5k2/5p2/8/8/8/5P2/R3K2R b KQ - 0 1';
        $parser = $this->getParser($fen);
        // when
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];

        $kingMoves = $this->getValidMovesForSquare($pLegal, 'e1');
        $expectedSquares = array('f1', 'd1', 'e2', 'd2', 'g1', 'c1');

        // then
        $this->assertHasSquares($expectedSquares, $kingMoves, $kingMoves);
    }


    /**
     * @test
     */
    public function shouldFindLegalBlackCastleMoves()
    {
        $fen = 'r3k2r/8/5p2/8/8/8/5P2/R3K2R b KQk - 0 1';
        $parser = $this->getParser($fen);
        // when
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];

        $kingMoves = $this->getValidMovesForSquare($pLegal, 'e8');
        $expectedSquares = array('d8', 'd7', 'e7', 'f8', 'f7', 'g8');

        // then
        $this->assertHasSquares($expectedSquares, $kingMoves);
    }

    /**
     * @test
     */
    public function shouldFindOpponentsCaptureAndProtectiveMoves()
    {
        // given
        $fen = '7k/4b2p/8/8/8/8/8/5K2 w - - 0 1';
        $parser = $this->getParser($fen);
        // when
        $pLegal = $parser->getCaptureAndProtectiveMoves('black');

        $pLegal = explode(",", substr($pLegal, 1, strlen($pLegal) - 2));

        $expectedSquares = 'd6,c5,b4,a3,d8,f8,f6,g5,h4,g6,g8,g7,h7';
        $expectedSquares = explode(",", $expectedSquares);
        // then
        $this->assertHasSquares($expectedSquares, $pLegal);
    }

    /**
     * @test
     */
    public function shouldFindOpponentsCaptureAndProtectiveMovesContinued()
    {
        // given
        $fen = '6k1/8/8/2b5/8/8/5p2/5K2 w - - 0 1';
        $parser = $this->getParser($fen);
        // when
        $pLegal = $parser->getCaptureAndProtectiveMoves('black');


        $expectedSquares = 'e1,g1,b6,a7,b4,a3,d6,e7,f8,d4,e3,f2,f8,f7,g7,h7,h8';
        $expectedSquares = explode(",", $expectedSquares);
        // then
        $this->assertHasSquares($expectedSquares, $pLegal);
    }

    /**
     * @test
     */
    public function shouldExcludeInvalidKingMoves()
    {
        $fen = '6k1/8/8/2b5/8/8/5p2/5K2 w - - 0 1';
        $parser = $this->getParser($fen);
        // when
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];
        $kingMoves = $this->getValidMovesForSquare($pLegal, 'f1');
        $expectedSquares = array('e2', 'g2');
        // then
        $this->assertHasSquares($expectedSquares, $kingMoves);
    }

    /**
     * @test
     */
    public function shouldExcludeInvalidBlackKingMoves()
    {
        $fen = '6k1/5p2/5P2/2B5/8/8/5p2/5K2 b - - 0 1';
        $parser = $this->getParser($fen);
        // when
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        $kingMoves = $this->getValidMovesForSquare($pLegal, 'g8');
        $expectedSquares = array('h7', 'h8');
        // then
        $this->assertHasSquares($expectedSquares, $kingMoves);
    }

    /**
     * @test
     */
    public function shouldFindQueenMoves()
    {
        // given
        $fen = '6k1/6pp/3P2p1/8/8/3Q1P2/8/1P3K2 w - - 0 1';
        $parser = $this->getParser($fen);
        // when
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];
        $queenMoves = $this->getValidMovesForSquare($pLegal, 'd3');
        $expectedSquares = 'c2,d2,d1,e2,d4,d5,c4,b5,a6,e4,f5,g6,c3,b3,a3,e3';
        $expectedSquares = explode(',', $expectedSquares);
        // then
        $this->assertHasSquares($expectedSquares, $queenMoves);
    }


    /**
     * @test
     */
    public function shouldExcludeInvalidKingCastleMoves()
    {
        $fen = '1k4r1/8/3r4/8/8/1b6/4P3/4K2R w K - 0 1';
        $parser = $this->getParser($fen);
        // when
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];
        $kingMoves = $this->getValidMovesForSquare($pLegal, 'e1');
        $expectedSquares = array('f1', 'f2');
        // then
        $this->assertHasSquares($expectedSquares, $kingMoves);
    }

    /**
     * @test
     */
    public function shouldLegalEnPassantMoves()
    {
        $fen = '7k/4b2p/8/3pP3/8/8/8/5K2 w - d6 0 1';
        $parser = $this->getParser($fen);
        // when
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];
        $pawnMoves = $this->getValidMovesForSquare($pLegal, 'e5');
        $expectedSquares = array('d6', 'e6');
        // then
        $this->assertHasSquares($expectedSquares, $pawnMoves);
    }

    /**
     * @test
     */
    public function shouldFindSlidingPiecesInPathOfKing()
    {
        // given
        $fen = '6k1/5pp1/8/8/8/8/BB6/5KR1 w - - 0 1';
        $parser = $this->getParser($fen);
        // when
        $pieces = $parser->getSlidingPiecesAttackingKing('white');

        // then
        $this->assertEquals(2, count($pieces));
        $this->assertEquals(Board0x88Config::$mapping['g1'], $pieces[1]['s']);
        $this->assertEquals(Board0x88Config::$mapping['a2'], $pieces[0]['s']);

        //given
        $fen = '6k1/Q5n1/4p3/8/8/8/B7/5KR1 b - - 0 1';
        $parser = $this->getParser($fen);
        // when
        $pieces = $parser->getSlidingPiecesAttackingKing('white');

        // then
        $this->assertEquals(2, count($pieces));

        // given
        $fen = 'R5k1/8/8/8/8/8/8/5K2 b - - 0 1';
        $parser = $this->getParser($fen);
        // when
        $pieces = $parser->getSlidingPiecesAttackingKing('white');

        // then
        $this->assertEquals(1, count($pieces));
        $this->assertEquals(1, $pieces[0]['p']);
    }

    /**
     * @test
     */
    public function shouldFindCheckPositions()
    {
        // given
        $fen = '6k1/6pp/5p2/8/8/8/B7/6K1 b - - 0 1';
        $parser = $this->getParser($fen);

        $moves = $parser->getCaptureAndProtectiveMoves('white');

        $this->assertEquals(1, $parser->getCountChecks('black', $moves));
    }

    /**
     * @test
     */
    public function shouldFindDoubleChecks()
    {
        // given
        $fen = '3R2k1/6pp/5p2/8/8/8/B7/6K1 b - - 0 1';
        $parser = $this->getParser($fen);

        $moves = $parser->getCaptureAndProtectiveMoves('white');

        $this->assertEquals(2, $parser->getCountChecks('black', $moves));
    }

    public function OnlyKingShouldbeAbleToMoveOnDoubleCheck()
    {

        // given
        $fen = '3R2k1/6p1/5p1p/8/8/8/B7/6K1 b - - 0 1';
        $parser = $this->getParser($fen);
        // when
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];

        $pawnMoves = $this->getValidMovesForSquare($pLegal, 'g8');
        $expectedSquares = array('h7');
        // then
        $this->assertHasSquares($expectedSquares, $pawnMoves);

    }

    /**
     * @test
     */
    public function AttackingMovesShouldIncludeSquaresAfterKing()
    {
        // given
        $fen = '3R2k1/6p1/5p1p/8/8/8/B7/6K1 b - - 0 1';
        $parser = $this->getParser($fen);

        // when
        $moves = $parser->getCaptureAndProtectiveMoves('white');
        if(!is_array($moves))$moves = explode(",", $moves);
        $this->assertTrue(array_search(Board0x88Config::$mapping['h8'], $moves) >= 0);
    }

    /**
     * @test
     */
    public function shouldbeAbleToFindDistanceBetweenTwoSquares()
    {
        // given
        $parser = $this->getParser();
        // when
        $square2 = $this->getNumericSquare('e1');
        $square1 = $this->getNumericSquare('f3');
        // then
        $this->assertEquals(2, $parser->getDistance($square1, $square2));
        // when
        $square2 = $this->getNumericSquare('h5');
        $square1 = $this->getNumericSquare('b1');
        // then
        $this->assertEquals(6, $parser->getDistance($square1, $square2));

        // when
        $square2 = $this->getNumericSquare('a1');
        $square1 = $this->getNumericSquare('b2');
        $this->assertEquals(1, $parser->getDistance($square1, $square2));

        // when
        $square2 = $this->getNumericSquare('b6');
        $square1 = $this->getNumericSquare('e1');
        // then
        $this->assertEquals(5, $parser->getDistance($square1, $square2),'a6 vs e1');
        // when
        $square2 = $this->getNumericSquare('f3');
        $square1 = $this->getNumericSquare('e1');
        // then
        $this->assertEquals(2, $parser->getDistance($square1, $square2),'f3 vs e1');
        // when
        $square2 = $this->getNumericSquare('a1');
        $square1 = $this->getNumericSquare('h8');
        // then
        $this->assertEquals(7, $parser->getDistance($square1, $square2));
        // when
        $square2 = $this->getNumericSquare('h1');
        $square1 = $this->getNumericSquare('a8');
        // then
        $this->assertEquals(7, $parser->getDistance($square1, $square2));

        $square1 = $this->getNumericSquare('a1');
        for ($i = 2; $i <= 8; $i++) {
            $square2 = $this->getNumericSquare('a' . $i);
            $this->assertEquals($i - 1, $parser->getDistance($square1, $square2), 'a' . $i);
        }
        $square1 = $this->getNumericSquare('a1');
        for ($i = 2; $i <= 8; $i++) {
            $square2 = $this->getNumericSquare('b' . $i);
            $this->assertEquals($i - 1, $parser->getDistance($square1, $square2), 'b' . $i);
        }
        $square1 = $this->getNumericSquare('a8');
        for ($i = 7; $i >= 1; $i--) {
            $square2 = $this->getNumericSquare('b' . $i);
            $this->assertEquals(8 - $i, $parser->getDistance($square1, $square2), 'b' . $i);
        }
    }

    private function assertSquareIsPinnedBy($square, $pinnedBy, $pinned)
    {
        $this->assertEquals($this->getNumericSquare($pinnedBy), $pinned[$this->getNumericSquare($square)]['by']);
    }

    /**
     * @test
     */
    public function shouldFindPinningPieces()
    {
        // given
        $fen = '6k1/Q5n1/4p3/8/8/1B6/B7/5KR1 b - - 0 1';
        $parser = $this->getParser($fen);

        // when
        $pinned = $parser->getPinned('black');

        // then
        $this->assertSquareIsPinnedBy('e6', 'b3', $pinned);
        $this->assertSquareIsPinnedBy('g7', 'g1', $pinned);

    }


    public function KnightShouldNotbeableToMoveWhenPinned()
    {
        // given
        $fen = '6k1/6p1/4n3/8/8/8/B7/6K1 b - - 0 1';
        $parser = $this->getParser($fen);

        // when
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        $knightMoves = $this->getValidMovesForSquare($pLegal, 'e6');
        $expectedSquares = array();
        // then
        $this->assertHasSquares($expectedSquares, $knightMoves);

    }

    public function PawnShouldNotbeAbleToMoveWhenPinnedByRook()
    {
        // given
        $fenPawnOnG2KingOnH2BlackRookOnA2 = '5k2/8/8/8/8/8/r5PK/8 w - - 0 1';
        $parser = $this->getParser($fenPawnOnG2KingOnH2BlackRookOnA2);
        $pinned = $parser->getPinned('white');

        // then
        $this->assertSquareIsPinnedBy('g2', 'a2', $pinned);
        // when
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];
        $pawnMoves = $this->getValidMovesForSquare($pLegal, 'g2');
        $expectedSquares = array();
        // then
        $this->assertHasSquares($expectedSquares, $pawnMoves);

        // when
        $fen = '5kr1/8/8/8/8/5p2/6P1/6K1 w - - 0 1';
        $parser = $this->getParser($fen);
        $validMoves = $parser->getValidMovesAndResult('white');
        $pLegal = $validMoves['moves'];
        $pawnMoves = $this->getValidMovesForSquare($pLegal, 'g2');
        $expectedSquares = array('g3', 'g4');
        // then
        $this->assertHasSquares($expectedSquares, $pawnMoves);

        // when
        $fen = '6r1/R3pk2/8/8/8/5p2/6P1/6K1 b - - 0 1';
        $parser = $this->getParser($fen);
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        $pawnMoves = $this->getValidMovesForSquare($pLegal, 'e7');
        $expectedSquares = array();
        // then
        $this->assertEquals(0, count($pawnMoves));
        $this->assertHasSquares($expectedSquares, $pawnMoves);

        // when
        $fen = '4k1r1/4p3/3P4/8/8/5p2/6P1/4R1K1 b - - 0 1';
        $parser = $this->getParser($fen);
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        $pawnMoves = $this->getValidMovesForSquare($pLegal, 'e7');
        $expectedSquares = array('e6', 'e5');
        // then
        $this->assertEquals(2, count($pawnMoves));
        $this->assertHasSquares($expectedSquares, $pawnMoves);
    }

    public function PinnedBishopSlidingPiecesShouldOnlybeAbleToBetweenPinningAndKing()
    {
        // given
        $fenBishopA2AndE6KingOng8 = '6k1/8/4b3/8/8/8/B7/6K1 b - - 0 1';
        $parser = $this->getParser($fenBishopA2AndE6KingOng8);
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        // when
        $bishopMoves = $this->getValidMovesForSquare($pLegal, 'e6');
        $expectedSquares = array('d5', 'c4', 'b3', 'a2', 'f7');
        // then
        $this->assertHasSquares($expectedSquares, $bishopMoves);

    }

    public function PinnedRookSlidingPiecesShouldOnlybeAbleToBetweenPinningAndKing()
    {
        // given
        $fenRookOnE5AndE2KingOnE8 = '4k3/8/8/4r3/8/8/4R3/6K1 b - - 0 1';
        $parser = $this->getParser($fenRookOnE5AndE2KingOnE8);
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        // when
        $rookMoves = $this->getValidMovesForSquare($pLegal, 'e5');
        $expectedSquares = array('e4', 'e3', 'e2', 'e6', 'e7');
        // then
        $this->assertHasSquares($expectedSquares, $rookMoves);
    }

    /**
     * @test
     */
    public function shouldFindPawnCheckMoves()
    {
        // given
        $fenPawnOnE6CheckingKingOnF7 = '8/rn3k2/1b2P3/8/8/8/1QN5/2BRK3 b - - 0 1';
        $parser = $this->getParser($fenPawnOnE6CheckingKingOnF7);
        // when
        $checks = $parser->getValidSquaresOnCheck('black');

        // then
        $this->assertEquals(1, count($checks));
        $this->assertEquals($this->getNumericSquare('e6'), $checks[0]);
    }

    /**
     * @test
     */
    public function shouldFindBlackPawnCheckMoves()
    {
        // given
        $fenPawnOng2CheckingKingOng1 = '5k2/8/8/8/8/8/5p2/6K1 w - - 0 1';
        $parser = $this->getParser($fenPawnOng2CheckingKingOng1);
        // when
        $checks = $parser->getValidSquaresOnCheck('white');

        // then
        $this->assertEquals(1, count($checks));
        $this->assertEquals($this->getNumericSquare('f2'), $checks[0]);
    }

    /**
     * @test
     */
    public function shouldFindValidSquaresWhenCheckedByKnight()
    {
        // given
        $fenKnightOnF6CheckingKingOnG8 = '5rk1/5pp1/5N2/8/8/8/8/5KR1 b - - 0 1';
        $parser = $this->getParser($fenKnightOnF6CheckingKingOnG8);
        // when
        $checks = $parser->getValidSquaresOnCheck('black');

        // then
        $this->assertEquals(1, count($checks));
        $this->assertEquals($this->getNumericSquare('f6'), $checks[0]);

    }

    /**
     * @test
     */
    public function shouldFindValidSquaresWhenCheckedByBishop()
    {
        // given
        $fenBishopOnB3CheckingKingOnG7 = '6k1/6pp/8/8/8/1B6/8/6K1 b - - 0 1';
        $parser = $this->getParser($fenBishopOnB3CheckingKingOnG7);

        $blackKing = $parser->getKing('black');
        $this->assertEquals(Board0x88Config::$mapping['g8'], $blackKing['s']);
        $sq = Board0x88Config::$mapping['b3'];
        $bishop = $parser->getPieceOnSquare($sq);
        $bishopCheckPaths = $parser->getBishopCheckPath($bishop, $blackKing);


        $this->assertTrue(($blackKing['s'] - $bishop['s']) % 17 === 0);
        $this->assertEquals(5, $parser->getDistance($bishop['s'], $blackKing['s']));
        $this->assertEquals(17, ($blackKing['s'] - $bishop['s']) / $parser->getDistance($bishop['s'], $blackKing['s']));
        $this->assertEquals(5, count($bishopCheckPaths), 'bishop:' . json_encode($bishop, true).", king: ". json_encode($blackKing, true));
        // when
        $checks = $parser->getValidSquaresOnCheck('black');
        $expectedSquares = array('b3', 'c4', 'd5', 'e6', 'f7');
        // then
        $this->assertEquals(5, count($checks), 'invalid length for ' . isset($checks) && is_array($checks) ? implode(',', $checks) : $checks);
        $this->assertHasSquares($expectedSquares, $checks);
    }

    /**
     * @test
     */
    public function shouldFindValidSquaresWhenCheckedByRook()
    {
        // given
        $fenRookOnF3CheckingKingOnF8 = '5kb1/4p3/3p4/2p5/1p6/p4R2/8/7K b - - 0 1';
        $parser = $this->getParser($fenRookOnF3CheckingKingOnF8);
        // when
        $checks = $parser->getValidSquaresOnCheck('black');
        $expectedSquares = array('f3', 'f4', 'f5', 'f6', 'f7');
        // then

        $this->assertHasSquares($expectedSquares, $checks);
    }

    /**
     * @test
     */
    public function shouldFindValidSquaresWhenCheckedByRookOnSameRank()
    {
        // given
        $fenRookOnA8CheckingKingOnF8 = 'R4kb1/4p3/3p4/2p5/1p6/p7/8/7K b - - 0 1';
        $parser = $this->getParser($fenRookOnA8CheckingKingOnF8);
        // when
        $checks = $parser->getValidSquaresOnCheck('black');
        $expectedSquares = array('a8', 'b8', 'c8', 'd8', 'e8');
        // then
        $this->assertHasSquares($expectedSquares, $checks);
    }

    /**
     * @test
     */
    public function shouldFindValidSquaresWhenCheckedByQueen()
    {
        // given
        $fenQueenOnF3CheckingKingOnF8 = '5kb1/4p3/3p4/2p5/1p6/p4Q2/8/7K b - - 0 1';
        $parser = $this->getParser($fenQueenOnF3CheckingKingOnF8);
        // when
        $checks = $parser->getValidSquaresOnCheck('black');
        $expectedSquares = array('f3', 'f4', 'f5', 'f6', 'f7');
        // then

        $this->assertHasSquares($expectedSquares, $checks);


        // given
        $fenQueenOnB3CheckingKingOnG7 = '6k1/6pp/8/8/8/1Q6/8/6K1 b - - 0 1';
        $parser = $this->getParser($fenQueenOnB3CheckingKingOnG7);
        // when
        $checks = $parser->getValidSquaresOnCheck('black');
        $expectedSquares = array('b3', 'c4', 'd5', 'e6', 'f7');
        // then

        $this->assertHasSquares($expectedSquares, $checks);

    }

    public function PieceShouldOnlybeableToMoveToValidSquaresOnCheck()
    {
        // given
        // Queen on f3 checkign king on f8
        // Bishop on g8 shouldonly beAbleto$move to f7
        $fen = '5kb1/4p3/3p4/2p5/1p6/p4Q2/8/7K b - - 0 1';
        $parser = $this->getParser($fen);
        $validMoves = $parser->getValidMovesAndResult('black');
        $pLegal = $validMoves['moves'];
        // when
        $moves = $this->getValidMovesForSquare($pLegal, 'g8');
        $expectedSquares = array('f7');
        // then
        $this->assertHasSquares($expectedSquares, $moves);


    }

    /**
     * @test
     */
    public function shouldFindCheckMate()
    {
        // given
        $notCheckMateFens = array('4k3/5p2/2B3p1/8/8/8/4R3/5K2 b - - 0 1',
            '5k2/5p1p/6p1/2B5/8/8/4R3/5K2 b - - 0 1');

        for ($i = 0; $i < count($notCheckMateFens); $i++) {
            // when
            $parser = $this->getParser($notCheckMateFens[$i]);
            $moves = $parser->getValidMovesAndResult('black');
            // then
            $this->assertNotEquals(1, $moves['result']);
        }

        // given
        $checkMateFens = array('5kr1/5ppp/8/2B5/8/8/4R3/5K2 b - - 0 1',
            '3qkbn1/2rpp3/8/5n1Q/8/8/8/5K2 b - - 0 1',
            '3qkr2/3ppp2/3N4/8/8/8/8/4R1K1 b - - 0 1',
            '6rk/5Npp/8/8/8/1P6/2PP4/3K4 b - - 0 1',
            '6pk/6p1/8/8/8/8/8/2K4R b - - 0 1',
            '4b1pk/5rpp/6N1/8/8/8/8/2K4R b - - 0 1'

        );

        for ($i = 0; $i < count($checkMateFens); $i++) {
            // when
            $parser = $this->getParser($checkMateFens[$i]);
            $moves = $parser->getValidMovesAndResult();

            $color = $parser->getColor();
            $protectiveMoves = $parser->getCaptureAndProtectiveMoves($color);

            // then
            $this->assertEquals(1, $moves['result'], 'Position('.$i.'): ' . $checkMateFens[$i] . ', moves: ' . json_encode($protectiveMoves,true));
        }
    }

    /**
     * @test
     */
    public function shouldFindStalemate()
    {
        // given
        $stalematePos = array('7k/7p/7P/8/8/8/8/3K2R1 b - - 0 1',
            '1R4bk/6pp/7P/8/8/8/1B6/5K2 b - - 0 1');
        // when
        for ($i = 0; $i < count($stalematePos); $i++) {
            $parser = $this->getParser($stalematePos[$i]);
            $moves = $parser->getValidMovesAndResult();
            $this->assertEquals(.5, $moves['result'],$stalematePos[$i]);
        }
    }

    /**
     * @test
     */
    public function shouldGetFenForAMove()
    {
        // given
        $fen = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
        $parser = $this->getParser($fen);

        // when
        $parser->move(array('from' => 'e2', 'to' => 'e4'));
        $newFen = $parser->getFen();
        $expectedFen = 'rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq - 0 1';
        // then

        $this->assertEquals($expectedFen, $newFen);
    }
    /**
     * @test
     */
    public function ShouldGetFenForAPromotionMove()
    {
        // given
        $fen = '7k/2P3p1/7p/8/8/4p3/8/7K w - - 0 1';

        $parser = $this->getParser($fen);

        // when
        $parser->move(array('from' => 'c7', 'to' => 'c8', 'promoteTo' => 'queen'));
        $newFen = $parser->getFen();
        $expectedFen = '2Q4k/6p1/7p/8/8/4p3/8/7K b - - 0 1';
        // then
        $this->assertEquals($expectedFen, $newFen);
        // given
        $fen = '7k/8/8/8/8/8/2p5/7K b - - 0 1';

        $parser = $this->getParser($fen);
        // when
        $parser->move(array('from' => 'c2', 'to' => 'c1', 'promoteTo' => 'queen'));
        $newFen = $parser->getFen(array('from' => 'c2', 'to' => 'c1', 'promoteTo' => 'queen'));
        $expectedFen = '7k/8/8/8/8/8/8/2q4K w - - 0 2';
        // then
        $this->assertEquals($expectedFen, $newFen);

    }

    public function ShoulludoetFenForEnPassantMoves()
    {

        // given
        $fen = 'rnbqkbnr/1ppppppp/p7/4P3/8/8/PPPP1PPP/RNBQKBNR b KQkq - 0 2';
        $parser = $this->getParser($fen);

        // when
        $parser->move(array('from' => 'd7', 'to' => 'd5'));
        $newFen = $parser->getFen();

        $expectedFen = 'rnbqkbnr/1pp1pppp/p7/3pP3/8/8/PPPP1PPP/RNBQKBNR w KQkq d6 0 3';
        $this->assertEquals($expectedFen, $newFen);
    }

    /**
     * @test
     */
    public function shouldbeAbleToGetFenForCastleMoves()
    {

        // given
        $fen = 'r3k2r/4pppp/8/8/8/8/4PPPP/R3K2R w KQkq - 0 1';
        $parser = $this->getParser($fen);

        // when
        $parser->move(array('from' => 'e1', 'to' => 'g1'));
        $newFen = $parser->getFen();
        $expectedFen = 'r3k2r/4pppp/8/8/8/8/4PPPP/R4RK1 b kq - 1 1';

        $this->assertEquals($expectedFen, $newFen);

        // given
        $fen = '3k4/4p3/5p2/6p1/7p/8/8/R3K3 w Q - 0 1';
        $parser = $this->getParser($fen);
        // when
        $parser->move(array('from' => 'e1', 'to' => 'c1'));
        $newFen = $parser->getFen(array('from' => 'e1', 'to' => 'c1'));
        $expectedFen = '3k4/4p3/5p2/6p1/7p/8/8/2KR4 b - - 1 1';

        $this->assertEquals($expectedFen, $newFen);

    }

    /**
     * @test
     */
    public function shouldbeAbleToGetFenForBlackCastleMoves()
    {

        // given
        $fen = 'r3k2r/6pp/8/8/8/8/8/6K1 b kq - 0 1';
        $parser = $this->getParser($fen);

        // when
        $parser->move(array('from' => 'e8', 'to' => 'c8'));
        $newFen = $parser->getFen(array('from' => 'e8', 'to' => 'c8'));
        $expectedFen = '2kr3r/6pp/8/8/8/8/8/6K1 w - - 1 2';

        $this->assertEquals($expectedFen, $newFen);

        // given
        $fen = 'r3k2r/6pp/8/8/8/8/8/6K1 b kq - 0 1';
        $parser = $this->getParser($fen);
        // when
        $parser->move(array('from' => 'e8', 'to' => 'g8'));
        $newFen = $parser->getFen(array('from' => 'e8', 'to' => 'g8'));
        $expectedFen = 'r4rk1/6pp/8/8/8/8/8/6K1 w - - 1 2';

        $this->assertEquals($expectedFen, $newFen);


    }

    public function ShoulludoetFenForCaptureMoves()
    {
        $fen = '5rk1/4Qppp/8/8/8/1B6/8/5RK1 w - - 0 1';
        $parser = $this->getParser($fen);

        // when
        $parser->move(array('from' => 'b3', 'to' => 'f7'));
        $newFen = $parser->getFen();

        $expectedFen = '5rk1/4QBpp/8/8/8/8/8/5RK1 b - - 0 1';
        $this->assertEquals($expectedFen, $newFen);
    }

    /**
     * @test
     */
    public function shouldIncrementFullMoves()
    {
        // given
        $fen = 'r1bqkbnr/pppp1ppp/2n5/4p3/2B1P3/5N2/PPPP1PPP/RNBQK2R b KQkq - 0 3';
        $parser = $this->getParser($fen);

        // when
        $parser->move(array('from' => 'g8', 'to' => 'f6'));
        $newFen = $parser->getFen();
        $expectedFen = 'r1bqkb1r/pppp1ppp/2n2n2/4p3/2B1P3/5N2/PPPP1PPP/RNBQK2R w KQkq - 1 4';

        // then
        $this->assertEquals($expectedFen, $newFen);
    }

    /**
     * @test
     */
    public function shouldIncrementHalfMoves()
    {
        // given
        $fen = 'r1bqkb1r/pppp1ppp/2n2n2/4p3/2B1P3/2N2N2/PPPP1PPP/R1BQK2R b KQkq - 0 4';
        $parser = $this->getParser($fen);

        // when
        $parser->move(array('from' => 'f8', 'to' => 'c5'));
        $newFen = $parser->getFen();
        $expectedFen = 'r1bqk2r/pppp1ppp/2n2n2/2b1p3/2B1P3/2N2N2/PPPP1PPP/R1BQK2R w KQkq - 1 5';

        // then
        $this->assertEquals($expectedFen, $newFen);

    }

    /**
     * @test
     */
    public function shouldExcludeCastleSquaresWhenMovingKing()
    {
        $fen = '5k2/5p2/6p1/7p/8/8/8/R3K2R w KQ - 0 1';
        $parser = $this->getParser($fen);
        // when
        $parser->move(array('from' => 'e1', 'to' => 'f1'));
        $newFen = $parser->getFen();
        $expectedFen = '5k2/5p2/6p1/7p/8/8/8/R4K1R b - - 1 1';

        // then
        $this->assertEquals($expectedFen, $newFen);

        // given
        $fen = 'r3k2r/1p3p2/p1p1p1p1/3p4/P6P/1P4P1/2P2P2/R3K2R b KQkq - 0 1';
        $parser = $this->getParser($fen);
        // when
        $parser->move(array('from' => 'e8', 'to' => 'e7'));
        $newFen = $parser->getFen();
        $expectedFen = 'r6r/1p2kp2/p1p1p1p1/3p4/P6P/1P4P1/2P2P2/R3K2R w KQ - 1 2';

        // then
        $this->assertEquals($expectedFen, $newFen);
    }

    /**
     * @test
     */
    public function shouldExcludeCastleWhenRookIsMoving()
    {
        // given
        $fen = 'r3k2r/1p3p2/p1p1p1p1/3p4/P6P/1P4P1/2P2P2/R3K2R w KQkq - 0 1';
        $parser = $this->getParser($fen);
        // when
        $parser->move(array('from' => 'a1', 'to' => 'b1'));
        $newFen = $parser->getFen();
        $expectedFen = 'r3k2r/1p3p2/p1p1p1p1/3p4/P6P/1P4P1/2P2P2/1R2K2R b Kkq - 1 1';

        // then
        $this->assertEquals($expectedFen, $newFen);

        // given
        $fen = 'r3k2r/1p3p2/p1p1p1p1/3p4/P6P/1P4P1/2P2P2/R3K2R w KQkq - 0 1';
        $parser = $this->getParser($fen);
        // when
        $parser->move(array('from' => 'h1', 'to' => 'g1'));
        $newFen = $parser->getFen();
        $expectedFen = 'r3k2r/1p3p2/p1p1p1p1/3p4/P6P/1P4P1/2P2P2/R3K1R1 b Qkq - 1 1';

        // then
        $this->assertEquals($expectedFen, $newFen);

        // given
        $fen = 'r3k2r/1p3p2/p1p1p1p1/3p4/P6P/1P4P1/2P2P2/R3K2R b KQkq - 0 1';
        $parser = $this->getParser($fen);
        // when
        $parser->move(array('from' => 'h8', 'to' => 'g8'));
        $newFen = $parser->getFen();
        $expectedFen = 'r3k1r1/1p3p2/p1p1p1p1/3p4/P6P/1P4P1/2P2P2/R3K2R w KQq - 1 2';

        // then
        $this->assertEquals($expectedFen, $newFen);

    }

    /**
     * @test
     */
    public function shouldFindMovedAndRemovedPiecesForAMove()
    {
        // given
        $fen = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
        $parser = $this->getParser($fen);

        // when
        $parser->move(array('from' => 'e2', 'to' => 'e4'));
        $moves = $parser->getPiecesInvolvedInLastMove();

        // then
        $this->assertEquals(1, count($moves));
        $this->assertEquals('e2', $moves[0]['from']);
        $this->assertEquals('e4', $moves[0]['to']);

        // given
        $fen = '7k/8/8/3Pp3/8/8/8/7K w - e6 0 1';
        $parser = $this->getParser($fen);

        // when
        $moves = $parser->getPiecesInvolvedInMove(array('from' => 'd5', 'to' => 'e6'));

        // then
        $this->assertEquals(2, count($moves));
        $this->assertEquals('d5', $moves[0]['from']);
        $this->assertEquals('e6', $moves[0]['to']);
        $this->assertEquals('e5', $moves[1]['capture']);

        // given
        $fen = '8/P6k/8/8/8/8/8/5K2 w - - 0 1';
        $parser = $this->getParser($fen);
        // when
        $moves = $parser->getPiecesInvolvedInMove(array('from' => 'a7', 'to' => 'a8', 'promoteTo' => 'queen'));
        $this->assertEquals(2, count($moves));

    }

    /**
     * @test
     */
    public function shouldFindMovedPiecesForACastleMove()
    {
        // given
        $fen = 'r3k2r/5ppp/8/8/8/1B6/5PPP/R3K2R w KQkq - 0 1';
        $parser = $this->getParser($fen);

        // when
        $moves = $parser->getPiecesInvolvedInMove(array('from' => 'e1', 'to' => 'g1'));
        // then
        $this->assertEquals(2, count($moves));
        $this->assertEquals('e1', $moves[0]['from']);
        $this->assertEquals('g1', $moves[0]['to']);
        $this->assertEquals('h1', $moves[1]['from'], json_encode($moves, true));
        $this->assertEquals('f1', $moves[1]['to']);

    }

    /**
     * @test
     */
    public function shouldFindCorrectNotationForAMove()
    {

        $fens = array(
            'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1',
            'rnbqkbnr/ppp1pppp/8/3pP3/8/8/PPPP1PPP/RNBQKBNR w KQkq d6 0 1',
            '7k/2P2ppp/8/8/8/8/8/7K w - - 0 1',
            '4r2k/6p1/7p/8/8/8/8/4R2K w - - 0 1',
            '6k1/1R3p2/6p1/7p/8/8/5R2/6K1 w - - 0 1',
            '6k1/1R3p2/6p1/7p/8/8/5R2/6K1 w - - 0 1',
            '6k1/5p2/1N4p1/3p3p/1N6/8/5R2/6K1 w - - 0 1',
            '2k5/8/8/8/8/8/8/R3K2R w KQ - 0 1',
            '3k4/4p3/5p2/6p1/7p/8/8/R3K3 w Q - 0 1'

        );
        $moves = array('e2e4', 'e5d6', array('from' => 'c7', 'to' => 'c8', 'promoteTo' => 'queen'), 'e1e8', 'f2f7', 'b7f7', 'b6d5', 'e1g1', 'e1c1');
        $expected = array('e4', 'exd6', 'c8=Q#', 'Rxe8+', 'Rfxf7', 'Rbxf7', 'N6xd5', 'O-O', 'O-O-O+');

        for ($i = 0; $i < count($fens); $i++) {
            $parser = $this->getParser($fens[$i]);
            if (!is_array($moves[$i])) {
                $moves[$i] = array(
                    'from' => substr($moves[$i], 0, 2),
                    'to' => substr($moves[$i], 2, 2)
                );
            }
            $parser->move($moves[$i]);
            $notation = $parser->getNotation();
            $this->assertEquals($expected[$i], $expected[$i], $notation);
        }
    }

    /**
     * @test
     */
    public function shouldFindCorrectLongNotationForAMove()
    {
        $fens = array(
            'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1',
            'rnbqkbnr/ppp1pppp/8/3pP3/8/8/PPPP1PPP/RNBQKBNR w KQkq d6 0 1',
            '7k/2P2ppp/8/8/8/8/8/7K w - - 0 1',
            '4r2k/6p1/7p/8/8/8/8/4R2K w - - 0 1',
            '6k1/1R3p2/6p1/7p/8/8/5R2/6K1 w - - 0 1',
            '6k1/1R3p2/6p1/7p/8/8/5R2/6K1 w - - 0 1',
            '6k1/5p2/1N4p1/3p3p/1N6/8/5R2/6K1 w - - 0 1',
            '2k5/8/8/8/8/8/8/R3K2R w KQ - 0 1',
            '3k4/4p3/5p2/6p1/7p/8/8/R3K3 w Q - 0 1'

        );
        $moves = array('e2e4', 'e5d6', array('from' => 'c7', 'to' => 'c8', 'promoteTo' => 'queen'), 'e1e8', 'f2f7', 'b7f7', 'b6d5', 'e1g1', 'e1c1');
        $expected = array('e2-e4', 'e5xd6', 'c7-c8=Q#', 'Re1xe8+', 'Rf2xf7', 'Rb7xf7', 'Nb6xd5', 'O-O', 'O-O-O+');

        for ($i = 0; $i < count($fens); $i++) {
            $parser = $this->getParser($fens[$i]);
            if (!is_array($moves[$i])) {
                $moves[$i] = array(
                    'from' => substr($moves[$i], 0, 2),
                    'to' => substr($moves[$i], 2, 2)
                );
            }
            $parser->move($moves[$i]);
            $notation = $parser->getLongNotation();
            $this->assertEquals($expected[$i], $expected[$i], $notation);
        }

    }

    /**
     * @test
     */
    public function shouldbeableToMakeSeveralMovesAndThenGetFen()
    {
        // given

        $parser = $this->getParser();

        $parser->move(array('from' => 'e2', 'to' => 'e4'));
        $parser->move(array('from' => 'e7', 'to' => 'e5'));
        $parser->move(array('from' => 'g1', 'to' => 'f3'));
        $parser->move(array('from' => 'g8', 'to' => 'f6'));
        $parser->move(array('from' => 'f1', 'to' => 'c4'));
        $parser->move(array('from' => 'f8', 'to' => 'c5'));
        $parser->move(array('from' => 'e1', 'to' => 'g1'));
        $parser->move(array('from' => 'e8', 'to' => 'g8'));
        $parser->move(array('from' => 'c2', 'to' => 'c3'));
        $parser->move(array('from' => 'h7', 'to' => 'h6'));
        $parser->move(array('from' => 'd2', 'to' => 'd4'));
        $parser->move(array('from' => 'e5', 'to' => 'd4'));
        $parser->move(array('from' => 'c3', 'to' => 'd4'));
        $parser->move(array('from' => 'c5', 'to' => 'b4'));
        $parser->move(array('from' => 'a2', 'to' => 'a3'));
        $parser->move(array('from' => 'b4', 'to' => 'a5'));
        $parser->move(array('from' => 'b2', 'to' => 'b4'));
        $parser->move(array('from' => 'a5', 'to' => 'b6'));
        $parser->move(array('from' => 'f1', 'to' => 'e1'));


        $expectedFen = 'rnbq1rk1/pppp1pp1/1b3n1p/8/1PBPP3/P4N2/5PPP/RNBQR1K1 b - - 2 10';
        $fen = $parser->getFen();

        // then
        $this->assertEquals($expectedFen, $fen);

        // given
        $parser = $this->getParser('3r2k1/pp1r4/1b3Q1P/5B2/8/8/P2p1PK1/8 b - - 3 41');
        $parser->move(array('from' => 'd2', 'to' => 'd1', 'promoteTo' => 'queen'));

        $expectedFen = '3r2k1/pp1r4/1b3Q1P/5B2/8/8/P4PK1/3q4 w - - 0 42';
        $fen = $parser->getFen();
        $this->assertEquals($expectedFen, $fen);
    }


    /**
     * @test
     */
    public function shouldbeableToMakePromotionMove()
    {
        // given
        $fen = '6k1/8/8/8/8/8/1p5P/7K b - - 0 1';
        $parser = $this->getParser($fen);

        // when
        $expectedFen = '6k1/8/8/8/8/8/7P/1q5K w - - 0 2';
        $parser->move(array('from' => 'b2', 'to' => 'b1', 'promoteTo' => 'queen'));

        // then
        $this->assertEquals($expectedFen, $parser->getFen());
    }

    /**
     * @test
     */
    public function shouldbeableToFindFromAndToFromNotation()
    {
        // given
        $parser = $this->getParser();
        $moves = array('e2e4', 'e7e5', 'g1f3', 'g8f6', 'f1c4', 'f8c5', 'e1g1', 'e8g8', 'c2c3', 'h7h6', 'd2d4', 'e5d4', 'c3d4', 'c5b4');
        $notations = array('e4', 'e5', 'Nf3', 'Nf6', 'Bc4', 'Bc5', 'O-O', 'O-O', 'c3', 'h6', 'd4', 'exd4', 'cxd4', 'Bb4');

        for ($i = 0; $i < count($moves); $i++) {
            $move = $parser->getFromAndToByNotation($notations[$i]);
            // Then
            $this->assertEquals(substr($moves[$i], 0, 2), $move['from'], $notations[$i]);
            $this->assertEquals(substr( $moves[$i], 2, 2), $move['to'], $notations[$i]);

            $parser->makeMove(array('from' => substr($moves[$i], 0, 2), 'to' => substr($moves[$i], 2, 2)));

        }

        // Given
        $parser = $this->getParser('6k1/R4p2/6p1/7p/8/8/B4R2/7K w - - 0 1');
        // when
        $move = $parser->getFromAndToByNotation('Rfxf7');
        // then
        $this->assertEquals('f2', $move['from']);
        $this->assertEquals('f7', $move['to']);
        // Given
        $parser = $this->getParser('4nkn1/R4pn1/6p1/7p/8/8/B4R2/7K w - - 0 1');
        // when
        $move = $parser->getFromAndToByNotation('Raxf7#');
        // then
        $this->assertEquals('a7', $move['from']);
        $this->assertEquals('f7', $move['to']);

        // Given
        $parser = $this->getParser('Rbkq4/1p6/1BP4p/4p3/4B3/1QPP1P2/6rP/6K1 w - - 0 29');
        // when
        $move = $parser->getFromAndToByNotation('Kxg2');
        // then
        $this->assertEquals('g1', $move['from']);
        $this->assertEquals('g2', $move['to']);
        // Given
        $parser = $this->getParser('r1bqkb1r/ppp3pp/2n5/3nppN1/2B5/2NP4/PPP2PPP/R1BQ1RK1 b kq - 1 8');
        // when
        $move = $parser->getFromAndToByNotation('Nce7');
        // then
        $this->assertEquals('c6', $move['from']);
        $this->assertEquals('e7', $move['to']);
        // Given
        $parser = $this->getParser('3r2k1/pp1r4/1b3Q1P/5B2/8/8/P2p1PK1/8 b - - 3 41');
        // when
        $move = $parser->getFromAndToByNotation('d1=Q');
        // then
        $this->assertEquals('d2', $move['from']);
        $this->assertEquals('d1', $move['to']);
        $this->assertEquals('queen', $move['promoteTo']);
        // Given
        $parser = $this->getParser('r1bqk1nr/ppp2ppp/1b1p2n1/3PP1N1/2B5/8/P4PPP/RNBQ1RK1 b kq - 2 11');
        // when
        $move = $parser->getFromAndToByNotation('N8e7');
        // then
        $this->assertEquals('g8', $move['from']);
        $this->assertEquals('e7', $move['to']);
        // Given
        $parser = $this->getParser('r1bq3r/ppp3pp/1b6/n2nk3/2B5/B1P2Q2/P2P1PPP/RN4K1 w - - 0 14');
        // when
        $move = $parser->getFromAndToByNotation('d4+');
        // then
        $this->assertEquals('d2', $move['from']);
        $this->assertEquals('d4', $move['to']);


    }

    /**
     * @test
     */
    public function shouldgetpiecetypebynotation()
    {
        $parser = $this->getParser();
        $notations = array('Nf3', 'Nxf6', 'Rxf8=Q', 'Nfxf8', 'O-O', 'exe5', 'Kxg2', 'Nxd6', 'd1=Q');
        $expected = 'NNRNKPKNP';

        for ($i = 0; $i < count($notations); $i++) {
            $expectedValue = substr($expected, $i, 1);
            if ($expectedValue === 'P') {
                $expectedValue = '';
            }
            $this->assertEquals($expectedValue, Board0x88Config::$notationMapping[$parser->getPieceTypeByNotation($notations[$i])]);
        }

    }

    /**
     * @test
     */
    public function shouldFindFromRankByNotation()
    {
        // given
        $parser = $this->getParser();

        // when
        $notations = array('R7e4', 'N5xf5', 'Ne5', 'N8e7');
        $expected = array(6, 4, null, 7);
        for ($i = 0; $i < count($notations); $i++) {
            if ($expected[$i] !== null) {
                $expectedValue = $expected[$i] * 16;
            } else {
                $expectedValue = null;
            }
            $this->assertEquals($expectedValue, $parser->getFromRankByNotation($notations[$i]));

        }

    }

    /**
     * @test
     */
    public function shouldFindFromFileByNotation()
    {
        // given
        $parser = $this->getParser();

        // when
        $notations = array('Ree4', 'Naxf5', 'Ne5', 'exd8=Q', 'axb5', 'Nce7', 'bxa1');
        $expected = array(4, 0, null, 4, 0, 2, 1);
        for ($i = 0; $i < count($notations); $i++) {
            $this->assertEquals($expected[$i], $parser->getFromFileByNotation($notations[$i]), $notations[$i]);
        }
    }

    /**
     * @test
     */
    public function shouldFindFromToSquareByNotation()
    {
        // given
        $parser = $this->getParser();
        // when
        $notations = array('Ree4', 'Naxf5', 'Ne5', 'exd8=Q');
        $expected = array('e4', 'f5', 'e5', 'd8');
        for ($i = 0; $i < count($notations); $i++) {
            $expectedValue = Board0x88Config::$mapping[$expected[$i]];
            $this->assertEquals($expectedValue, $parser->getToSquareByNotation($notations[$i]), $notations[$i]);

        }
    }

    /**
     * @test
     */
    public function should_find_promotion_when_no_equal_sign_in_notation()
    {
        // given
        $parser = $this->getParser();

        $notations = array('a8=R+', 'g8Q', 'axb1=R', 'b8');
        $colors = array('white', 'white', 'black', 'white');
        $expectedResults = array('rook', 'queen', 'rook', '');

        // when
        for ($i = 0; $i < count($notations); $i++) {
            $this->assertEquals($expectedResults[$i], $parser->getPromoteByNotation($notations[$i]), 'Notation ' . $notations[$i] . ' failed ');
        }
    }



    /**
     * @test
     */
    public function shouldBeAbleToCreateGameMoveByMove(){
        // given
        $parser = new FenParser0x88();
        $parser->newGame();
        $parser->move(array('from' => 'e2', 'to' => 'e4'));

        $this->assertEquals('rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq - 0 1', $parser->getFen());

        $parser->move(array('from' => 'e7', 'to' => 'e5'));
        $this->assertEquals('rnbqkbnr/pppp1ppp/8/4p3/4P3/8/PPPP1PPP/RNBQKBNR w KQkq - 0 2', $parser->getFen());

    }
    /**
     * @test
     */
    public function shouldBeAbleToMoveByNotation(){
        $parser = $this->getSpasskyFischerGameWith3FoldReptition();
        $this->assertEquals('8/1p2ppk1/p1np4/6p1/2R1P3/1P4KP/P1R1r1P1/8 b - - 7 45', $parser->getFen());
    }

    /**
     * @test
     */
    public function shouldDetermine3FoldRepetition(){
        // given
        $parser = new FenParser0x88();
        // when
        $fens = $this->getFenFromSpasskyFischer();
        // then
        $this->assertTrue($parser->hasThreeFoldRepetition($fens));

    }

    /**
     * @test
     */
    public function shouldParseProblematicGame(){
        // given

        $pgnParser = new PgnParser("pgn/problematic.pgn");

        // when
        $game = $pgnParser->getFirstGame();

        // then
        $this->assertEquals((36*2)+1, count($game['moves']));
    }
    /**
     * @test
     */
    public function shouldParseProblematicGame2(){
        // given

        $pgnParser = new PgnParser("pgn/problematic.pgn");

        // when
        $game = $pgnParser->getGameByIndex(1);

        // then
        $this->assertEquals(52, count($game['moves']));
    }

    /**
     * @test
     */
    public function shouldParseProblematicGame3(){
        // given

        $pgnParser = new PgnParser("pgn/problematic.pgn");

        // when
        $game = $pgnParser->getGameByIndex(3);

        // then
        $this->assertEquals(82, count($game['moves']));
    }

    /**
     * @test
     */
    public function shouldSplitPgnIntoCorrectGames(){
        // given
        $pgnParser = new PgnParser("pgn/1001-brilliant-checkmates.pgn");
        // when
        $games = $pgnParser->getUnparsedGames();
        // then
        $this->assertEquals(995, count($games));

    }


    private function getSpasskyFischerGameWith3FoldReptition(){
        $parser = $this->getParser();
        $moves = 'e4,d6,d4,g6,Nc3,Nf6,f4,Bg7,Nf3,c5,dxc5,Qa5,Bd3,Qxc5,Qe2,O-O,Be3,Qa5,O-O,Bg4,Rad1,Nc6,Bc4,Nh5,Bb3,Bxc3,bxc3,Qxc3,f5,Nf6,h3,Bxf3,Qxf3,Na5,Rd3,Qc7,Bh6,Nxb3,cxb3,Qc5+,Kh1,Qe5,Bxf8,Rxf8,Re3,Rc8,fxg6,hxg6,Qf4,Qxf4,Rxf4,Nd7,Rf2,Ne5,Kh2,Rc1,Ree2,Nc6,Rc2,Re1,Rfe2,Ra1,Kg3,Kg7,Rcd2,Rf1,Rf2,Re1,Rfe2,Rf1,Re3,a6,Rc3,Re1,Rc4,Rf1,Rdc2,Ra1,Rf2,Re1,Rfc2,g5,Rc1,Re2,R1c2,Re1,Rc1,Re2,R1c2';
        $moves = explode(",", $moves);

        foreach($moves as $move){
            $parser->makeMoveByNotation($move);
        }
        return $parser;
    }
    private function getFenFromSpasskyFischer(){
        $parser = $this->getParser();
        $moves = 'e4,d6,d4,g6,Nc3,Nf6,f4,Bg7,Nf3,c5,dxc5,Qa5,Bd3,Qxc5,Qe2,O-O,Be3,Qa5,O-O,Bg4,Rad1,Nc6,Bc4,Nh5,Bb3,Bxc3,bxc3,Qxc3,f5,Nf6,h3,Bxf3,Qxf3,Na5,Rd3,Qc7,Bh6,Nxb3,cxb3,Qc5+,Kh1,Qe5,Bxf8,Rxf8,Re3,Rc8,fxg6,hxg6,Qf4,Qxf4,Rxf4,Nd7,Rf2,Ne5,Kh2,Rc1,Ree2,Nc6,Rc2,Re1,Rfe2,Ra1,Kg3,Kg7,Rcd2,Rf1,Rf2,Re1,Rfe2,Rf1,Re3,a6,Rc3,Re1,Rc4,Rf1,Rdc2,Ra1,Rf2,Re1,Rfc2,g5,Rc1,Re2,R1c2,Re1,Rc1,Re2,R1c2';
        $moves = explode(",", $moves);
        $ret = array();
        foreach($moves as $move){
            $parser->makeMoveByNotation($move);
            $ret[] = $parser->getFen();
        }
        return $ret;
    }

    /**
     * @param null $fen
     * @return FenParser0x88
     */
    private function getParser($fen = null)
    {
        if (!isset($fen)) {
            $fen = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
        }
        return new FenParser0x88($fen);

    }
}
