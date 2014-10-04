<?php

namespace GescanPim\Bundle\ConnectorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AttributeList
 *
 * @ORM\Table(name="gescan_attribute_list")
 * @ORM\Entity(repositoryClass="GescanPim\Bundle\ConnectorBundle\Entity\AttributeListRepository")
 */
class AttributeList
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
     * @ORM\Column(name="sku", type="string", length=255)
     */
    private $sku;

    /**
     * @var integer
     * 
     * @ORM\Column(name="gescan_mapping_code_id", type="integer")
     */
    private $mappingCodeId;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=500)
     */
    private $value;


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
     * Set sku
     *
     * @param string $sku
     * @return AttributeList
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string 
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set mappingCode
     *
     * @param integer $mappingCode
     * @return AttributeList
     */
    public function setMappingCodeId($mappingCode)
    {
        $this->mappingCodeId = $mappingCode;

        return $this;
    }

    /**
     * Get mappingCode
     *
     * @return integer 
     */
    public function getMappingCodeId()
    {
        return $this->mappingCodeId;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return AttributeList
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
}
