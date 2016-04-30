<?php
/* No execution time limits */
    set_time_limit(0); 

/* Constant */
    define("UNDEFINED", "undefined");
    define("NOLINK", "nolink");

/* Variables */
	$attr = array('name', 'colors', 'types', 'subtypes'); //Single card attributes array
	$cards = json_decode(file_get_contents("AllCards.json"), true); //All Json cards
    $listIndex = 0; //Index of the $list matrix
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
* $stmArgs (strings Array): variables such as: table name, column name, clauses, exc. 
*
* the sql statment which is passed must have ':var' (without apostrophes) instead normal fields.
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
            $stm -> execute();
        } catch(PDOException $e) {
            echo("Failed to Query at line 47: " . $e -> getMessage());
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
* $imgAlt (string): image name
* $proxy (bool): if there is proxy or not
*
* 
* 
*
*/
    function getImageLink($url, $imgAlt, $proxy) { 
        $fp = fopen ('tempFile.html', 'w');
        $channel = curl_init();
        
        curl_setopt($channel, CURLOPT_URL, $url); //Set cURL url
        curl_setopt($channel, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1'); //Go simulate firefox or nothing will return
        curl_setopt($channel, CURLOPT_HEADER, false); //Set curl_header property to false to ignore <head></head> tag
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true); //Set curl to return data instead printing it to browser
		curl_setopt($channel, CURLOPT_FILE, $fp); // Set cURL to save RETURNTRANSFER data on file
		if( $proxy ){ // If proxy Ip / auth are needed (damed forefont)
			curl_setopt($channel, CURLOPT_PROXYPORT, "8080"); // Proxy port
			curl_setopt($channel, CURLOPT_PROXY, "192.168.0.10"); // Proxy Ip
			curl_setopt($channel, CURLOPT_PROXYAUTH, CURLAUTH_NTLM); // Proxy HTTP authentication method
			curl_setopt($channel, CURLOPT_PROXYUSERPWD, "inf.peironem1610:asdfghjkl"); // Proxy authentication credentials
		}
        
        $outPage = curl_exec($channel); //Exec cURL
        curl_close($channel); //Close the curl handler
		fclose($fp);
		
		$tempPage = file_get_contents('tempFile.html');
        
        $allImagesRegex = '/<img(.*)>/misU'; // Extract all images
		$cardLinkRegex = '/src="(.*)"/iU'; // Extract card image link
        
        preg_match_all($allImagesRegex, $tempPage, $image); // finding all images in page
        preg_match($cardLinkRegex, $image[0][1], $link); // extracting image link of "actual processed image" at position [0][1] (which contain card image)
        
        if($link[1]) {
            return $link[1]; //image link 
        } else {
            return NOLINK; //image link 
        }
		                                                                                 
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
    // Load data
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
        
    // Add data to db
        try {
            $stm = $conn -> prepare("INSERT INTO carte(nome, link, tipo, sottotipo, colore) VALUES(?, ?, ?, ?, ?)");
            $stm -> bindParam(1, $cardsList[$listIndex][0]);
            $stm -> bindParam(2, $cardsList[$listIndex][4]);
            $stm -> bindParam(3, $cardsList[$listIndex][2]);
            $stm -> bindParam(4, $cardsList[$listIndex][3]);  
            $stm -> bindParam(5, $cardsList[$listIndex][1]);
            $stm -> execute();
        } catch(PDOException $e) {
            echo("Failed to Query at line 164: " . $e -> getMessage());
            die($e -> getMessage());
        }
	}
    
    $conn = null;
?>