<?php
namespace GescanPim\Bundle\ConnectorBundle\Processor\Pim;

use GescanPim\Bundle\ConnectorBundle\Processor\ProductProcessor as AbstractProductProcessor;

class ProductProcessor extends AbstractProductProcessor{
    
    protected $mappingCodeList= array(
                                    'attribute'=>array(),
                                    'value'=>array(),
                                    'unit'=>array()
                                );
    protected $counter = 0;
    
    public function getConfigurationFields() 
    {
        $return = parent::getConfigurationFields();
        unset($return['skuColumn']);
        unset($return['channelIdColumn']);
        unset($return['mpnColumn']);
        unset($return['upcColumn']);
        unset($return['skuregexp']);
        unset($return['createProduct']);
        $return['channel']['options']['preferred_choices']=array('pim');
        return $return;
    }
    
    protected function checkItem()
    {
        return true;
    }
    
    protected function getColumnValue($column)
    {
        return $this->item->$column;
    }
    
    protected function findProduct()
    {
        if ($this->findProductByChannelId((string)$this->item->SUPPLIER_PID)) return $this->product;
		if ($this->findProductByMpn((string)$this->item->PRODUCT_DETAILS->MANUFACTURER_PID)) return $this->product;
        return null;
    }
    
    protected function updateAttributes()
    {
        /********************** Static PRODUCT Attribute************************************/
        $name = (string)$this->item->PRODUCT_DETAILS->DESCRIPTION_SHORT;
        if ($name != '.') {
            $this->updateProductAttribute('name', $name);
        }
        
        $this->updateProductAttribute('description', (string)$this->item->PRODUCT_DETAILS->DESCRIPTION_LONG)
             ->updateProductAttribute('_channelId', (string)$this->item->SUPPLIER_PID)
             ->updateProductAttribute('manufacturer', (string)$this->item->PRODUCT_DETAILS->MANUFACTURER_NAME)
             ->updateProductAttribute('mpn', (string)$this->item->PRODUCT_DETAILS->MANUFACTURER_PID)
             ->updateProductAttribute('_etim_class', (string)$this->item->PRODUCT_FEATURES->REFERENCE_FEATURE_GROUP_ID);
             
        /********************** Family/Category PRODUCT************************************/
        //$this->updateCategory($this->item);
        /********************** PRODUCT Attribute ************************************/
        $this->updateFeature($this->item);
        /********************** Media Attribute ************************************/
        $this->updateDocument($this->item);
        return $this->product;
    }
	
    protected function updateCategory()
    {
        $categoryCode = (string)$this->item->PRODUCT_FEATURES->REFERENCE_FEATURE_GROUP_ID;
        if (!$categoryCode) return null;
        
        $category = $this->getCategory($categoryCode);
        if (!$category)return null;

        $this->product->addCategory($category);
    }
	
    protected function updateFeature ()
    {
        $features = $this->item->PRODUCT_FEATURES->FEATURE;
        if (!$features) return null;
        $features->rewind();
        while( $feature = $features->current()) {
            $value = null;
            switch($feature->FVALUE->count()){
                case 1:
                    $value = (string)$feature->FVALUE;
                    if($value != '-') break;
                    if(in_array(substr($value,0,2),array('EV','SV'))) $value =   $this->getMappingValue($value, 'value');
                    break;
					
                case 2 :
                    $value = (string)$feature->FVALUE[0].' - '.(string)$feature->FVALUE[1];
                    break;
					
                default;
                    print_r($feature->FVALUE->count()."\n");
                    $value = false;
                    continue;
            }
            
            if ($value && $value != '-') {
                if ((string)$feature->FUNIT) {
                    $unit = $this->getMappingValue((string)$feature->FUNIT, 'unit');
                    if ($unit) {
                        $value = $value.' '.$unit;
                    }
                } 
            }
            $attributeCode = (string)$feature->FNAME;
            
            $this->createAttributeList($attributeCode,$value, true, true);
            
            $attribute = $this->getMappingAttribute($attributeCode);
            if ($value && $value != '-' && $attribute) $this->updateProductAttribute($attribute, $value);
            $features->next();
        }
    }
	
    protected function updateDocument()
    {
        $medias = $this->item->MIME_INFO->MIME;
        if(!$medias) return null;
        $medias->rewind();
        $imagenb =1;
        while( $media = $medias->current()){
            $path = explode('_',(string)$media->MIME_SOURCE,2);
            $filename = $path[0].'/'.$path[1];

            switch(strtolower((string)$media->MIME_PURPOSE)){
                case 'normal':
                    if($this->updateProductAttribute('image_'.$imagenb,$filename)) $imagenb++;
                break;

                case 'thumbnail':
                    $this->updateProductAttribute('thumbnail',$filename);
                break;

                case 'safety data sheet':
                case 'safetydatasheet':
                case 'data sheet':
                case 'data_sheet':
                    if(substr((string)$media->MIME_TYPE,0,strlen('image'))=='image'){
                            if($this->updateProductAttribute('image_'.$imagenb,$filename)) $imagenb++;
                    }else{
                            $this->updateProductAttribute('spec_sheet',$filename);
                    }
                break;

                default:
                    if(substr((string)$media->MIME_TYPE,0,strlen('image'))=='image'){
                            if($this->updateProductAttribute('image_'.$imagenb,$filename)) $imagenb++;
                    }elseif((string)$media->MIME_TYPE =='application/pdf'){
                            $this->updateProductAttribute('spec_sheet',$filename);
                    }else{
                            print_r('unkown purpose: '.(string)$media->MIME_TYPE."\n");
                    }
                break;
            }
            $medias->next();
        }
    }
}