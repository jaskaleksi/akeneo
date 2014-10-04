<?php
namespace GescanPim\Bundle\ConnectorBundle\Processor;

use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MappingProcessor
 *
 * @author ecoisne
 */
abstract class MappingProcessor extends AbstractConfigurableStepElement implements
    ItemProcessorInterface,
    StepExecutionAwareInterface
{
    protected $source;
    protected $type;
    
    protected $repository;
    
    public function __construct(\GescanPim\Bundle\ConnectorBundle\Entity\MappingCodeRepository $repository,$type,$source) {
        $this->repository = $repository;
        $this->type = $type;
        $this->source = $source;
    }
    
    public function getConfigurationFields() {
        return array();
    }

    public function process($item) {
        $code = $this->getCode($item);
        $mapping = $this->repository->getMappingValue($code, $this->type, $this->source);
        if(!$mapping){
            $mapping = new \GescanPim\Bundle\ConnectorBundle\Entity\MappingCode();
            $mapping->setCode($code);
            $mapping->setType($this->type);
            $mapping->setSource($this->source);
            $mapping->setInitialValue($this->getInitialValue($item));
        }
        return $mapping;
    }

    public function setStepExecution(StepExecution $stepExecution) {
        $this->stepExecution = $stepExecution;
    }
    
    protected abstract function getCode($item);

    
    protected abstract function getInitialValue($item);
    
    

//put your code here
}
