<?php
namespace GescanPim\Bundle\ConnectorBundle\Reader\File\Eleknet;

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
class ProductCsvReader extends PimCsvReader{
    
    protected $current = null;
    
    public function read() {
        $data = false;
        while($data===false||$data['Prod']==$this->current){
            $data = parent::read();
            if(!is_array($data)){
                return $data;
            }
            
        }
        $this->current = $data['Prod'];
        return $data;
    }
}
