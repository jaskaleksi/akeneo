<?php

namespace GescanPim\Bundle\ConnectorBundle\Processor\Eleknet;

use GescanPim\Bundle\ConnectorBundle\Processor\ProductProcessor as AbstractProductProcessor ;

class ProductProcessor 
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
        $sku = $this->item['Prod'];
		if (!$sku||array_key_exists($sku, $this->treatedProduct)) return null;
		
		if (!$this->findProductBySku($sku)) return null;
        return $this->product;
    }
    
    protected function updateAttributes()
    {
        $this->updateProductAttribute('name', $this->item['Description'])
             ->updateProductAttribute('upc', $this->item['UPC'])
             ->updateProductAttribute('mpn', $this->item['VendorPartNumber'])
             ->updateProductAttribute('manufacturer', $this->item['VendorName'])
        ;
        
        /********************** CATEGORY ***************************************************/
        $categoryCode = 'sx_'.$this->item['Category'];
        if($categoryCode){
            $category = $this->getCategory($categoryCode);
            if($category){
                $this->product->addCategory($category);
            }else{
                $this->stepExecution->addWarning('unknow_category', 'The category does not exist', array(), $categoryCode);
            }
        }
	
        return $this->product;
    }
	
	public function getConfigurationFields() {
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
    
    public function getCategory($code){
        $category = parent::getCategory($code);
        if(!$category && strlen($code)>2){
            $category = $this->getCategory(substr($code,0,-1));
            if($category){
                $this->categoryList[$code]=$category;
            }
        }
        return $this->categoryList[$code];
    }

}