<?php
namespace GescanPim\Bundle\ConnectorBundle\Reader\Doctrine;

use Pim\Bundle\BaseConnectorBundle\Reader\Doctrine\Reader;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FakeProductReader
 *
 * @author ecoisne
 */
class AllProductReader extends Reader{
    
    protected $limit=null;
    
    protected $offset=null;
    
    protected $repository;
    
    protected $nbResult;
    
    protected $counter = 0;
    
    protected $maxResult=100;
    
    protected $enabled;
    
    protected $clearMemory;
    
    protected $em;
    
    protected $interval = null;
    

    //put your code here
    public function __construct(\Pim\Bundle\CatalogBundle\Doctrine\ORM\ProductRepository $repository,\Doctrine\ORM\EntityManager $em) {
        $this->repository = $repository;
        $this->em = $em;
    }
    
    public function getQueryBuilder($limit=false, $offset=false){
        $qb = $this->repository->createQueryBuilder('p');
        if($this->enabled=='1'){
            $qb->where('p.enabled=1');
        }elseif($this->enabled=='0'){
            $qb->where('p.enabled=0');
        }
        if($this->getDiffDate()){
            if(!$this->interval){
                $this->interval = new \DateTime();
                $this->interval->sub( new \DateInterval($this->getDiffDate()));
                print_r($this->interval->format(\Datetime::ISO8601 )."\n");
            }
            //$qb->expr()->gt("p.updated", ":date");
            $qb->andWhere('p.updated >= :date');
            $qb->setParameter('date',$this->interval);
            //print_r($qb->getQuery()->getSQL());
            //die();
        }        
        if(!$limit && !$offset && !$this->nbResult){
            $this->nbResult = $this->repository->createQueryBuilder('p')
                                                ->select('COUNT(p)')
                                                ->getQuery()
                                                ->getSingleScalarResult();
        }else{
            if($offset){
                $qb->setFirstResult($offset);
            }
            if($limit){
                $qb->setMaxResults($limit);
            }
        }
        
        return $qb;
    }


    public function getNextSetOfResult(){
        
        if($this->isClearMemory()){
            $this->em->clear();
        }
        $qb = $this->getQueryBuilder($this->maxResult,$this->current);
        $this->setQuery($qb->getQuery());
        $this->results = $this->getQuery()->execute();
        
        return count($this->results);
    }
    
    public function read(){
        if(!$this->query){
            $qb = $this->getQueryBuilder();
            $this->current = 0;
            if($this->nbResult>$this->maxResult){
                $qb = $this->getQueryBuilder($this->maxResult,0);
            }
            
            $this->setQuery($qb->getQuery());
            $this->results = $this->getQuery()->execute();
        }
        $result = null;
        
        if ($result = array_pop($this->results)) {
            $this->current++;
            $this->stepExecution->incrementSummaryInfo('read');
        }else{
            if($this->nbResult>$this->maxResult && $this->current<$this->nbResult){
                if($this->getNextSetOfResult()){
                    $this->current++;
                    $result = array_pop($this->results);
                }
            }
        }
        if($this->current%10000 == 0){
            print_r($this->current."\n");
        }
        return $result;
                
    }

    public function getMaxResult(){
        return $this->maxResult;
    }
    
    public function setMaxResult($limit){
        $this->maxResult = $limit;
        return $this;
    }
    
    public function isClearMemory(){
        return $this->clearMemory;
    }
    
    public function setClearMemory($limit){
        $this->clearMemory = $limit;
        return $this;
    }
    
    public function getEnabled(){
        return $this->enabled;
    }
    
    public function setEnabled($limit){
        $this->enabled = $limit;
        return $this;
    }
    
    protected $diffDate;
    public function getDiffDate(){
        return $this->diffDate;
    }
    
    public function setDiffDate($val){
        $this->diffDate = $val;
        return $this;
    }
    
    public function getConfigurationFields(){
        return array(
            'maxResult'     =>array(
                'options' => array(
                    'label' => 'limit',
                    'help'  => '')
                ),
            'clearMemory'     =>array(
                'type'    => 'switch',
                'options' => array(
                    'label' => 'Clear Memory each step',
                    'help'  => '')
                ),
            'enabled' =>array(
                'options' => array(
                    'label' => 'Product enabled?',
                    'help'  => '0 for none,1 for yes, other for both')
                ),
            'diffDate' =>array(
                'options' => array(
                    'label' => 'updated date',
                    'help'  => 'use an interval http://www.php.net/manual/en/dateinterval.format.php')
                )
            
        );
    }
}
