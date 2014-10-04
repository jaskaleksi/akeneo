<?php

namespace GescanPim\Bundle\ConnectorBundle\Processor\Sx;

use GescanPim\Bundle\ConnectorBundle\Processor\ProductProcessor as AbstractProductProcessor;

class IcswProcessor extends AbstractProductProcessor{
    
    protected function checkItem()
    {
        if (is_array($this->item)) return true;
        return false;
    }
    
     protected function getColumnValue($column)
    {
        return $this->item[$column];
    }
    
    protected function findProduct()
    {
        return $this->findAttributeBySku($this->item['sku']);
    }
    
    protected function updateAttributes()
    {   
        $this->updateProductAttribute('_qty',$this->item['TOTAL']);
    }
    
    public function getConfigurationFields() 
    {
        $return = parent::getConfigurationFields();
        unset($return['skuColumn']);
        unset($return['channelIdColumn']);
        unset($return['mpnColumn']);
        unset($return['UpcColumn']);
        unset($return['documentDirectory']);
        unset($return['downloadDocument']);
        unset($return['skuregexp']);
        $return['channel']['options']['preferred_choices']=array('sx');
        return $return;
    }

}