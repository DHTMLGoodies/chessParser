<?php

/**
 * Created by IntelliJ IDEA.
 * User: alfmagne1
 * Date: 13/03/2017
 * Time: 19:01
 */

require_once(__DIR__ . "/../autoload.php");

class JSONToPgnTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeAbleToAddGames()
    {
        // given
        $json = $this->gameAsJson();

        $parser = new JsonToPgnParser();
        // when
        $parser->addGame($json);
        $pgn = $parser->asPgn();

        // then
        /*
         * [Event "4th match"]
[Site "London"]
[Date "1834.??.??"]
[Round "62"]
[White "McDonnell,A"]
[Black "De La Bourdonnais,L"]
[Result "0-1"]

1.e4 c5 2.Nf3 Nc6 3.d4 cxd4 4.Nxd4 e5 5.Nxc6 bxc6 6.Bc4 Nf6 7.Bg5 Be7 8.Qe2 d5 9.Bxf6 Bxf6 10.Bb3 O-O 11.O-O a5 12.exd5 cxd5 13.Rd1 d4 14.c4 Qb6 15.Bc2 Bb7 16.Nd2 Rae8 17.Ne4 Bd8 18.c5 Qc6 19.f3 Be7 20.Rac1 f5 21.Qc4+ Kh8 22.Ba4 Qh6 23.Bxe8 fxe4 24.c6 exf3 25.Rc2 Bc8 26.Bd7 Qe3+ 27.Kh1 f2 28.Rf1 d3 29.Rc3 Bxd7 30.cxd7 e4 31.Qc8 Bd8 32.Qc4 Qe1 33.Rc1 d2 34.Qc5 Rg8 35.Rd1 e3 36.Qc3 Qxd1 37.Rxd1 e2 0-1
         */

        $this->assertContains('[Event "4th match"]', $pgn);
        $this->assertContains('[Date "1834.??.??"]', $pgn);
        $this->assertContains('1. e4 c5 2. Nf3 Nc6 3. d4 cxd4', $pgn);

    }

    /**
     * @test
     */
    public function shouldGetAnnotations()
    {
        $parser = new JsonToPgnParser();
        $json = $this->getAnnotated();
        $parser->addGame($json);

        // when
        $pgn = $parser->asPgn();

        // then
        $this->assertContains('{In my preparation to this tournament, I (and probably everybody else) paid the most serious', $pgn);
    }

    /**
     * @test
     */
    public function shouldGetVariations(){

        $parser = new JsonToPgnParser();
        $json = $this->getAnnotated();
        $parser->addGame($json);

        // when
        $pgn = $parser->asPgn();

        // then
        $this->assertContains('(16... h6 17. Be3 {is much better for White, since his rook ', $pgn);


    }

    private function getAnnotated()
    {
        $parser = new PgnParser("pgn/annotated.pgn");
        $games = $parser->getGames();
        return json_encode($games[0]);
    }

    private function gamesAsJson()
    {
        $games = $this->getGames();
        return json_encode($games);
    }

    private function gameAsJson()
    {
        $games = $this->getGames();
        $game = $games[0];
        return json_encode($game);
    }

    private function getGames()
    {
        $parser = new PgnParser("pgn/greatgames.pgn");
        return $parser->getGames();
    }

}