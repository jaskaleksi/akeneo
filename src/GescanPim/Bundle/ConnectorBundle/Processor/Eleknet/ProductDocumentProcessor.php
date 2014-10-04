<?php

namespace GescanPim\Bundle\ConnectorBundle\Processor\Eleknet;

use GescanPim\Bundle\ConnectorBundle\Processor\ProductProcessor as AbstractProductProcessor;

class ProductDocumentProcessor 
	extends AbstractProductProcessor
{
    protected $imageNumber = array();
    
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
        if(!$this->item['ProdID']) return null;
		if(!$this->findProductByChannelId($this->item['ProdID'])) return null;
        return $this->product;
    }
    
    protected function updateAttributes()
    {
        switch($this->item['doctype']){
            case 'IMAGE':
                    $currentImage = array_key_exists($this->item['ProdID'], $this->imageNumber)?$this->imageNumber[$this->item['ProdID']]+1:1;
                    if($currentImage>10) return null;
                    $this->updateProductAttribute('image_'.$currentImage, $this->getUploadFile($this->item['LinkUrl']));
                    break;

            case 'DOC':
                    $this->updateProductAttribute('spec_sheet', $this->getUploadFile($this->item['LinkUrl']));
                    break;
                
            case 'THUMB':
            default:
                    $this->product = null;
                    break;
        }
	return $this->product ;
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