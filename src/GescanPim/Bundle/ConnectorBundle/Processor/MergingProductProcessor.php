<?php
namespace GescanPim\Bundle\ConnectorBundle\Processor;

use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FakeProductProcessor
 *
 * @author ecoisne
 */
class MergingProductProcessor extends AbstractConfigurableStepElement implements
    ItemProcessorInterface,
    StepExecutionAwareInterface 
{
    
    protected $stepExecution;
    
    protected $merger;
    
    /**
     *
     * @var \Symfony\Bridge\Monolog\Logger 
     */
    protected $logger ;
    
    public function __construct(\GescanPim\Bundle\ConnectorBundle\Event\FinalChannelListener $merger, \Doctrine\ORM\EntityManager $em, \Symfony\Bridge\Monolog\Logger $logger) {
       $this->merger = $merger ;
       $this->merger->setEntityManager($em);
       $this->logger = $logger;
    }

    public function getConfigurationFields() {
        return array();
    }
    
    protected $counter = 0;
    
    protected function echo_memory_usage() { 
        $mem_usage = memory_get_usage(true); 
        
        if ($mem_usage < 1024) 
            return  $mem_usage." bytes"; 
        elseif ($mem_usage < 1048576) 
            return round($mem_usage/1024,2)." kilobytes"; 
        else 
            return round($mem_usage/1048576,2)." megabytes"; 
            
        echo "<br/>"; 
    } 

    public function process($item) {
        $this->counter++;
        if($this->counter%100 == 0){
            $this->logger->info(
                sprintf('Product count: %s, Memory Usage: %s', $this->counter,$this->echo_memory_usage())
            );
            //print_r($this->counter.' Products: '.$this->echo_memory_usage()."\n");
        }
        //print_r($item->getIdentifier()."\n");
        try{
        $this->merger->mergeFinalChannel($item);
        $this->merger->completeScoring($item);
        }catch(Exception $e){
            print_r($e->getMessage());
        }
        return $item;
    }

    public function setStepExecution(StepExecution $stepExecution) {
        $this->stepExecution = $stepExecution;
    }

//put your code here
}
