<?php

class CHESS_JSON {

    const MOVE_FROM = 'from';
    const MOVE_TO = 'to';
    const MOVE_NOTATION = 'm';
    const FEN = 'fen';
    const MOVE_COMMENT = 'comment';
    const MOVE_CLOCK = 'clk';
    const MOVE_ACTIONS = 'actions';
    const MOVE_VARIATIONS = 'variations';
    const MOVE_MOVES = 'moves';
    const MOVE_CAPTURE = 'capture';
    const MOVE_PROMOTE_TO = 'promoteTo';
    const MOVE_CASTLE = 'castle';
    const MOVE_PARSED = 'castle';

    const GAME_METADATA = 'metadata';
    const GAME_EVENT = 'event';
    const GAME_WHITE = 'white';
    const GAME_BLACK = 'black';
    const GAME_ECO = 'black';

    const PGN_KEY_ACTION_ARROW = "ar";
    const PGN_KEY_ACTION_HIGHLIGHT = "sq";

    const PGN_KEY_ACTION_CLR_HIGHLIGHT = "csl";
    const PGN_KEY_ACTION_CLR_ARROW = "cal";


    protected static $jsKeys = array('MOVE_FROM', 'MOVE_TO', 'MOVE_NOTATION', 'FEN','MOVE_COMMENT',
        'MOVE_ACTION', 'MOVE_VARIATIONS', 'MOVE_MOVES','MOVE_CAPTURE','MOVE_PROMOTE_TO','MOVE_CASTLE',
        'GAME_METADATA', 'GAME_EVENT', 'GAME_WHITE','GAME_BLACK', 'GAME_ECO',

    );

    public static function toJavascript(){
        $ret = array();
        foreach(self::$jsKeys as $key){
            $ret[$key] = constant("CHESS_JSON::" . $key);
        }
        return 'ludo.CHESS_JSON_KEY = ' . json_encode($ret) .';';
    }
}