<?php

namespace GescanPim\Bundle\ConnectorBundle\Reader\File\Pim;

use Akeneo\Bundle\BatchBundle\Item\ItemReaderInterface;
use Pim\Bundle\BaseConnectorBundle\Reader\File\FileReader;
use Pim\Bundle\CatalogBundle\Manager\CategoryManager;
/**
 * Description of PimXMLFeaturesReader
 *
 * @author ecoisne
 */
class XmlFeaturesReader extends FileReader implements ItemReaderInterface{
    
    protected $array= null;
    
    
    /**
     *
     * @var CategoryManager 
     */
    protected $categoryManager;

    public function __construct(CategoryManager $categoryManager) {
        $this->categoryManager = $categoryManager;
    }
    public function read()
    {
        if (null === $this->array) {
            // for exemple purpose, we should use XML Parser to read line per line
            $this->array= array();
            $xml = simplexml_load_file($this->filePath, 'SimpleXMLIterator');
            $xml= $xml->T_NEW_CATALOG->PRODUCT;
            $xml->rewind();
            $classtogroup=array();
            while($val= $xml->current()){
                $categoryCode = (string)$val->PRODUCT_FEATURES->REFERENCE_FEATURE_GROUP_ID;
                //print_r("\n".$categoryCode."\n");
                if(!array_key_exists($categoryCode, $classtogroup)){
                    $classtogroup[$categoryCode]=false;
                    $category = $this->categoryManager->getCategoryByCode($categoryCode);
                    if($category && $category->getParent()){
                        $classtogroup[$categoryCode]=$category->getParent()->getCode();
                    }
                }
                if($classtogroup[$categoryCode]!==false){
                    $features = array();
                    $xmlfeature = $val->PRODUCT_FEATURES->FEATURE;
                    $xmlfeature->rewind();
                    while($featureCode = $xmlfeature->current()){
                        $features[(string)$featureCode->FNAME]= true;
                        $xmlfeature->next();
                    }
                
                    if(array_key_exists($classtogroup[$categoryCode],$this->array)){
                        foreach($this->array[$classtogroup[$categoryCode]] as $attribute=>$val){
                            if(!array_key_exists($attribute, $features)){
                                unset($this->array[$classtogroup[$categoryCode]][$attribute]);
                            }
                        }
                    }else{
                        $this->array[$classtogroup[$categoryCode]]=$features;
                    }
                }
                
                $xml->next();
            }
            print_r($this->array);
            die();
        }

        if ($data = $this->xml->current()) {
            $this->xml->next();
            return $data;
        }

        return null;
    }
    
    public function getConfigurationFields()
    {
        return array(
            'filePath' => array(
                'options' => array(
                    'label' => 'gescan_pimconnector.steps.import.filePath.label',
                    'help'  => 'gescan_pimconnector.steps.import.filePath.help'
                )
            ),
        );
    }
}
