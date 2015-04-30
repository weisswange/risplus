<?php

/**
 * Class DB
 */
class DB 
{
	var $sDatabase = 'stadtrat';
	var $sUser = 'root';
	var $sPassword = 'root';
	var $sHost = 'localhost';
	
	private $sDbHandle = null;
	
	function __construct()
	{
		// db connect
		if (! $this->sDbHandle = mysql_connect($this->sHost, $this->sUser, $this->sPassword))
		{
		    die('Verbindung schlug fehl: ' . mysql_error());
		}
		
		if (! mysql_select_db($this->sDatabase, $this->sDbHandle))
		{
		    die('Datenbank konnte nicht ausgewählt werden: ' . mysql_error());
		}
	}
	
	function insertVorlage($aData)
	{
		$sSql = sprintf("INSERT INTO sr_vorlage (name, type, date, subject) VALUES ('%s', '%s', %u, '%s')",
		    mysql_real_escape_string($aData['name']),
		    mysql_real_escape_string($aData['type']),
		    $aData['date'],
		    mysql_real_escape_string($aData['subject'])
		);
		
		if (! mysql_query($sSql))
		{
		    die('Ungültige Anfrage: ' . mysql_error());
		} 
		
		return mysql_insert_id();
	}
	
	function insertFile($aData)
	{
		$sSql = sprintf("INSERT INTO sr_file (content, filename) VALUES ('%s', '%s')",
		    mysql_real_escape_string($aData['content']),
		    mysql_real_escape_string($aData['filename'])
		);
		
		if (! mysql_query($sSql))
		{
		    die('Ungültige Anfrage: ' . mysql_error());
		} 
		
		return mysql_insert_id();
	}
	
	function fileExists($sFileName)
	{
		$sSql = sprintf("SELECT id FROM sr_file WHERE filename = '%s'", mysql_real_escape_string($sFileName));

		if (! $hRes = mysql_query($sSql))
		{
		    die('Ungültige Anfrage: ' . mysql_error());
		}
		
		$aData = mysql_fetch_assoc($hRes);
		
		if (mysql_num_rows($hRes) == 0)
		{
			return false;
		}
		
		return $aData['id'];
	}
	
	function vorlageExists($sVorlageName)
	{
		$sSql = sprintf("SELECT id FROM sr_vorlage WHERE name = '%s'", mysql_real_escape_string($sVorlageName));

		if (! $hRes = mysql_query($sSql))
		{
		    die('Ungültige Anfrage: ' . mysql_error());
		}
		
		$aData = mysql_fetch_assoc($hRes);
		
		if (mysql_num_rows($hRes) == 0)
		{
			return false;
		}
		
		return $aData['id'];
	}
	
	function insertVorlageFileConnection($iVorlageId, $iFileId)
	{
		$sSql = sprintf("INSERT INTO sr_vorlage_file (vorlageid, fileid) VALUES (%u, %u)",
		    $iVorlageId,
		    $iFileId
		);
		
		if (! mysql_query($sSql))
		{
		    die('Ungültige Anfrage: ' . mysql_error());
		} 
		
		return mysql_insert_id();
	}
	
	function searchFilesContent($sSearchWord, $bFilterEinladungen, $bFilterNiederschriften)
	{
		$sSearchWord = preg_replace('/\s/', '%', $sSearchWord);
		
		$sSql = sprintf("SELECT * FROM sr_file WHERE content LIKE '%%%s%%'", $sSearchWord);
		
		if ($bFilterEinladungen)
		{
			$sSql .= " AND filename NOT LIKE '%einladung%'";
		}

        if ($bFilterNiederschriften)
        {
            $sSql .= " AND filename LIKE '%niederschrift%'";
        }
		
		if (! $hRes = mysql_query($sSql))
		{
		    die('Ungültige Anfrage: ' . mysql_error());
		}
		
		$aContent = array();
		while ($aData = mysql_fetch_assoc($hRes))
		{
			$aContent[] = $aData;
		}
		
		return $aContent;
	}

	function searchFilesTitle($sSearchWord)
	{
		$sSql = sprintf("SELECT * FROM sr_file WHERE filename LIKE '%%%s%%'", $sSearchWord);
		
		if (! $hRes = mysql_query($sSql))
		{
		    die('Ungültige Anfrage: ' . mysql_error());
		}
		
		$aContent = array();
		while ($aData = mysql_fetch_assoc($hRes))
		{
			$aContent[] = $aData;
		}
		
		return $aContent;
	}
	
	function getVorlagenForFile($iFileId)
	{
		$sSql = sprintf("SELECT DISTINCT sr_vorlage.* FROM sr_vorlage INNER JOIN sr_vorlage_file ON (sr_vorlage.id = sr_vorlage_file.vorlageid) WHERE sr_vorlage_file.fileid = %u ORDER BY date DESC", $iFileId);
		
		if (! $hRes = mysql_query($sSql))
		{
		    die('Ungültige Anfrage: ' . mysql_error());
		}
		
		$aContent = array();
		while ($aData = mysql_fetch_assoc($hRes))
		{
			$aContent[] = $aData;
		}
		
		return $aContent;
	}
	
	function getFileById($iFileId)
	{
		$sSql = sprintf("SELECT * FROM sr_file WHERE id = %u", $iFileId);
		
		if (! $hRes = mysql_query($sSql))
		{
		    die('Ungültige Anfrage: ' . mysql_error());
		}
		
        if (mysql_num_rows($hRes) == 0)
        {
            return false;
        }

		return mysql_fetch_assoc($hRes);
	}

    function getVorlageById($iVorlageId)
   	{
   		$sSql = sprintf("SELECT * FROM sr_vorlage WHERE id = %u", $iVorlageId);

   		if (! $hRes = mysql_query($sSql))
   		{
   		    die('Ungültige Anfrage: ' . mysql_error());
   		}

           if (mysql_num_rows($hRes) == 0)
           {
               return false;
           }

   		return mysql_fetch_assoc($hRes);
   	}

    function getFilesForVorlage($iVorlageId)
    {
        $sSql = sprintf("SELECT DISTINCT sr_file.* FROM sr_file INNER JOIN sr_vorlage_file ON (sr_file.id = sr_vorlage_file.fileid) WHERE sr_vorlage_file.vorlageid = %u", $iVorlageId);

        if (! $hRes = mysql_query($sSql))
        {
            die('Ungültige Anfrage: ' . mysql_error());
        }

        $aContent = array();
        while ($aData = mysql_fetch_assoc($hRes))
        {
            $aContent[] = $aData;
        }

        return $aContent;
    }

    function getDatabaseSize()
    {
        mysql_select_db("yourdatabase");
        $q = mysql_query("");

        $sSql = "SHOW TABLE STATUS";

        if (! $hRes = mysql_query($sSql))
        {
            die('Ungültige Anfrage: ' . mysql_error());
        }

        $iSize = 0;
        while ($aRow = mysql_fetch_array($hRes))
        {
            $iSize += $aRow["Data_length"] + $aRow["Index_length"];
        }

        $decimals = 2;
        $iSizeInMegabytes = number_format($iSize/(1024*1024),$decimals);

        return $iSizeInMegabytes;
    }

    function searchVorlagen($sSearchWord)
    {
        $sSql = sprintf("SELECT * FROM sr_vorlage WHERE name = '%s' OR subject LIKE '%%%s%%' ORDER BY date DESC", $sSearchWord, $sSearchWord);

        if (! $hRes = mysql_query($sSql))
        {
            die('Ungültige Anfrage: ' . mysql_error());
        }

        $aContent = array();
        while ($aData = mysql_fetch_assoc($hRes))
        {
            $aContent[] = $aData;
        }

        return $aContent;
    }
}