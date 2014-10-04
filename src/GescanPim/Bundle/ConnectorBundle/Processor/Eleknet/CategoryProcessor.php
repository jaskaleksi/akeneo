<?php
namespace GescanPim\Bundle\ConnectorBundle\Processor\Eleknet;
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
class CategoryProcessor extends AbstractConfigurableStepElement implements
    ItemProcessorInterface,
    StepExecutionAwareInterface
{
    /** @var StepExecution **/
    protected $stepExecution;

    /**
     * @var CategoryRepository
     */
    protected  $categoryRepository;
    
    protected $categoryList = array();
    
    public function __construct(CategoryRepository $categoryRepository) {     
        $this->categoryRepository = $categoryRepository;
    }
    
    public function getConfigurationFields() {
      return array();   
    }

    public function process($item) {
        return $this->processCategory($item);
    }
    
    public function getCategory($code){
        if(!array_key_exists($code, $this->categoryList)){
            $category = $this->categoryRepository->findByReference($code);
            if(!$category){
                $category=new Category();
                $category->setCode($code);
            }
            $this->categoryList[$code]=$category;
        }
        return  $this->categoryList[$code];
    }
    
    public function processCategory($item){
        if(array_key_exists('prodcat', $item)&&$item['prodcat']){
            $parentcode = 'sx'; 
            if(strlen($item['prodcat'])>2){
                $parentcode .= '_'.substr($item['prodcat'],0,-1);
            }
            $parent = $this->getCategory($parentcode);
            if($parent){
                $category = $this->getCategory('sx_'.$item['prodcat']);
                if(!$category->getId()){
                    $category->setLocale('en_CA')
                             ->setLabel($item['descr_en']);
                    $category->setParent($parent);
                    return $category;
                }
                $this->stepExecution->incrementSummaryInfo('skip');
            }else{
                print_r("\nparrent not found\n");
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
