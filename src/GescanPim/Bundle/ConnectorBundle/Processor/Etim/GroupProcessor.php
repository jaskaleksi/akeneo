<?php
namespace GescanPim\Bundle\ConnectorBundle\Processor\Etim;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use Pim\Bundle\CatalogBundle\Entity\AttributeRequirement;
use Pim\Bundle\CatalogBundle\Entity\Category;
use Pim\Bundle\CatalogBundle\Entity\Family;
use Pim\Bundle\CatalogBundle\Entity\Repository\CategoryRepository;
use Pim\Bundle\CatalogBundle\Entity\Repository\ChannelRepository;
use Pim\Bundle\CatalogBundle\Entity\Repository\FamilyRepository;
/**
 * Description of EtimFamilyProcessor
 *
 * @author ecoisne
 */
class GroupProcessor extends AbstractConfigurableStepElement implements
    ItemProcessorInterface,
    StepExecutionAwareInterface
{
    /** @var StepExecution **/
    protected $stepExecution;
    
    protected $attribute= array(
        'sku'=>true,
        'upc'=>false,
        'title'=>false,
        'image_1'=>false,
        'description'=>false,
        'spec'=>false,
        'manufacturer'=>false,
        'mpn'=>false,
    );
    /**
     *
     * @var \Pim\Bundle\CatalogBundle\Entity\Repository\AttributeRepository
     */
    protected $familyRepository;
   
    /**
     * @var CategoryRepository
     */
    protected  $categoryRepository;
    
    /**
     * @var \Pim\Bundle\CatalogBundle\Entity\Repository\AttributeRepository
     */
    protected  $attributeRepository;
    /**
     * @var ChannelRepository
     */
    protected  $channelRepository;

    protected $type;
   
    
    public function __construct(FamilyRepository $familyRepository, 
                                CategoryRepository $categoryRepository,
                                \Pim\Bundle\CatalogBundle\Entity\Repository\AttributeRepository $attributeRepository,
                                ChannelRepository $channelRepository,
                                $type) {
        $this->familyRepository = $familyRepository;
        $this->categoryRepository = $categoryRepository;
        $this->attributeRepository = $attributeRepository;
        $this->channelRepository = $channelRepository;
        $this->type = $type;
    }
    
    public function getConfigurationFields() {
      return array();   
    }

    public function process($item) {
        $return = array();
        /*$family = $this->processFamily($item);
        
        if($family){
            $return[]= $family;
        }*/
        $category = $this->processCategory($item);
        if($category){
            $return[]= $category;
        }
        return $return;
    }
    
    public function processFamily($item){
        if(array_key_exists('ARTGROUPID', $item)&&$item['ARTGROUPID']){
            $family = $this->familyRepository->findOneByCode($item['ARTGROUPID']);
            if(!$family){
                $family = new Family();
                $family->setCode($item['ARTGROUPID'])
                        ->setLocale('en_CA')
                        ->setLabel($item['CA']);
                $family->setAttributeAsLabel($this->attributeRepository->getIdentifier());
                //$family->addAttribute($this->attributeRepository->getIdentifier());
                foreach($this->attribute as $code => $required){
                    $attribute = $this->attributeRepository->findByReference($code);
                    if($attribute){
                        $family->addAttribute($attribute);
                        $channels = $this->channelRepository->findAll();
                        foreach($channels as $channel){
                            $requirement = new AttributeRequirement();
                            $requirement->setAttribute($attribute)
                                        ->setChannel($channel)
                                        ->setRequired($required);
                             $family->addAttributeRequirement($requirement);
                            }
                       
                    }
                }
                $this->stepExecution->incrementSummaryInfo('added');
                return $family;
            }
            $this->stepExecution->incrementSummaryInfo('skip');
             
        }else{
            $this->stepExecution->incrementSummaryInfo('NULL');
            $this->stepExecution->addError('noEID', "The code for the family is null", array(), $item);
            
        }
    }
    public function processCategory($item){
 
        if(array_key_exists('ARTGROUPID', $item)&&$item['ARTGROUPID']){
            $parent = $this->categoryRepository->findByReference('pim');
            if($parent){
                $category = $this->categoryRepository->findByReference($item['ARTGROUPID']);
                if(!$category){
                    $category = new Category();
                    $category->setCode($item['ARTGROUPID'])
                            ->setLocale('en_CA')
                            ->setLabel($item['CA']);
                    $category->setParent($parent);
                    $this->stepExecution->incrementSummaryInfo('added');
                    return $category;
                }
                $this->stepExecution->incrementSummaryInfo('skip');
            }else{
                $this->stepExecution->addError('noparent', "Catalog does not exist", array());
            }
             
        }else{
            $this->stepExecution->incrementSummaryInfo('skip');
            $this->stepExecution->addError('noEID', "The code for the family is null", array(), $item);
            
        }
    }

    public function setStepExecution(StepExecution $stepExecution) {
        $this->stepExecution = $stepExecution;
    }

//put your code here
}
