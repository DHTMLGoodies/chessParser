chessParser
===========
PHP class for conversion of Chess PGN files to PHP array or JSON.

This is the chess parser used in DHTML Chess at dhtml-chess.com

####License: GPL

Example of use: 

    <?php
    $parser = new PgnParser('my-games.pgn');
    echo json_encode($parser->getGames());
    ?>
