<?php
namespace GescanPim\Bundle\ConnectorBundle\Reader\File\Etim;

use Pim\Bundle\BaseConnectorBundle\Reader\File\CsvReader as PimCsvReader;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MagentoCsvProductReader
 *
 * @author ecoisne
 */
class CsvReader extends PimCsvReader{
    
    /**
     * {@inheritdoc}
     */
    public function read()
    {
        $return = parent::read();
        if(count($return)>3){
            $this->stepExecution->incrementSummaryInfo('malformed');
            $this->stepExecution->addWarning('malformed', "Malformed CSV: has ".count($return)." columns", array(), $return);
        }
        return $return;
        
    }
}
