<?php
/* Constant */
    define("UNDEFINED", "undefined");
/* Variables */
	$attr = array('name', 'colors', 'types', 'subtypes'); //Single cards attributes array
	$cards = json_decode(file_get_contents("AllCards.json"), true); //All Json cards
    $listIndex = 0; //Index of the $list matix
    $conn = null; //db reference
    $cardsList = array (
        array(), //Card name
        array(), //Color
        array(), //Card type
        array()  //Card subtype
    );
    $colorList = array();
    $typeList = array();
	
/* Functions */
   /*
    * fromDbToArray function returns an array-form of databases key => description tables.
    * 
    * $conn (PDO): database connection.
    * $stmSql (string): sql statment with n variables
    * $stmArgs (strings Array): sql statment variables (in order) such as: table name, column name, clauses, exc. 
    *
    * the sql statment which is passed must have ':var' (without apostrophes) instead of normal fields.
    * the statment arguments have to be as many as the sql statment ':var's .
    *
    */
    function fromDbToArray($conn, $stmSql, $stmArgs) {
        $tempArray = array();
        
        for($argsIndex = 0; $argsIndex < count($stmArgs); $argsIndex++) {
            $stmSql = preg_replace('/:var/', $stmArgs[$argsIndex], $stmSql, 1);
        }
        
        try {
            $stm = $conn -> prepare($stmSql);
            $stm->execute();
        } catch(PDOException $e) {
            echo("Failed to Query: " . $e -> getMessage());
            die($e -> getMessage());
        }
        
        while($row = $stm -> fetch()) {
            $tempArray[$row[1]] = intval($row[0]);
        }
        
        return $tempArray;
    }
   /*
    * getImageLink function returns the selected image from a website
    * 
    * $url (string): webpage URL
    * 
    * 
    *
    * 
    * 
    *
    */
    function getImageLink($url, $imgAlt, $proxy) { 
        $fp = fopen ('tempFile.html', 'w');
        $channel = curl_init();
        
        curl_setopt($channel, CURLOPT_URL, $url); //Set cURL url
        curl_setopt($channel, CURLOPT_USERAGENT, 'me'); //Go simulate firefox or nothing will return
        curl_setopt($channel, CURLOPT_HEADER, true); //Set curl_header prop. to true to ignore <head></head> tag
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true); //Set curl to return the data instead of printing it to the browser.
		curl_setopt($channel, CURLOPT_FILE, $fp);
		if($proxy){
			curl_setopt($channel, CURLOPT_PROXYPORT, "8080");
			curl_setopt($channel, CURLOPT_PROXY, "192.168.0.10");
			curl_setopt($channel, CURLOPT_PROXYAUTH, CURLAUTH_NTLM);
			curl_setopt($channel, CURLOPT_PROXYUSERPWD, "inf.peironem1610:asdfghjkl");
		}
        
        $outPage = curl_exec($channel); //Exec cURL
        curl_close($channel); //Close the curl handler
		fclose($fp);
		
		$tempPage = file_get_contents('tempFile.html');
		
		$regex = '/<img.*src=\"(.*)\".*alt=\"'.$imgAlt.'\".*>/misU';
		preg_match($regex, $tempPage, $m);
		return $m[1];                                                                                        /* IL PROBLEMA E` QUA DIO CANE */
    }
/* Set up sqlite db connection */
    try {
		$conn = new PDO('sqlite:carte.db', null, null, array(PDO::ATTR_PERSISTENT => true));
		$conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $ex) {
		echo("Failed to connect: " . $ex -> getMessage());
        die($ex -> getMessage());
	}
/* Fill associated array */
    $colorList = fromDbToArray($conn, 'SELECT :var FROM :var', array('*', 'colori'));
    $typeList = fromDbToArray($conn, 'SELECT :var FROM :var', array('*', 'tipi'));
/* Fill cards array form Json cards file */
	foreach ($cards as $key => $val) {
		$attrTemp = '';
        
		for($i = 0; $i < count($attr); $i++) { 
            
            if(isset($val[$attr[$i]])){
                $temp = $val[$attr[$i]];
                if(is_array($temp)) {
                    $cardsList[$listIndex][$i] = $temp[0];
                } else {
                    $cardsList[$listIndex][$i] = $temp;
                }
            } else {
                $cardsList[$listIndex][$i] = null;
            }
		}
		$listIndex++;
	}
    
/* Set db values to cards colour and type and take img link */
    for($listIndex = 0; $listIndex < count($cardsList); $listIndex++) {
        if($cardsList[$listIndex][1]) {
            $cardsList[$listIndex][1] = $colorList[strtolower($cardsList[$listIndex][1])];
        } else {
            $cardsList[$listIndex][1] = $colorList[UNDEFINED];
        }
        if($cardsList[$listIndex][2]) {
            $cardsList[$listIndex][2] = $typeList[strtolower($cardsList[$listIndex][2])];
        } else {
            $cardsList[$listIndex][2] = $typeList[UNDEFINED];
        }
        $cardsList[$listIndex][] = getImageLink('http://magiccards.info/query?q=' . str_replace(' ', '+', $cardsList[$listIndex][0]) . '&v=card&s=cname', $cardsList[$listIndex][0], false);
	}

    $conn = null;
?>