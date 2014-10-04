<?php
namespace GescanPim\Bundle\ConnectorBundle\Reader\File\Magento;

use Pim\Bundle\BaseConnectorBundle\Reader\File\CsvReader;

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
class CsvProductReader extends CsvReader{
    //put your code here
    protected $nextLine=false;
    /**
     * {@inheritdoc}
     */
    public function read()
    {
        $continue = true;
        $data = true;
        $return = $this->nextLine;
        if($this->nextLine===false){
            $this->nextLine = parent::read();
        }
        $line = 1;
        while($continue&&$this->nextLine){
            $data = parent::read();
            if(is_array($data)&&!$data['sku']){
                $line++;
                if($data['_media_image']){
                    $this->nextLine['image_'.$line]=$data['_media_image'];
                }
            }else{
                $return = $this->nextLine;
                $continue = false;
                $this->nextLine = $data;
            }
        }
        return $return;
        
    }
}
