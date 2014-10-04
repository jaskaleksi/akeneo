<?php
namespace GescanPim\Bundle\ConnectorBundle\Processor;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CSVProductProcessor
    extends ProductProcessor
{
    protected $attributesSeparator = '|';
    public function getAttributesSeparator(){return $this->attributesSeparator;}
    public function setAttributesSeparator($val){$this->attributesSeparator=$val;return $this;}

    protected $valuesSeparator = '=';
    public function getValuesSeparator(){return $this->valuesSeparator;}
    public function setValuesSeparator($val){$this->valuesSeparator=$val;return $this;}

    protected function checkItem()
    {
        if (is_array($this->item)) return true;
        return false;
    }
    
    protected function updateAttributes()
    {
        foreach ($this->item as $key=>$val) {

            if(!$val||!$key) continue;

            switch($key){
                case $this->getSkuColumn() :
                break;

                case $this->getChannelIdColumn() :
                    $this->updateProductAttribute('_channelId', $val);
                break;

                case $this->getUpcColumn() :
                    $this->updateProductAttribute('upc', $val);
                break;

                case $this->getMpnColumn() :
                    $this->updateProductAttribute('mpn', $val);
                break;

                default:
                    $attributeCode = $this->getMappingValue($key, 'attribute', true, true);
                    if (!$attributeCode){
                        $this->addUnusedValue($key, $val);
                        break;
                    }
                    if($attributeCode == 'attributes'){
                        //print_r($this->getValuesSeparator());

                        $attributes = explode($this->getAttributesSeparator(),$val);
                        foreach($attributes as $string){

                            if(!$string || !strpos($string,$this->getValuesSeparator()))continue;

                            list($attributeCode,$value) = explode($this->getValuesSeparator(),$string,2);
                            $attributeCode = trim($attributeCode);
                            $value = trim($value);
                            $this->createAttributeList($attributeCode, $value, true,true);
                            $attribute = $this->getMappingValue($attributeCode, 'attribute', true, true);

                            if ($attribute == false){
                                $this->addUnusedValue($attributeCode, $value);
                                continue;
                            }
                            $this->removeUnusedValue($attributeCode);
                            $this->updateProductAttribute($attribute, $value);
                        }
                    }else{
                        $this->createAttributeList($key, $val, true,true);
                        $this->removeUnusedValue($key);
                        $this->updateProductAttribute($attributeCode, $val);
                    }
                break;
            }
        }
        
        $this->productManager->handleMedia($this->product);
        return $this->product;
    }
    
    protected function getColumnValue($column)
    {
         if ($this->columnExist($column,$this->item)) {
             return $this->item[$column];
         }
         return null;
    }
    
    protected function columnExist($column, $array)
    {
            return $column && array_key_exists($column,$array) && $array[$column] ;
    }

    public function getConfigurationFields()
    {
        $return  = parent::getConfigurationFields();
        $return['attributesSeparator'] = array(
            'options' => array(
                'label' => 'Attributes separator',
                'help'  => 'Character used to separate the different attributes in a field.(separate with | if more)'
            )
        );
        $return['valuesSeparator'] = array(
            'options' => array(
                'label' => 'Value separator',
                'help'  => 'Character used to separate the attribute and the value.(separate with | if more)'
            )
        );
        return $return;
    }
}