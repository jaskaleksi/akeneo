<?php
namespace GescanPim\Bundle\ConnectorBundle\Processor\Sx;

use GescanPim\Bundle\ConnectorBundle\Processor\MappingProcessor;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SxManufacturerProcessor
 *
 * @author ecoisne
 */
class ManufacturerProcessor  extends MappingProcessor{
    protected function getCode($item) {
        return $item->vendno;
    }

    protected function getInitialValue($item) {
        return $item->name;
    }
    
    public function process($item) {
        $return = parent::process($item);
        
        $return->setValue($this->getInitialValue($item));
        return $return;
    }
}
