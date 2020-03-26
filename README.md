# PGN to PHP array parser
PHP class for conversion of Chess PGN files to PHP array or JSON.

This is the chess parser used in DHTML Chess at [dhtml-chess.com]

* License: LGPL (Lesser General Public License).

## Example of use: 

### Import games from PGN file:

```PHP
<?php
$parser = new PgnParser('my-games.pgn');
echo json_encode($parser->getGames());
?>
```

This will give you data in this format:

```JSON
[
    {
        "metadata":{
            "blackelo":"2400",
            "opening":"Grunfeld",
            "time":"10:41:37",
            "variation":"Three Knights Variation",
            "whiteelo":"2400",
            "whitetype":"human",
            "blacktype":"human",
            "castle":1
        },
        "event":"Computer chess game",
        "site":"BORROW-PC",
        "date":"2013.01.19",
        "round":"?",
        "white":"White player",
        "black":"Black player",
        "result":"*",
        "eco":"D90",
        "timecontrol":"300",
        "termination":"unterminated",
        "plycount":"21",
        "fen":"rnbqkbnr\/pppppppp\/8\/8\/8\/8\/PPPPPPPP\/RNBQKBNR w KQkq - 0 1",
        "moves":[
            {
                "m":"e4",
                "from":"e2",
                "to":"e4",
                "fen":"rnbqkbnr\/pppppppp\/8\/8\/4P3\/8\/PPPP1PPP\/RNBQKBNR b KQkq - 0 1"
            },
            {
                "m":"d5",
                "variations":[
                    [
                        {
                            "m":"e5",
                            "comment":"Test comment before",
                            "from":"e7",
                            "to":"e5",
                            "fen":"rnbqkbnr\/pppp1ppp\/8\/4p3\/4P3\/8\/PPPP1PPP\/RNBQKBNR w KQkq - 0 2"
                        },
                        {
                            "m":"Nf3",
                            "comment":"Test comment after",
                            "from":"g1",
                            "to":"f3",
                            "fen":"rnbqkbnr\/pppp1ppp\/8\/4p3\/4P3\/5N2\/PPPP1PPP\/RNBQKB1R b KQkq - 1 2"
                        },
                        {
                            "m":"Nc6",
                            "from":"b8",
                            "to":"c6",
                            "fen":"r1bqkbnr\/pppp1ppp\/2n5\/4p3\/4P3\/5N2\/PPPP1PPP\/RNBQKB1R w KQkq - 2 3"
                        },
                        {
                            "m":"Bc4",
                            "from":"f1",
                            "to":"c4",
                            "fen":"r1bqkbnr\/pppp1ppp\/2n5\/4p3\/2B1P3\/5N2\/PPPP1PPP\/RNBQK2R b KQkq - 3 3"
                        },
                        {
                            "m":"Nf6",
                            "variations":[
                                [
                                    {
                                        "m":"d6",
                                        "from":"d7",
                                        "to":"d6",
                                        "fen":"r1bqkbnr\/ppp2ppp\/2np4\/4p3\/2B1P3\/5N2\/PPPP1PPP\/RNBQK2R w KQkq - 0 4"
                                    },
                                    {
                                        "m":"O-O",
                                        "comment":"(O-O Ng8-f6 Nb1-c3 Nc6-d4 Nf3-g5 d6-d5 e4xd5 h7-h6 Ng5-e4 c7-c6 d5xc6 Nf6xe4 Nc3xe4 b7xc6) +0.48\/11 8",
                                        "from":"e1",
                                        "to":"g1",
                                        "fen":"r1bqkbnr\/ppp2ppp\/2np4\/4p3\/2B1P3\/5N2\/PPPP1PPP\/RNBQ1RK1 b kq - 1 4"
                                    },
                                    {
                                        "m":"Nf6",
                                        "from":"g8",
                                        "to":"f6",
                                        "fen":"r1bqkb1r\/ppp2ppp\/2np1n2\/4p3\/2B1P3\/5N2\/PPPP1PPP\/RNBQ1RK1 w kq - 2 5"
                                    },
                                    {
                                        "m":"Nc3",
                                        "comment":"Last move in variation",
                                        "from":"b1",
                                        "to":"c3",
                                        "fen":"r1bqkb1r\/ppp2ppp\/2np1n2\/4p3\/2B1P3\/2N2N2\/PPPP1PPP\/R1BQ1RK1 b kq - 3 5"
                                    }
                                ]
                            ],
                            "from":"g8",
                            "to":"f6",
                            "fen":"r1bqkb1r\/pppp1ppp\/2n2n2\/4p3\/2B1P3\/5N2\/PPPP1PPP\/RNBQK2R w KQkq - 4 4"
                        },
                        {
                            "m":"O-O",
                            "comment":"Variation ended",
                            "from":"e1",
                            "to":"g1",
                            "fen":"r1bqkb1r\/pppp1ppp\/2n2n2\/4p3\/2B1P3\/5N2\/PPPP1PPP\/RNBQ1RK1 b kq - 5 4"
                        },
                        {
                            "m":"d6",
                            "from":"d7",
                            "to":"d6",
                            "fen":"r1bqkb1r\/ppp2ppp\/2np1n2\/4p3\/2B1P3\/5N2\/PPPP1PPP\/RNBQ1RK1 w kq - 0 5"
                        }
                    ]
                ],
                "from":"d7",
                "to":"d5",
                "fen":"rnbqkbnr\/ppp1pppp\/8\/3p4\/4P3\/8\/PPPP1PPP\/RNBQKBNR w KQkq - 0 2"
            },
            {
                "m":"exd5",
                "comment":"Variation ended",
                "from":"e4",
                "to":"d5",
                "fen":"rnbqkbnr\/ppp1pppp\/8\/3P4\/8\/8\/PPPP1PPP\/RNBQKBNR b KQkq - 0 2"
            },
            {
                "m":"Nf6",
                "from":"g8",
                "to":"f6",
                "fen":"rnbqkb1r\/ppp1pppp\/5n2\/3P4\/8\/8\/PPPP1PPP\/RNBQKBNR w KQkq - 1 3"
            },
            {
                "m":"c4",
                "variations":[
                    [
                        {
                            "m":"Nf3?!",
                            "from":"g1",
                            "to":"f3",
                            "fen":"rnbqkb1r\/ppp1pppp\/5n2\/3P4\/8\/5N2\/PPPP1PPP\/RNBQKB1R b KQkq - 2 3"
                        },
                        {
                            "m":"Qxd5",
                            "from":"d8",
                            "to":"d5",
                            "fen":"rnb1kb1r\/ppp1pppp\/5n2\/3q4\/8\/5N2\/PPPP1PPP\/RNBQKB1R w KQkq - 0 4"
                        },
                        {
                            "m":"Nc3",
                            "from":"b1",
                            "to":"c3",
                            "fen":"rnb1kb1r\/ppp1pppp\/5n2\/3q4\/8\/2N2N2\/PPPP1PPP\/R1BQKB1R b KQkq - 1 4"
                        },
                        {
                            "m":"Qa5",
                            "from":"d5",
                            "to":"a5",
                            "fen":"rnb1kb1r\/ppp1pppp\/5n2\/q7\/8\/2N2N2\/PPPP1PPP\/R1BQKB1R w KQkq - 2 5"
                        },
                        {
                            "m":"d4",
                            "from":"d2",
                            "to":"d4",
                            "fen":"rnb1kb1r\/ppp1pppp\/5n2\/q7\/3P4\/2N2N2\/PPP2PPP\/R1BQKB1R b KQkq - 0 5"
                        }
                    ]
                ],
                "from":"c2",
                "to":"c4",
                "fen":"rnbqkb1r\/ppp1pppp\/5n2\/3P4\/2P5\/8\/PP1P1PPP\/RNBQKBNR b KQkq - 0 3"
            },
            {
                "m":"Bf5",
                "comment":"Variation ended",
                "variations":[
                    [
                        {
                            "m":"Nbd7",
                            "from":"b8",
                            "to":"d7",
                            "fen":"r1bqkb1r\/pppnpppp\/5n2\/3P4\/2P5\/8\/PP1P1PPP\/RNBQKBNR w KQkq - 1 4"
                        },
                        {
                            "m":"Nf3",
                            "from":"g1",
                            "to":"f3",
                            "fen":"r1bqkb1r\/pppnpppp\/5n2\/3P4\/2P5\/5N2\/PP1P1PPP\/RNBQKB1R b KQkq - 2 4"
                        },
                        {
                            "m":"Nb6",
                            "from":"d7",
                            "to":"b6",
                            "fen":"r1bqkb1r\/ppp1pppp\/1n3n2\/3P4\/2P5\/5N2\/PP1P1PPP\/RNBQKB1R w KQkq - 3 5"
                        },
                        {
                            "m":"d4",
                            "from":"d2",
                            "to":"d4",
                            "fen":"r1bqkb1r\/ppp1pppp\/1n3n2\/3P4\/2PP4\/5N2\/PP3PPP\/RNBQKB1R b KQkq d3 0 5"
                        },
                        {
                            "m":"g6",
                            "from":"g7",
                            "to":"g6",
                            "fen":"r1bqkb1r\/ppp1pp1p\/1n3np1\/3P4\/2PP4\/5N2\/PP3PPP\/RNBQKB1R w KQkq - 0 6"
                        },
                        {
                            "m":"Nc3",
                            "from":"b1",
                            "to":"c3",
                            "fen":"r1bqkb1r\/ppp1pp1p\/1n3np1\/3P4\/2PP4\/2N2N2\/PP3PPP\/R1BQKB1R b KQkq - 1 6"
                        },
                        {
                            "m":"Qd6",
                            "from":"d8",
                            "to":"d6",
                            "fen":"r1b1kb1r\/ppp1pp1p\/1n1q1np1\/3P4\/2PP4\/2N2N2\/PP3PPP\/R1BQKB1R w KQkq - 2 7"
                        }
                    ]
                ],
                "from":"c8",
                "to":"f5",
                "fen":"rn1qkb1r\/ppp1pppp\/5n2\/3P1b2\/2P5\/8\/PP1P1PPP\/RNBQKBNR w KQkq - 1 4"
            },
            {
                "m":"Nf3",
                "from":"g1",
                "to":"f3",
                "fen":"rn1qkb1r\/ppp1pppp\/5n2\/3P1b2\/2P5\/5N2\/PP1P1PPP\/RNBQKB1R b KQkq - 2 4"
            }
        ]
    }
]
```


### Create parser from PGN String

```PHP
$pgn = '[Event "Moscow Championship (blitz) 2015"]
[Site "Moscow RUS"]
[Date "2015.09.06"]
[Round "19"]
[White "Morozevich, Alexander"]
[Black "Dubov, Daniil"]
[Result "0-1"]
[ECO "B20"]
[WhiteElo "2711"]
[BlackElo "2661"]
[PlyCount "146"]
[EventDate "2015.09.06"]

1. e4 c5 2. g3 g6 3. Bg2 Bg7 4. d3 Nc6 5. f4 e6 6. Nf3 d5 7. O-O Nf6 8. e5 Nd7
9. c4 Nb6 10. Qe2 O-O 11. Nc3 f6 12. exf6 Bxf6 13. Kh1 Bd7 14. Bd2 Nd4 15. Nxd4
cxd4 16. Nd1 dxc4 17. dxc4 Bc6 18. Bxc6 bxc6 19. Nf2 c5 20. Rae1 Qd7 21. b3
Rfe8 22. Nd3 Rac8 23. Qe4 Qc6 24. g4 Qxe4+ 25. Rxe4 Nd7 26. Be1 h5 27. gxh5
gxh5 28. Rf3 Kh7 29. Bf2 Kg6 30. Rxe6 Kf5 31. Rd6 Nb6 32. Rh3 h4 33. Kg2 Be7
34. Rh6 Ke4 35. Re6+ Kf5 36. Re5+ Kg6 37. Kf3 Nd7 38. Re6+ Kf7 39. f5 Bf6 40.
Bxh4 Rxe6 41. fxe6+ Kxe6 42. Bg3 Rf8 43. Rh5 Bg5+ 44. Kg2 Be3 45. Rd5 Re8 46.
Rd6+ Ke7 47. Ra6 Ra8 48. h4 Nf6 49. Nxc5 Nd7 50. Nd3 Rg8 51. Kf3 Rf8+ 52. Ke2
Rg8 53. Bf4 Bxf4 54. Nxf4 Nc5 55. Rxa7+ Kd6 56. b4 Re8+ 57. Kf3 Re3+ 58. Kg4
Ne4 59. Ra6+ Kd7 60. Ra5 Nf2+ 61. Kf5 d3 62. Rd5+ Kc7 63. h5 Rf3 64. Ke5 Re3+
65. Kf5 Rf3 66. h6 Nh3 67. h7 Rxf4+ 68. Kg6 Rh4 69. Kg7 Nf4 70. Rc5+ Kd6 71.
Rc8 Ne6+ 72. Kf6 d2 73. c5+ Kd7 0-1';

$parser = new PgnParser();
$parser->setPgnContent($pgn);
$game = $parser->getFirstGame();
echo json_encode($game);

```

### Create a game programatically.
This uses the FenParser0x88 class:

```PHP
$parser = new FenParser0x88();
$parser->newGame();
$parser->move("g1f3");
$notation =  $parser->getNotation(); // returns Nf3
$fen = $parser->getFen();
// $fen = rnbqkbnr/pppppppp/8/8/8/5N2/PPPPPPPP/RNBQKB1R b KQkq - 1 1
```

### Get valid moves

```PHP
$parser = new FenParser0x88('6k1/6p1/4n3/8/8/8/B7/6K1 b - - 0 1');
$validBlackMoves = $parser->getValidMovesBoardCoordinates("black");
echo json_encode($validBlackMoves);
```

which outputs

```JSON
{"g8":["f7","h7","f8","h8"],"g7":["g6","g5"],"e6":[]}
```

where key, example "g8" is the square of a piece and ["f7","h7","f8","h8"] are all the valid moves
for the piece on that square.

In this example, there's a knight on e6. However, it cannot move because it is pinned by a white
bishop on a2. Thus, the valid moves array for "e6" is empty.
