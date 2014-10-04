<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of XmlReader
 *
 * @author ecoisne
 */
namespace GescanPim\Bundle\ConnectorBundle\Reader\File;

use Akeneo\Bundle\BatchBundle\Item\ItemReaderInterface;
use Pim\Bundle\BaseConnectorBundle\Reader\File\FileReader;

class XmlReader extends FileReader implements ItemReaderInterface {
    
    protected $xml;
    
    protected $table;

    public function read()
    {
        if (null === $this->xml) {
            // for exemple purpose, we should use XML Parser to read line per line
            $this->xml = simplexml_load_file($this->filePath, 'SimpleXMLIterator');
            //var
            $path = $this->getXmlPath();
            $this->table = $this->xml->xpath($path);
            //print_r($this->table);
            //die();
            
        }
        $item = array_pop($this->table);
        /*if($item){
            $item = new \SimpleXMLElement($item->asXML());
        }*/
        return $item;
    }
    
    protected $xmlPath;
    public function getXmlPath(){
        return $this->xmlPath;
    }
    public function setXmlPath($path){
        $this->xmlPath = $path;
        Return $this;
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
            'xmlPath' => array(
                'options' => array(
                    'label' => 'Item Xml XPath',
                    'help'  => 'Item Xml XPath'
                )
            ),
        );
    }
}