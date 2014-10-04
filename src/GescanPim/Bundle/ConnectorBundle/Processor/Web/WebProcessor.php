<?php

namespace GescanPim\Bundle\ConnectorBundle\Processor\Web;

use GescanPim\Bundle\ConnectorBundle\Processor\ProductProcessor;
use GescanPim\Bundle\ConnectorBundle\Services\Encoding;
use GescanPim\Bundle\ConnectorBundle\Entity\MappingCodeRepository;
use GescanPim\Bundle\ConnectorBundle\Entity\MappingCode;
use Pim\Bundle\CatalogBundle\Entity\Repository\FamilyRepository;
use Pim\Bundle\CatalogBundle\Manager\CategoryManager;
use Pim\Bundle\CatalogBundle\Manager\ProductManager;
use Pim\Bundle\CatalogBundle\Manager\ChannelManager;

class WebProcessor
    extends ProductProcessor
{
    
    protected function checkItem() { return true; }
    
    protected function getColumnValue($column)
    {
        $values = $this->item->xpath(trim($column));
        if (count($values) == 0) return null;
        
        return (string)$values[0];
    }

    protected function updateAttributes() {
        $paths = explode('|',$this->getColumnXPath()) ;
        $attributeNamePath = explode('|',$this->getAttributeNameXPath()) ;
        $attributeValuePath = explode('|',$this->getAttributeValueXPath()) ;

        if (count($paths)!==count($attributeNamePath)
            &&count($paths)!==count($attributeValuePath))  throw \RuntimeException( sprintf(
                                                                                        'Xpath Configurtation is wrong: Column Path %s, Attribute Path %s, Value Path %s',
                                                                                        count($paths),
                                                                                        count($attributeNamePath),
                                                                                        count($attributeValuePath)
                                                                                    ));

        foreach ($paths as $key => $path) {
            $columns = $this->item->xpath(trim($path));
            while (list( , $col) = each($columns)) {
                $attributeCode = $col->xpath(trim($attributeNamePath[$key]));
                if (count($attributeCode) == 0)  continue ;

                $attributeCode=(string)$attributeCode[0];
                if (!$attributeCode) continue;
                
                if ($attributeValuePath[$key]){
                    $value = $col->xpath(trim($attributeValuePath[$key]));
                    if (count($value) == 0) continue;

                    $value=(string)$value[0];
                }else{
                    $value = (string)$col;
                }
                
                $this->createAttributeList($attributeCode, $value, true,true);
                
                $attributeCode = $this->getMappingAttributeCode($attributeCode);
                
    		if (!$attributeCode ) continue;  

                if ($attributeValuePath[$key]){
                    $value = $col->xpath(trim($attributeValuePath[$key]));
                    if (count($value) == 0) continue;

                    $value=(string)$value[0];
                }else{
                    $value = (string)$col;
                }

                if(!$value) continue;
                
                switch ($attributeCode) {
                    case 'attributes':
                        $attributes = explode($this->getAttributesSeparator(),$value);
                        foreach($attributes as $string){
                            if(!$string || !strpos($string, $this->getValuesSeparator())) continue;
                            list($attribute,$value) = explode($this->getValuesSeparator(),$string,2);
                            $this->createAttributeList($attribute, $value, true,true);
                            $attribute = $this->getMappingAttribute($attribute);
    			            if ($attribute == false) continue;
                            $this->updateProductAttribute($attribute, $value);
                        }
                        break;
                        
                    default:
                        $this->updateProductAttribute($attributeCode, $value);
                        break;
                }
            }
        }
        return $this->product;
    }

    protected $attributesSeparator = '|';
    public function getAttributesSeparator(){return $this->attributesSeparator;}
    public function setAttributesSeparator($val){$this->attributesSeparator=$val;return $this;}

    protected $valuesSeparator = '=';
    public function getValuesSeparator(){return $this->valuesSeparator;}
    public function setValuesSeparator($val){$this->valuesSeparator=$val;return $this;}

    protected $columnXPath;
    public function getColumnXPath(){return $this->columnXPath;}
    public function setColumnXPath($val){$this->columnXPath=$val;return $this;}

    protected $attributeNameXPath;
    public function getAttributeNameXPath(){return $this->attributeNameXPath;}
    public function setAttributeNameXPath($val){$this->attributeNameXPath=$val;return $this;}

    protected $attributeValueXPath;
    public function getAttributeValueXPath(){return $this->attributeValueXPath;}
    public function setAttributeValueXPath($val){$this->attributeValueXPath=$val;return $this;}


    public function getConfigurationFields() 
    {
        $return  = parent::getConfigurationFields();
        $return['skuColumn']['options']['help'] =  'SKU XPath';
        $return['upcColumn']['options']['help'] =  'UPC XPath';
        $return['mpnColumn']['options']['help'] =  'MPN XPath';
        $return['columnXPath'] = array(
                'options' => array(
                    'label' => 'Column XPath',
                    'help'  => 'Xpath to find the product columns.(separate with | if more)'
                )
            );
        $return['attributeNameXPath'] = array(
                'options' => array(
                    'label' => 'Attribute Name XPath',
                    'help'  => 'Xpath to find the name of the attribute.(separate with | if more)'
                )
            );
        $return['attributeValueXPath'] = array(
                'options' => array(
                    'label' => 'Attribute Value XPath',
                    'help'  => 'Xpath to find the value of the attribute.(separate with | if more)'
                )
            );
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
