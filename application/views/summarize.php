<?php

	

	function Summarize($filename){

		
		$load_file  = file_get_contents("assets/corpus/".$filename);
		$document = $load_file;
		$kalimat 	= preg_split("/[.]+/", $load_file);
		$pisah_kalimat = $kalimat;
		
		$kalimat = array_slice($kalimat, 0, sizeof($kalimat)-1);
		for ($i=0; $i < count($kalimat); $i++) { 
			$kalimat[$i] = preg_split("/[\s]+/", $kalimat[$i]);
			$kalimat[$i] = array_count_values($kalimat[$i]);
		}

		$total_kalimat = count($kalimat);		
		$stopwords	= file_get_contents("./assets/stopwords/stopwords.txt");
		$stopwords	= preg_split("/[\s]+/", $stopwords);
		$token = preg_split("/[\d\W\s]+/", strtolower($document));
		$token = array_slice($token, 0, sizeof($token)-1);

	
		$token = array_diff($token, $stopwords);		

		$token = array_values($token); 

		$token = array_count_values($token); 
		$total_token = count($token);
		ksort($token);
		$vsm_tf = array();
		$i = 0;
		foreach ($token as $key => $value) { 
			for ($j=0; $j < $total_kalimat; $j++) { 
				for ($k=0; $k < $total_token; $k++) { 
					if(array_key_exists($key, $kalimat[$j])){
						$vsm_tf[$i][$j] = $kalimat[$j][$key];
					}else{
						$vsm_tf[$i][$j] = 0;
					}
				}
			}
			$i++;
		}


		$vsm_wtf = array();
		for ($i=0; $i < $total_token; $i++) { 
			for ($j=0; $j < $total_kalimat; $j++) { 
				if($vsm_tf[$i][$j] !== 0){
					$vsm_wtf[$i][$j] = 1 + log10($vsm_tf[$i][$j]);
				}else{
					$vsm_wtf[$i][$j] = 0;
				}
			}
		}

		$dft = array();
		foreach ($token as $key => $value) {
			$dft[$key] = $value;
		}

		$idft = array();
		foreach ($dft as $key => $value) {
			$idft[$key] = log10($total_kalimat/$dft[$key]);
		}	


		$vsm_wtd = array();
		$i = 0;
		foreach ($idft as $key => $value) {
			for ($j=0; $j < $total_kalimat; $j++) { 
				$vsm_wtd[$i][$j] = $vsm_wtf[$i][$j] * $value;
			}
			$i++;
		}

		$ws = array();
		$i = 0;
		for ($i=0; $i < $total_token; $i++) { 
			for ($j=0; $j < $total_kalimat; $j++) {
				if(empty($ws[$j])){
					$ws[$j] = 0;
					$ws[$j] += $vsm_wtd[$i][$j];
				}else{
					$ws[$j] += $vsm_wtd[$i][$j];
				}
			}
		}


		arsort($ws);

		$sorted = array_slice($ws, 0, count($ws)/2, true);
		ksort($sorted);
		$summary = "";
		foreach ($sorted as $key => $value) {
			$summary = $summary.$pisah_kalimat[$key].". ";
		}

		$final_result = array();
		$final_result['asli'] = $document;
		$final_result['ringkasan'] = $summary;

		return $final_result;
	}

?>
