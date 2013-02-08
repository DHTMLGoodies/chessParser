<?php

class DGTGameParser {

    private $remoteUrl;

    public function __construct(){

    }

    public function getPgnFromDGTData($remoteUrl) {
        $this->remoteUrl = $this->getCorrectUrl($remoteUrl);
        $gameIds = $this->getGameIds();

		if(!count($gameIds)){
			return array(
				'success' => false,
				'message' => 'Unable to load data from url ' . $this->remoteUrl
			);
		}

        $contents = '';
        foreach($gameIds as $gameId){
        	$urlPropertyData = $this->remoteUrl . 'game' . $gameId . '.txt';
            if(!$dgtGameData = $this->readRemoteFile($urlPropertyData)){
                return false;
            }
            $urlPositionData = $this->remoteUrl . 'pos' . $gameId . '.txt';
            if(!$dgtMoveData = $this->readRemoteFile($urlPositionData)){
                return false;
            }
            $contents .= $this->toPgn($dgtGameData, $dgtMoveData);
        }
        $ret['finished_round'] = false;
        return $contents;
    }
    private function getCorrectUrl($url){
    	$posQueryString = strpos($url, '?');
    	if($posQueryString >0){
    		$url = substr($url, 0,$posQueryString);
    	}
    	return $url."/";
    }

    private function getGameIds(){
    	$ret = array();
    	$content = $this->readRemoteFile($this->remoteUrl . 'tocks.txt');
    	preg_match_all("/<(.*?)>/s", $content,$matches, PREG_SET_ORDER);
    	for($i=0,$count = count($matches);$i<$count; $i+=2){
    		if($matches[$i][1]!='.'){
    			$ret[] = $matches[$i][1];
    		}else{
    			return $ret;
    		}
    	}
    	return $ret;
    }

    private function readRemoteFile($url) {
        $contents = RemoteFileReader::getFromUrl($url);
        if(preg_match("/<html/si", $contents) || preg_match("/<h1>/si", $contents) || preg_match("/<body>/si", $contents)){
            return '';
        }
		return $contents;
    }

    private function toPgn($gameData, $moveData){
        return (
            $this->getGameProperties($gameData) .
            $this->getFenProperty($moveData).
            "\n".
            $this->getMoves($moveData)).
            "\n\n";
    }

    private function getGameProperties($dgtData){
        $ret = '';
        $mappingKeys = array(
            'u' => 'Event',
            'w' => 'White',
            'b' => 'Black',
            'm' => 'LastMoves'
        );
        $indexKeys = array(
            array('index' => 4, 'property' => 'Result'),
            array('index' => 5, 'property' => 'ClockWhite'),
            array('index' => 6, 'property' => 'ClockBlack'),
        );
        foreach($mappingKeys as $key=>$value){
            $property  = preg_replace("/.*?<".$key . ">(.*?)<.*/si", "$1", $dgtData);
            if($property){
                $ret.= '['. $value . '" '.$property.'"]' . "\n";
            }
        }

        $items = explode("<", $dgtData);
        foreach($indexKeys as $indexKey) {
            $ret.= '['. $indexKey['property'] . ' "'.$this->removeTags($items[$indexKey['index']]).'"]' . "\n";
        }
        return $ret;
    }

    private function getFenProperty($moveData){
        $items = explode("<", $moveData);
        return '[FEN "'.$this->removeTags($items[2]).'"]' . "\n";
    }


    private function removeTags($content){
        return preg_replace("/[<>]/", "", $content);
    }

    private function getMoves($dgtData){
        $ret = '';
        preg_match_all("/<([a-hO0RQKBN][^\.]{1,4}|[RNBQK][0-8a-h][^\.]{1,4})>/s", $dgtData,$matches);

        $moves = $matches[1];
        for($i=0, $countMoves = count($moves);$i<$countMoves; $i++){
            if($i % 2 == 0){
                $ret.= ceil(($i+1) / 2) .". ";
            }
            $ret.=$moves[$i]." ";
        }

        return trim($ret);
    }

}
