<?php
date_default_timezone_set("Europe/Berlin");

// example of how to modify HTML contents
include('classes/simple_html_dom.php');
include('classes/class.db.php');

// get DOM from URL or file
define('BASEURL', 'http://buergerinfo.halle.de/');

for ($i = 1; $i < 13; $i++)
{
	fetchData($i, 2013);
}

function fetchData($iMonth, $iYear)
{
	$iVorlageCount = 0;
	$iFileCount = 0;

	$html = file_get_html(BASEURL . 'si0040.asp?__cmonat=' . $iMonth . '&__cjahr=' . $iYear . '&__canz=1');
	$oDb = new DB();

	// remove all image
	foreach ($html->find('a.smc_doc') as $e)
	{
		echo $e->innertext . "\n";

		$sitzungUrl = $e->href;

		$sitzungHtml = file_get_html(BASEURL . $sitzungUrl);

		foreach ($sitzungHtml->find('a.smc_doc') as $sitzungE)
		{
			echo ' - ' . $sitzungE->innertext . "\n";

			$vorlageUrl = $sitzungE->href;

			$vorlageHtml = false;
			if (! $vorlageHtml = file_get_html(BASEURL . $vorlageUrl))
			{
				sleep(10);
				if (! $vorlageHtml = file_get_html(BASEURL . $vorlageUrl))
				{
					echo 'ERROR FETCHING URL: ' . BASEURL . $vorlageUrl . "\n";
				}
			}

			$aVorlageMeta = array(
				'name' => '',
				'type' => '',
				'date' => '',
				'subject' => '',
			);

			foreach ($vorlageHtml->find('table#smctablevorgang tbody tr') as $vorlageInfo)
			{
				if (! is_object($vorlageInfo->find('td', 0)) || ! is_object($vorlageInfo->find('td', 1)))
				{
					continue;
				}
				
				$sMetaKey = trim($vorlageInfo->find('td', 0)->innertext);
				$sMetaValue = trim($vorlageInfo->find('td', 1)->innertext);

				$sMetaValue = utf8_decode(preg_replace_callback("/&#(\\d+);/u", "pcreEntityToUtf", $sMetaValue));

				switch ($sMetaKey)
				{
					case "Name:": $aVorlageMeta['name'] = $sMetaValue; break;
					case "Art:": $aVorlageMeta['type'] = $sMetaValue; break;
					case "Datum:": $aVorlageMeta['date'] = $sMetaValue; break;
					case "Betreff:": $aVorlageMeta['subject'] = $sMetaValue; break;
				}
			}		
			
			// setup date
			$aVorlageMeta['date'] = strtotime($aVorlageMeta['date']);

			$iVorlageId = $oDb->vorlageExists($aVorlageMeta['name']);

			if ($iVorlageId == false)
			{
				$iVorlageId = $oDb->insertVorlage($aVorlageMeta);
			}

			foreach ($vorlageHtml->find('table.smcdocbox td.smc_doc') as $vorlageForm)
			{
				$vorlageFormDataRaw = $vorlageForm->find('input[type=hidden]');
				$vorlageFormData = array();

				foreach ($vorlageFormDataRaw as $input)
				{
					$vorlageFormData[$input->name] = $input->value;
				}

				if (! isset($vorlageFormData['DT']) || $vorlageFormData['DT'] == '' || ! isset($vorlageFormData['DEN']) || $vorlageFormData['DEN'] == '')
				{
					continue;
				}

				$sFileName = $vorlageFormData['DT'] . '.' . $vorlageFormData['DEN'];
				$iFileId = $oDb->fileExists($sFileName);

				if ($iFileId === false)
				{
					$aFileData = array(
						'content' => getForm($vorlageFormData),
						'filename' => $sFileName,
					);

					$iFileId = $oDb->insertFile($aFileData);
				}

				$oDb->insertVorlageFileConnection($iVorlageId, $iFileId);

				$iFileCount++;
			}

			$iVorlageCount++;

			echo "Stand: " . $iVorlageCount . " Vorlagen mit " . $iFileCount . " Dokumenten\n\n";
		}	
	}	
}

function getForm($aData)
{
	include('config/stopwords.php');
	$sText = '';

	if ($aData['DT'] == '' || $aData['DEN'] == '')
	{
		return false;
	}
	
	$filename = 'downloads/' . $aData['DT'] . '.' . $aData['DEN'];
	
	// get file
	if (! file_exists($filename))
	{
		$url = BASEURL . 'getfile.asp';
		$sData = '';
		foreach ($aData as $key => $value) 
		{ 
			$sData .= $key.'='.$value.'&'; 
		}
		rtrim($sData, '&');

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, count($aData));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $sData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$data = curl_exec($ch);//get curl response

		file_put_contents($filename, $data);

		echo ' --> ' . $filename . "\n";

		//done
		curl_close($ch);
	}
	
	foreach ($stopwords['de'] as $nKey => $sValue) 
	{
		$stopwords['de'][$nKey] = "/\b$sValue\b/i";
	}
	
	$command = 'components/xpdfbin/bin64/pdftotext -q ' . $filename . ' -';
	$a = exec($command, $text, $retval);
	if (sizeof($text) > 0)
	{
		$sText = implode(' ', $text);
		$sText = preg_replace($stopwords["de"], ' ', $sText);
	}
	
	return $sText;
}

function pcreEntityToUtf($matches) 
{ 
	$char = intval(is_array($matches) ? $matches[1] : $matches); 
	
	if ($char < 0x80) 
	{ 
		// to prevent insertion of control characters 
		if ($char >= 0x20) return htmlspecialchars(chr($char)); 
		else return "&#$char;"; 
	} 
	else if ($char < 0x8000) 
	{ 
		return chr(0xc0 | (0x1f & ($char >> 6))) . chr(0x80 | (0x3f & $char)); 
	} 
	else 
	{ 
		return chr(0xe0 | (0x0f & ($char >> 12))) . chr(0x80 | (0x3f & ($char >> 6))). chr(0x80 | (0x3f & $char)); 
	} 
}
    
?>