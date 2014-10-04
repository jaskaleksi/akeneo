<?php

namespace GescanPim\Bundle\ConnectorBundle\Processor\Eleknet;

use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;

class AttributeSetProcessor extends AbstractConfigurableStepElement implements
    ItemProcessorInterface,
    StepExecutionAwareInterface
{
    protected $enclosure = '"';
    protected $delimiter = ',';
    protected $categoryTree=array();
    protected $productManager = null;
    protected $writeHeader = true;
    
     protected function findProductsByChannelId($ids){
        $qb = $this->productManager->getProductRepository()->createQueryBuilder('p')
                            ->select('p')
                            ->join('p.values','v')
                            ->join('v.attribute', 'a');
        $qb->where('v.varchar IN (:ids)')
           ->setParameter('ids',$ids);
        $qb->andwhere('a.code = :code')
           ->setParameter('code','_channelId');
        $qb->andWhere('v.scope = :channel')
           ->setParameter('channel','eleknet');
        
        $results = $qb->getQuery()->execute();
        if (!$results instanceof \Iterator) {
            $results = new \ArrayIterator($results);
        }
        
        
        return  $results;
    }
    
    protected function buildCategory($tree,$parent=false){
        foreach ($tree as $val){
            if($parent){
                $this->categoryTree[$val->getCode()]['name']=$this->categoryTree[$parent->getCode()]['name'].'/'.$val->getTranslation('en_CA')->getLabel();
            }else{
                $this->categoryTree[$val->getCode()]['name']=$val->getTranslation('en_CA')->getLabel();
            }
            $this->categoryTree[$val->getCode()]['products'] = $val->getProductsCount();
            if($val->hasChildren()){
                $this->buildCategory($val->getChildren(),$val);
            }
        }
    }
    
    public function __construct(\Pim\Bundle\CatalogBundle\Entity\Repository\CategoryRepository $repository,$manager) {
        $this->productManager = $manager;
        $sxcategory = $repository->findByReference('sx');
        $this->buildCategory($sxcategory->getChildren());
        //var_dump($this->categoryTree);
        //die();
        
    }
    
    public function getConfigurationFields() {
        return array();
    }
    
    protected function getHeader(){
        $return = array();
        $return['name']= 'attribute';
        $return['example']='Examples';
        $return['total']='Nb Product';
        $return['uncategorized']='UnCategorized';
        foreach($this->categoryTree as $key=>$val){
            $return[$key]=$val['name'];
        }
        return $return;
        
    }
    
    protected function initResult($name, $examples=array(), $total=0){
        $return = array();
        $return['name']= $name;
        $return['example']=implode("\r\n",array_slice($examples,0,5));
        $return['total']=$total;
        $return['uncategorized']='';
        foreach($this->categoryTree as $key=>$val){
            $return[$key]='';
        }
        return $return;
        
    }
    protected function getCSV($array){
        static $fp = false;
        if ($fp === false){
            $fp = fopen('php://temp', 'r+'); // see http://php.net/manual/en/wrappers.php.php - yes there are 2 '.php's on the end.
            // NB: anything you read/write to/from 'php://temp' is specific to this filehandle
        }
        else{
            ftruncate($fp,0);
            rewind($fp);
        }
        if (fputcsv($fp, $array, $this->delimiter, $this->enclosure) === false){
            return false;
        }
        rewind($fp);
        $csv = stream_get_contents($fp);
        return $csv;
    }
    
    
    public function process($item) {
        //print_r($item);
        //die();
        //print_r($item);
        $products = $this->findProductsByChannelId(array_keys($item['products']));
        $return = $this->initResult($item['name'], array_keys($item['examples']), count($item['products']));
        while($product = $products->current()){
            $categories = $product->getCategories();
            if(count($categories)){
                foreach ($categories as $cat){
                    if(array_key_exists($cat->getCode(),$return)){
                        if($return[$cat->getCode()]==''){
                            $return[$cat->getCode()]=1;
                        }else{
                            $return[$cat->getCode()]+=1;
                        }
                    }
                }
            }else{
                if($return['uncategorized']==''){
                            $return['uncategorized']=1;
                        }else{
                            $return['uncategorized']+=1;
                        }
            }
                
                
            $products->next();
        }
        
        /*foreach ($this->categoryTree as $key=>$val){
            if($return[$key]!=''){
                $return[$key].='/'.$val['products'];
            }
        }*/
        if($this->writeHeader){
            $this->writeHeader=false;
            return $this->getCSV($this->getHeader()).$this->getCSV($return);
        }else{
            return $this->getCSV($return);
        }
    }

    public function setStepExecution(StepExecution $stepExecution) {
        
    }

}