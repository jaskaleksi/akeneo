<?php

namespace GescanPim\Bundle\ConnectorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MappingCode
 * 
 * @ORM\Table(name="gescan_mapping_code")
 * @ORM\Entity(repositoryClass="GescanPim\Bundle\ConnectorBundle\Entity\MappingCodeRepository")
 */
class MappingCode
{
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
     * @ORM\Column(name="code", type="string", length=500)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", nullable=true, length=100)
     */
    private $source;
    
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=100)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="initial_value", nullable=true, type="string", length=500)
     */
    private $initialValue;

    /**
     * @var string
     *
     * @ORM\Column(name="value", nullable=true, type="string", length=255)
     */
    private $value;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="ignored", type="boolean",options={"default" = false})
     */
    private $ignored;
    
    private $user;
   /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return MappingCode
     */
    public function setCode($code)
    {
        $this->code = strtolower($code);

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return MappingCode
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }
    
    /**
     * Set type
     *
     * @param string $type
     * @return MappingCode
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set initialValue
     *
     * @param string $initialValue
     * @return MappingCode
     */
    public function setInitialValue($initialValue)
    {
        $this->initialValue = $initialValue;

        return $this;
    }

    /**
     * Get initialValue
     *
     * @return string 
     */
    public function getInitialValue()
    {
        return $this->initialValue;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return MappingCode
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }
    
    
    /**
     * Set user
     *
     * @param string $value
     * @return MappingCode
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
    
    public function isIgnored(){
        return $this->ignored;
    }
    
    public function setIgnored($bool){
        $this->ignored = $bool;
        return $this;
    }
    
    public function __toString() {
        return $this->getCode()." => ".$this->getValue();
    }
}
