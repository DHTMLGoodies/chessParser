##PGN to PHP array parser
PHP class for conversion of Chess PGN files to PHP array or JSON.

This is the chess parser used in DHTML Chess at dhtml-chess.com

####License: LGPL (Lesser General Public License).

Example of use: 

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
