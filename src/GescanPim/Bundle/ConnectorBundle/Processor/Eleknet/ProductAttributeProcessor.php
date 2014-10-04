<?php

namespace GescanPim\Bundle\ConnectorBundle\Processor\Eleknet;

use GescanPim\Bundle\ConnectorBundle\Processor\ProductProcessor as AbstractProductProcessor;

class ProductAttributeProcessor 
    extends AbstractProductProcessor
{
    
    protected function checkItem()
    {
        if (is_array($this->item)) return true;
        return false;
    }
    
    protected function getColumnValue($column)
    {
        return $this->item[$column];
    }
    
    protected function updateAttributes()
    {
        
        if (!$this->item['attributeName'] || !$this->item['attributeDesc']) return null;
        $this->createAttributeList($this->item['attributeName'],$this->item['attributeDesc'], true, true);
        $attribute = $this->getMappingAttribute( $this->item['attributeName'] );
        
        if ($attribute) {
            $this->updateProductAttribute( $attribute, $this->item['attributeDesc'] );
        }
        return $this->product;
    }
    
    protected function findProduct()
    {
        if (!$this->item['prodID']) return null;
        return $this->findProductByChannelId( $this->item['prodID'] );
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
        unset($return['createProduct']);
        unset($return['skuregexp']);
        $return['channel']['options']['preferred_choices']=array('eleknet');
        return $return;
    }

}