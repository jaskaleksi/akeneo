<?php
namespace GescanPim\Bundle\ConnectorBundle\Services;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MappingServices
 *
 * @author ecoisne
 */
class MappingServices {
    //put your code here
    
    /**
     *
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $em;
    
    protected $defaultChannelOrder=array(
        'user',
        'magento',
        'vendor',
        'pim',
        'web',
        'eleknet',
        'sx',
    );
    protected $attributeChannelOrder= array();
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
    }
    
    public function getChannelOrder($code){
        if(array_key_exists($code, $this->attributeChannelOrder)){
            return $this->attributeChannelOrder[$code];
        }else{
            return $this->defaultChannelOrder;
        }
    }
    
    public function getAllChannelOrder(){
        $return = $this->attributeChannelOrder;
        $return['default'] = $this->defaultChannelOrder;
        return $return;
        
    }
    
    public function getManufacturerList(){
        $return = array();
        $all = $this->em->getRepository('GescanPimConnectorBundle:Manufacturer')->findBy(array(), array('name' => 'ASC'));
        foreach($all as $val){
            $return[$val->getName()]=$val->getName();
        }
        return $return ;
    }
    
    public function getColorList(){
        $return = array();
        $all = $this->em->getRepository('GescanPimConnectorBundle:Color')->findBy(array(), array('name' => 'ASC'));
        foreach($all as $val){
            $return[$val->getName()]=$val->getName();
        }
        return $return ;
    }
    
}
