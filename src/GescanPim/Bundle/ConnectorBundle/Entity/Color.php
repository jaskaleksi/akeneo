<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace GescanPim\Bundle\ConnectorBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * MappingCode
 *
 * 
 * @ORM\Table(name="gescan_color")
 * @ORM\Entity
 * @UniqueEntity(fields="name", message="This color is already existing.")
 */
class Color {
    //put your code here
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255 , unique=true, nullable=false)
     */
    private $name;
    
    public function getId(){
        return $this->id;
    }
    
    public function getName(){
        return $this->name;
    }
     public function setName($name){
        $this->name = $name;
        return $this;
    }
    
    public function __toString() {
        return $this->getName()?$this->getName():'New';
    }
            
}
