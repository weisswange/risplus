<?php

/**
 * Class DB
 */
class DB 
{
    /**
     * @var string  database name
     */
    var $sDatabase = 'stadtrat';
    /**
     * @var string  database user
     */
    var $sUser = 'root';
    /**
     * @var string  database password
     */
    var $sPassword = 'root';
    /**
     * @var string  database host
     */
    var $sHost = 'localhost';

    /**
     * @var null|resource   database handle
     */
    private $hDb = null;

    /**
     * Constructor
     *
     * @return \DB
     */
    function __construct()
	{
        if (is_resource($this->hDb))
        {
            return true;
        }

		// db connect
		if (! $this->hDb = mysql_connect($this->sHost, $this->sUser, $this->sPassword))
		{
		    die('Verbindung schlug fehl: ' . mysql_error());
		}
		
		if (! mysql_select_db($this->sDatabase, $this->hDb))
		{
		    die('Datenbank konnte nicht ausgewählt werden: ' . mysql_error());
		}

        return true;
	}

    /**
     * Insert a vorlage to database
     *
     * @param $aData
     * @return int  id for vorlage
     */
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

    /**
     * Inserts file data to database
     *
     * @param $aData
     * @return int  id for file
     */
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

    /**
     * Checks if a file exists
     *
     * @param $sFileName
     * @return bool
     */
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

    /**
     * Checks if a vorlage exists
     *
     * @param $sVorlageName
     * @return bool
     */
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

    /**
     * Inserts relation from file to vorlage
     *
     * @param $iVorlageId
     * @param $iFileId
     * @return int
     */
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

    /**
     * Search in database for string
     *
     * @param $sSearchWord
     * @param $bFilterEinladungen
     * @param $bFilterNiederschriften
     * @return array
     */
    function searchFilesContent($sSearchWord, $bFilterEinladungen, $bFilterNiederschriften)
	{
		$sSearchWord = preg_replace('/\s/', '%', $sSearchWord);
		
		//$sSql = sprintf("SELECT * FROM sr_file WHERE content LIKE '%%%s%%'", $sSearchWord);
        $sSql = sprintf("SELECT *, MATCH(content) AGAINST('%%%s%%') AS score FROM sr_file WHERE MATCH(content) AGAINST('%%%s%%')", $sSearchWord, $sSearchWord);
		
		if ($bFilterEinladungen)
		{
			$sSql .= " AND filename NOT LIKE '%einladung%'";
		}

        if ($bFilterNiederschriften)
        {
            $sSql .= " AND filename LIKE '%niederschrift%'";
        }

        $sSql .= " ORDER BY score DESC LIMIT 30";
		
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

    /**
     * Search for string in file title
     *
     * @param $sSearchWord
     * @return array
     */
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

    /**
     * Returns vorlage for file
     *
     * @param $iFileId
     * @return array
     */
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

    /**
     * Returns file data for file id
     *
     * @param $iFileId
     * @return array|bool
     */
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

    /**
     * Returns a vorlage by id
     *
     * @param $iVorlageId
     * @return array|bool
     */
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

    /**
     * Get file for vorlage
     *
     * @param $iVorlageId
     * @return array
     */
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

    /**
     * Returns database size as formated string
     *
     * @return string
     */
    function getDatabaseSize()
    {
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
        $iSizeInMegabytes = number_format($iSize/(1024*1024), 2, ',', '.');

        return $iSizeInMegabytes;
    }

    /**
     * Search for string in vorlagen
     *
     * @param $sSearchWord
     * @return array
     */
    function searchVorlagen($sSearchWord)
    {
        $sSearchWord = preg_replace('/\s{1,}/', '%', $sSearchWord);
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