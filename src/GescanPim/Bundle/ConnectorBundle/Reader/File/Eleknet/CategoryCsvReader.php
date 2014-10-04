<?php
namespace GescanPim\Bundle\ConnectorBundle\Reader\File\Eleknet;

use Pim\Bundle\BaseConnectorBundle\Reader\File\CsvReader as PimCsvReader;

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
class CategoryCsvReader extends PimCsvReader{
    
    protected $filePathLevel2;
    protected $filePathLevel3;
    
    protected $level=1;
    
    public function getFilePathLevel2() {
        return $this->filePathLevel2;
    }
    
    public function getFilePathLevel3() {
        return $this->filePathLevel3;
    }
    
    
     public function setFilePathLevel2() {
        return $this->filePathLevel2;
    }
    
    public function setFilePathLevel3() {
        return $this->filePathLevel3;
    }
    
    public function getLevelFilePath() {
        $attribute = 'filePathLevel'.$this->level;
        return $this->$attribute;
    }
    
    public function read() {
        $data = parent::read();
        if(!$data &&$this->level < 3){
            $this->level = $this->level +1;
            $this->setFilePath($this->getLevelFilePath());
            return self::read();
        }else{
            return $data;
        }
    }

    public function getConfigurationFields()
    {
        return array(
            'filePath' => array(
                'options' => array(
                    'label' => 'File Level1',
                    'help'  => 'pim_base_connector.import.filePath.help'
                )
            ),
            'filePathLevel2' => array(
                'options' => array(
                    'label' => 'File Level2',
                    'help'  => 'pim_base_connector.import.filePath.help'
                )
            ),
            'filePathLevel3' => array(
                'options' => array(
                    'label' => 'File Level3',
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
