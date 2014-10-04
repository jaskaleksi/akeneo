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
use Pim\Bundle\CatalogBundle\Entity\Category;
use Pim\Bundle\CatalogBundle\Entity\Repository\CategoryRepository;
/**
 * Description of EtimFamilyProcessor
 *
 * @author ecoisne
 */
class ClassProcessor extends AbstractConfigurableStepElement implements
    ItemProcessorInterface,
    StepExecutionAwareInterface
{
    /** @var StepExecution **/
    protected $stepExecution;
    
    protected $attribute= array(
        'upc',
        'name',
        'image_1',
        'description',
        'spec',
        'manufacturer',
        'mpn',
    );

   
    /**
     * @var CategoryRepository
     */
    protected  $categoryRepository;

    protected $type;
   
    
    public function __construct(CategoryRepository $categoryRepository) {
        $this->categoryRepository = $categoryRepository;
    }
    
    public function getConfigurationFields() {
      return array();   
    }

    public function process($item) {
        $category = $this->processCategory($item);
        return $category;
    }
    
    public function processCategory($item){
 
        if(array_key_exists('GroupID', $item)&&$item['GroupID']&&array_key_exists('ARTCLASSID', $item)&&$item['ARTCLASSID']){
            $parent = $this->categoryRepository->findByReference($item['GroupID']);
            if($parent){
                $category = $this->categoryRepository->findByReference($item['ARTCLASSID']);
                if(!$category){
                    $category = new Category();
                    $category->setCode($item['ARTCLASSID'])
                            ->setLocale('en_CA')
                            ->setLabel($item['CA']);
                    $category->setParent($parent);
                    $this->stepExecution->incrementSummaryInfo('added');
                    return $category;
                }
                $this->stepExecution->incrementSummaryInfo('skip');
            }else{
                $this->stepExecution->addError('noparent', "Up category does not exist", array(),$item);
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
