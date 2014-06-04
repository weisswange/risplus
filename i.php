<?php

include_once('classes/class.vorlagen.php');

class Index extends App
{
    var $oDb = null;
    var $oSmarty = null;
    var $iParameter = 0;

    function __construct($iParameter)
    {
        $this->oDb = $this->getDb();
        $this->oSmarty = $this->getSmarty();
        $this->iParameter = $iParameter;
    }

    public function run()
    {
        $fi = new FilesystemIterator(__DIR__ . '/downloads', FilesystemIterator::SKIP_DOTS);
        $iStatsFilesCount = iterator_count($fi);

        $sDbSize = $this->oDb->getDatabaseSize();

        // collect variables
        $this->oSmarty->assign('stats_count_files', $iStatsFilesCount);
        $this->oSmarty->assign('stats_count_dbsize', $sDbSize);

        return true;
    }

    public function show()
    {
        $this->oSmarty->display('frame.i.tpl');
    }
}