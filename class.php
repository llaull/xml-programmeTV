<?php
class ProgramTV
{
	var $url = "http://www.kazer.org/tvguide.xml?u=XX"; //votre userhash 
	var $c_path = './';
	var $c_nom = "tvguide.xml";
	var $cache_life = '432000'; //caching time, in seconds /5j = 432000

	public function load(){

		$data = $this->curl_load();
 		$dom = new DOMDocument('1.0', 'UTF-8');
		

		$stat = @filemtime($this->c_path.$this->c_nom);
		if (!$stat or (time() - $stat >= $this->cache_life)){
		    
		    file_put_contents($this->c_path.$this->c_nom,$data);
			$dom->load($this->c_path.$this->c_nom);

		}else{

			$dom->load($this->c_path.$this->c_nom);
		}
		/**************/
		/* praser XML */
		$chaines = $dom->getElementsByTagName('channel'); 
		$programmes = $dom->getElementsByTagName('programme'); 


		//liste les chaines choisi dans mon compte kazer.org
		foreach ($chaines as $chaine) {
			$guideTV["chaine"][$chaine->getAttribute('id')] = $chaine->getElementsByTagName("display-name")->item(0)->nodeValue; 
		}

		//liste des programme
		foreach ($programmes as $programme) {
			
			$programmeDebut = $programme->getAttribute('start');
			preg_match("/(?P<year>\d{4})(?P<month>\d{2})(?P<day>\d{2})(?P<heure>\d{2})(?P<min>\d{2})(?P<sec>\d{2})/", $programmeDebut, $results);

            if( ($results["heure"] >= 20) AND ($results["heure"] <= 23) AND ($results["day"] == date("d")) AND ($programme->getElementsByTagName("length")->item(0)->nodeValue >= 20) ){

				$Newdate = $results["day"]." ".$results["heure"].":".$results["min"].":".$results["sec"];
				$channel = $programme->getAttribute('channel');

				$guideTV['programme'][$channel][$Newdate]["title"] = $programme->getElementsByTagName("title")->item(0)->nodeValue; 
				$guideTV['programme'][$channel][$Newdate]["sub-title"] = $programme->getElementsByTagName("sub-title")->item(0)->nodeValue; 
				$guideTV['programme'][$channel][$Newdate]["category"] = $programme->getElementsByTagName("category")->item(0)->nodeValue; 
				$guideTV['programme'][$channel][$Newdate]["desc"] = $programme->getElementsByTagName("desc")->item(0)->nodeValue; 
				$guideTV['programme'][$channel][$Newdate]["date"] = $programme->getElementsByTagName("date")->item(0)->nodeValue; 
				$guideTV['programme'][$channel][$Newdate]["length"] = $programme->getElementsByTagName("length")->item(0)->nodeValue; 

			}

		}
    
    	return $guideTV;
    	/**************/
		
	}

	public function curl_load(){

	    curl_setopt($ch=curl_init(), CURLOPT_URL, $this->url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $response = curl_exec($ch);
	    curl_close($ch);
	    
	    return $response;
	}
}
?>