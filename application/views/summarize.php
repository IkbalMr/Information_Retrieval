<?php

	

	function summarize($filename){

		
		$load_file  = file_get_contents("assets/corpus/".$filename);
		$document = $load_file;
		$kalimat 	= preg_split("/[.]+/", $load_file);
		$pisah_kalimat = $kalimat;

		// buang array terakhir (kosong)
		$kalimat = array_slice($kalimat, 0, sizeof($kalimat)-1);

		// menghitung frekuensi kata unik tiap kalimat
		for ($i=0; $i < count($kalimat); $i++) { 
			$kalimat[$i] = preg_split("/[\s]+/", $kalimat[$i]);
			$kalimat[$i] = array_count_values($kalimat[$i]);
		}

		
		$total_kalimat = count($kalimat);

		// buka daftar stopword dan pisahkan perkata
		$stopwords	= file_get_contents("./assets/stopwords/stopwords.txt");
		$stopwords	= preg_split("/[\s]+/", $stopwords);

		// membuat kalimat unik
		// case folding dan tokenisasi
		$token = preg_split("/[\d\W\s]+/", strtolower($document));

		// buang array terakhir (kosong)
		$token = array_slice($token, 0, sizeof($token)-1);

		// membuang stopwords (filtering)
		$token = array_diff($token, $stopwords);		

		// perbaiki indeks
		$token = array_values($token); 

		// menyimpan nilai df tiap token
		// hilangkan redudansi token
		$token = array_count_values($token); 

		// menghitung jumlah token
		$total_token = count($token);

		// mengurutkan token berdasarkan key
		ksort($token);
	
		// membuat vector space model dan
		// menghitung frekuensi kemunculan kata
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

		// echo "frekuensi tiap kata dalam kalimat<br>";
		// foreach ($vsm_tf as $key => $value) {
		// 	print_r($value);
		// 	echo "<br/>";
		// }

		// menghitung tf weight
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

		// echo "tf weight <br>";
		// foreach ($vsm_wtf as $key => $value) {
		// 	print_r($value);
		// 	echo "<br/>";
		// }

		// menghitung dft
		$dft = array();
		foreach ($token as $key => $value) {
			$dft[$key] = $value;
		}

		// menghitung idft
		$idft = array();
		foreach ($dft as $key => $value) {
			$idft[$key] = log10($total_kalimat/$dft[$key]);
		}	

		// menghitung wt,d
		$vsm_wtd = array();
		$i = 0;
		foreach ($idft as $key => $value) {
			for ($j=0; $j < $total_kalimat; $j++) { 
				$vsm_wtd[$i][$j] = $vsm_wtf[$i][$j] * $value;
			}
			$i++;
		}

		// echo "wt,d <br>";
		// foreach ($vsm_wtd as $key => $value) {
		// 	print_r($value);
		// 	echo "<br/>";
		// }

		// menghitung Ws
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

		// echo "Ws <br>";
		// foreach ($ws as $key => $value) {
		// 	print_r($key." ".$value);
		// 	echo "<br/>";
		// }	

		// mengurutkan hasil Ws
		arsort($ws);

		// memotong 50%
		$sorted = array_slice($ws, 0, count($ws)/2, true);

		// mengurutkan berdasarkan urutan kalimat
		ksort($sorted);

		// menggabungkan kalimat terpilih menjadi ringkasan
		$summary = "";
		foreach ($sorted as $key => $value) {
			$summary = $summary.$pisah_kalimat[$key].". ";
		}

		// menyimpan dokumen asli dan hasil ringkasan
		$final_result = array();
		$final_result['asli'] = $document;
		$final_result['ringkasan'] = $summary;

		// mengembalikan dokumen asli dan hasil ringkasan
		return $final_result;
	}

?>