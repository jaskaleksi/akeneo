<?php
namespace GescanPim\Bundle\ConnectorBundle\Reader\File\Eleknet;

use Pim\Bundle\BaseConnectorBundle\Reader\File\CsvReader as PimCsvReader;
use Pim\Bundle\CatalogBundle\Validator\Constraints\File as AssertFile;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MagentoCsvProductReader
 *
 * @author ecoisne
 */
class ProductAttributeCsvReader extends PimCsvReader{
    /**
     * @AssertFile(
     *     groups={"Execution"},
     *     allowedExtensions={"csv", "zip"},
     *     mimeTypes={
     *         "text/x-c",
     *         "text/csv",
     *         "text/comma-separated-values",
     *         "text/plain",
     *         "application/csv",
     *         "application/zip"
     *     }
     * )
     */
    protected $filePath;
    protected $array = null;
    protected $loadArray = false;
    protected $filePathReader;
    public function read() {
        if($this->loadArray == false){
            $this->loadArray = true;
            $this->setFilePath($this->getfilePathReader());
            $this->array = array();
            while( $tmp = parent::read()){
                $key = trim(strtolower($tmp['attributeName']));
                if($tmp['prodID']){
                    $this->array[$key]['name']=$tmp['attributeName'];
                    $this->array[$key]['products'][$tmp['prodID']]=true;
                    $this->array[$key]['examples'][$tmp['attributeDesc']]=true;
                }
            }
        }
        
        return array_pop($this->array);
    }
    public function getFilePathReader(){
        return $this->filePathReader;
    }
    
    public function getFilePath() {
        return $this->getfilePathReader();
    }
    
    
    public function setFilePathReader($file){
        $this->filePathReader = $file;
        $this->filePath = $file;
                $this->csv = null;
        return $this;
    }
    public function getConfigurationFields() {
        $config = parent::getConfigurationFields();
        unset($config['filePath']);
        $config['filePathReader'] = array(
                'options' => array(
                    'label' => 'Attributes file',
                    'help'  => 'pim_base_connector.import.filePath.help'
                )
            );
        return $config;
    }
}
