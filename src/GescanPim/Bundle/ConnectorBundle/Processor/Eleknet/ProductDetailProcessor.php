<?php

namespace GescanPim\Bundle\ConnectorBundle\Processor\Eleknet;

use GescanPim\Bundle\ConnectorBundle\Processor\ProductProcessor as AbstractProductProcessor;

class ProductDetailProcessor
    extends AbstractProductProcessor
{
    
    protected $treatedProduct = array();

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
        $this->product = null;
        if(!$this->item['prod'] || array_key_exists($this->item['prod'], $this->treatedProduct)) return null;
        $this->treatedProduct[$this->item['prod']] = true;
		if (!$this->findProductBySku($this->item['prod'])) return null;
        return $this->product;
    }
    protected function updateAttributes()
    {
        $this->updateProductAttribute('unspsc', $this->item['unspsc'])
             ->updateProductAttribute('_channelId', $this->item['ProdID'])
             ->updateProductAttribute('description', $this->item['longdesc']);
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
        $return['channel']['options']['preferred_choices']=array('eleknet');
        return $return;
    }

}