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
    function getImageLink($url, $imgAlt) { 
        $regexError = false; 
        $channel = curl_init();
        
        curl_setopt($channel, CURLOPT_URL, $url); //Set cURL url
        curl_setopt($channel, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'); //Go simulate firefox or nothing will return
        curl_setopt($channel, CURLOPT_HEADER, true); //Set curl_header prop. to true to ignore <head></head> tag
        curl_setopt($channel, CURLOPT_BINARYTRANSFER,1);
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true); //Set curl to return the data instead of printing it to the browser.
        
        $outPage = curl_exec($channel); //Exec cURL
        curl_close($channel); //Close the curl handler
        
        $startPos = stripos($outPage, '<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" style="margin: 0 0 0.5em 0;">');
        $endPos = stripos($outPage, '</table>', $startPos);
        var_dump($startPos . ' ' . $endPos);
        return $outPage;                                                                                        /* IL PROBLEMA E` QUA DIO CANE */
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
    /*for($listIndex = 0; $listIndex < count($cardsList); $listIndex++) {
        if($cardsList[$listIndex][1]) {
            $cardsList[$listIndex][1] = $colorList[strtolower($cardsList[$listIndex][1])];
        } else {
            $cardsList[$listIndex][1] = $colorList[UNDEFINED];
        }
        if($cardsList[$listIndex][2]) {
            $cardsList[$listIndex][2] = $typeList[strtolower($cardsList[$listIndex][2])];
        } else {
            $cardsList[$listIndex][2] = $typeList[UNDEFINED];
        }*/
        $listIndex = 0;
        /*$cardsList[$listIndex][] =*/echo(getImageLink('http://magiccards.info/query?q=' . str_replace(' ', '+', $cardsList[$listIndex][0]) . '&v=card&s=cname', $cardsList[$listIndex][0]));
    /*}*/
    
    $conn = null;

?>