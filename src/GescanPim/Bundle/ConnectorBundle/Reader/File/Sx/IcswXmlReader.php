<?php

namespace GescanPim\Bundle\ConnectorBundle\Reader\File\Sx;

use Akeneo\Bundle\BatchBundle\Item\ItemReaderInterface;
use Pim\Bundle\BaseConnectorBundle\Reader\File\FileReader;

/**
 * Description of PimXmlProductReader
 *
 * @author ecoisne
 */
class IcswXmlReader extends \Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement implements ItemReaderInterface {
    
    
    protected $item = null;
    
    protected $current = 0;
    
    protected $filePath;

    /**
     * Get the file path
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Set the file path
     *
     * @param string $filePath
     *
     * @return FileReader
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }


    public function read()
    {
        if (null === $this->item) {
            $this->item = array();
            $files = explode(',',$this->filePath);
            $wrongProduct= array();
            foreach($files as $file){
                // for exemple purpose, we should use XML Parser to read line per line
                $xml = simplexml_load_file($file, 'SimpleXMLIterator');
                //var
                $path = 'tt-icswRow';
                $xml = $xml->$path;
                $xml->rewind();
                $wrongProduct= array();
                while($current = $xml->current()){
                    $sku = (string)$current->prod;
                    $warehouse = (string)$current->whse;
                    $qty = floatval((string)$current->qtyonhand)-floatval((string)$current->qtyunavail);
                    if($qty<0){
                        $qty = 0;
                        $wrongProduct[$sku.' '.$warehouse]= true;
                        //die($file.' '.$sku.' '.$warehouse.' '.$qty.'negatif');
                    }
                    if(!array_key_exists($sku, $this->item)){
                        $this->item[$sku]= array('sku'=>$sku, 'TOTAL'=>0);
                    }
                    $this->item[$sku]['TOTAL']+=$qty;
                    if(array_key_exists($warehouse,$this->item[$sku])){
                        die($sku.' '.$warehouse.' already exists');
                    }
                    $this->item[$sku][$warehouse]=$qty;        
               
                    $xml->next();
                }
            }
        }
       /* print_r($wrongProduct);
        print_r("\n");
        var_dump(count($this->item));
        die();*/
        $return = array_pop($this->item);
        //print_r($return['sku']."\n");
        return $return;
    }

    public function getConfigurationFields()
    {
        return array(
            'filePath' => array(
                'options' => array(
                    'label' => 'gescan_pimconnector.steps.import.filePath.label',
                    'help'  => 'if more than 1 file, separate by a ,'
                )
            ),
        );
    }
}