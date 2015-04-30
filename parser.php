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

			foreach ($vorlageHtml->find('table.smcdocbox td.smcdocname') as $vorlageForm)
			{
				$oVorlageFileLink = $vorlageForm->find('a', 0);

				$iFileId = 0;
				$sFileLink = '';
				if (is_object($oVorlageFileLink))
				{
					$sFileLink = (string) $oVorlageFileLink->href;

					if (preg_match('/id=(\d{1,})/', $sFileLink, $aMatches))
					{
						$iFileId = $aMatches[1];
					}
				}

				if ($iFileId != 0 && $sFileLink != '')
				{
					$aFile = getForm($sFileLink, $iFileId);

					$aFileData = array(
						'content' => $aFile['content'],
						'filename' => $aFile['filename'],
					);
					
					$iFileId = $oDb->insertFile($aFileData);

					$oDb->insertVorlageFileConnection($iVorlageId, $iFileId);
				}

				$iFileCount++;
			}

			$iVorlageCount++;

			echo "Stand: " . $iVorlageCount . " Vorlagen mit " . $iFileCount . " Dokumenten\n\n";
		}	
	}	
}

function getForm($sFileLink, $iFileId)
{
	include('config/stopwords.php');
	$sText = '';
	$sFileName = '';

	$url = htmlspecialchars_decode(BASEURL . $sFileLink);

	//open connection
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_HEADER, 1);

	$response = curl_exec($ch);//get curl response

	// Then, after your curl_exec call:
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header = substr($response, 0, $header_size);
	$body = substr($response, $header_size);

	$sFileType = '';
	if (preg_match('/Content-Disposition: .*filename=".*?\.([^ ]+)"/', $header, $aMatches)) 
	{
		$sFileType = $aMatches[1];
		$sFileName = $iFileId . '.' . $sFileType;
	}

	if ($sFileName != '')
	{
		file_put_contents('downloads/' . $sFileName, $body);
		echo ' --> ' . $sFileName . "\n";
	}

	//done
	curl_close($ch);
	
	foreach ($stopwords['de'] as $nKey => $sValue) 
	{
		$stopwords['de'][$nKey] = "/\b$sValue\b/i";
	}
	
	$command = 'components/xpdfbin/bin64linux/pdftotext -q ' . '/var/www/html/downloads/' . $sFileName . ' -';
	$a = exec($command, $text, $retval);
	if (sizeof($text) > 0)
	{
		$sText = implode(' ', $text);
		$sText = preg_replace($stopwords["de"], ' ', $sText);
	}
	
	return array('content' => $sText, 'filename' => $sFileName);
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