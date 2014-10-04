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
use Pim\Bundle\CatalogBundle\Entity\Repository\AttributeGroupRepository;
use Pim\Bundle\CatalogBundle\Manager\AttributeManager;
/**
 * Description of EtimFamilyProcessor
 *
 * @author ecoisne
 */
class MappingProcessor extends AbstractConfigurableStepElement implements
    ItemProcessorInterface,
    StepExecutionAwareInterface
{
    /** @var StepExecution **/
    protected $stepExecution;
    
    
    /**
     *
     * @var \GescanPim\Bundle\ConnectorBundle\Entity\MappingCodeRepository
     */
    protected $Repository;
    
    
    
    public function __construct(\GescanPim\Bundle\ConnectorBundle\Entity\MappingCodeRepository $Repository) {
        $this->Repository = $Repository;
    }
    protected $idColumn;
    protected $valueColumn;
    protected $mappingType;
    public function getIdColumn(){return $this->idColumn;}
    public function setIdColumn($val){$this->idColumn = $val;return $this;}
    public function getValueColumn(){return $this->valueColumn;}
    public function setValueColumn($val){$this->valueColumn = $val;return $this;}
    public function getMappingType(){return $this->mappingType;}
    public function setMappingType($val){$this->mappingType = $val;return $this;}
    public function getConfigurationFields() {
        return array('idColumn' => array(
                            'options' => array(
                                'label' => 'Id column',
                                'help'  => ''
                            )
                    ),
                    'valueColumn' => array(
                            'options' => array(
                                'label' => 'Value column',
                                'help'  => ''
                            )
                    ),
                    'mappingType' => array(
                            'options' => array(
                                'label' => 'Mapping Type',
                                'help'  => ''
                            )
                    ),
                    );   
    }

    public function process($item) {
        if(array_key_exists($this->getIdColumn(), $item)&&$item[$this->getIdColumn()]){
            $mapping = $this->Repository->getMappingValue($item[$this->getIdColumn()], $this->getMappingType(),'pim');
            if($mapping){
                $mapping->setInitialValue($item[$this->getValueColumn()]);
                if($this->getMappingType() == 'attribute'&&!$mapping->getValue()&&!$mapping->isIgnored()){
                    $guess = $this->Repository->guessMapping($item[$this->getValueColumn()], $this->getMappingType());
                    if($guess instanceof \GescanPim\Bundle\ConnectorBundle\Entity\MappingCode){
                        $mapping->setValue($guess->getValue());
                        $mapping->setIgnored($guess->isIgnored());
                    }else{
                        $mapping->setValue($guess);
                    }
                    if($mapping->getValue()){
                        $mapping->setUser('system');
                    }
                }elseif($this->getMappingType() != 'attribute'){
                     $mapping->setValue($item[$this->getValueColumn()]);
                    
                }
                return $mapping;
            } 
            return null;
             
        }else{
            $this->stepExecution->incrementSummaryInfo('NULL');
            $this->stepExecution->addError('noID', "The code for the family is null", array(), $item);
            
        }
        return;
        
    }

    public function setStepExecution(StepExecution $stepExecution) {
        $this->stepExecution = $stepExecution;
    }

//put your code here
}
