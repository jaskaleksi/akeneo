<?php
namespace GescanPim\Bundle\ConnectorBundle\Reader\File;

use Pim\Bundle\BaseConnectorBundle\Reader\File\CsvReader as PimCsvReader;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CsvReader
 *
 * @author ecoisne
 */
class CsvReader extends PimCsvReader{
    //put your code here
    protected $filename;
    
    


    public function setFileName($filename){
        $this->filename = $filename;
    }
    
    public function setConfiguration(array $config) {
        if (array_key_exists('filePath'.$this->filename,$config)) {
            $config['filePath'] = $config['filePath'.$this->filename];
            
        }else{
            $config['filePath']='';
        }
        unset($config['filePath'.$this->filename]);
        parent::setConfiguration($config);
    }
    
    public function __get($name) {
        if($name=='filePath'.$this->filename){
            return $this->filePath;
        }
    }
    
    public function getConfiguration()
    {
        $result = array();
        foreach (array_keys($this->getConfigurationFields()) as $field) {
            if($field == 'filePath'.$this->filename){
                $result[$field] = $this->filePath;
            }else{
                $result[$field] = $this->$field;
            }
        }

        return $result;
    }


    public function getConfigurationFields()
    {
        return array(
            'filePath'.$this->filename => array(
                'options' => array(
                    'label' => 'File '.$this->filename,
                    'help'  => 'pim_base_connector.import.filePath.help'
                )
            ),
            'uploadAllowed' => array(
                'type'    => 'switch',
                'options' => array(
                    'label' => 'pim_base_connector.import.uploadAllowed.label',
                    'help'  => 'pim_base_connector.import.uploadAllowed.help'
                )
            ),
            'delimiter' => array(
                'options' => array(
                    'label' => 'pim_base_connector.import.delimiter.label',
                    'help'  => 'pim_base_connector.import.delimiter.help'
                )
            ),
            'enclosure' => array(
                'options' => array(
                    'label' => 'pim_base_connector.import.enclosure.label',
                    'help'  => 'pim_base_connector.import.enclosure.help'
                )
            ),
            'escape' => array(
                'options' => array(
                    'label' => 'pim_base_connector.import.escape.label',
                    'help'  => 'pim_base_connector.import.escape.help'
                )
            ),
        );
    }
}
