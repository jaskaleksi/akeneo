<?php

namespace GescanPim\Bundle\ConnectorBundle\Processor\Sx;

use GescanPim\Bundle\ConnectorBundle\Processor\ProductProcessor as AbstractProductProcessor;

class ProductProcessor extends AbstractProductProcessor{
    
    protected function checkItem()
    {
        if ($this->item->prod) return true;
        $this->log('no Sku', array(), 'error');
        return false;
    }
    
    protected function getColumnValue($column)
    {
        return $this->item->$column;
    }
    
    protected function findProduct()
    {
        return $this->findProductBySku((string)$this->item->prod);
    }
    protected function updateAttributes()
    {
        /********************** Static PRODUCT Attribute************************************/
        $this->updateProductAttribute('mpn', (string)$this->item->lookupnm) ;
        $this->updateProductAttribute('name', (string)$this->item->descrip_1);
        $this->updateProductAttribute('description', (string)$this->item->descrip_2);
        if (in_array($this->item->statustype,array('I','L')) || (string)$this->item->tbl_evnt == 'D') {
            $this->product->setEnabled(false);
        } else {
            $this->product->setEnabled(true);
        }

        return $this->product;
    }

    public function getConfigurationFields() 
    {
        $return = parent::getConfigurationFields();
        unset($return['skuColumn']);
        unset($return['channelIdColumn']);
        unset($return['mpnColumn']);
        unset($return['upcColumn']);
        unset($return['documentDirectory']);
        unset($return['downloadDocument']);
        unset($return['skuregexp']);
        unset($return['createAttributeList']);
        $return['channel']['options']['preferred_choices']=array('sx');
        return $return;
    }

}