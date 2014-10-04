<?php
namespace GescanPim\Bundle\ConnectorBundle\Processor;

use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use GescanPim\Bundle\ConnectorBundle\Entity\MappingCodeRepository;
use GescanPim\Bundle\ConnectorBundle\Entity\MappingCode;
use Pim\Bundle\CatalogBundle\Entity\Repository\FamilyRepository;
use Pim\Bundle\CatalogBundle\Manager\CategoryManager;
use Pim\Bundle\CatalogBundle\Manager\ProductManager;
use Pim\Bundle\CatalogBundle\Model\ProductInterface;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class AbstractProductProcessor extends AbstractConfigurableStepElement implements
    ItemProcessorInterface,
    StepExecutionAwareInterface
{
    /** @var StepExecution */
    protected $stepExecution;
    
    /** @var ProductManager */
    protected $productManager;
    
     /** @var CategoryManager */
    protected $categoryManager;
    
    /** @var FamilyRepository */
    protected $familyRepository;
    
    /** @var MappingCodeRepository */
    protected $mappingCodeRepository;
    
    /** @var ProductInterface **/
    protected $product = null;
    protected $item = null;
    protected $categoryList= array();
    protected $attributeList= array();
    protected $mappingCodeList= array('attribute'=>array());

    protected $scope=null;
    
    public function __construct(ProductManager $manager, 
                                CategoryManager $categoryManager, 
                                FamilyRepository $familyRepository,
                                MappingCodeRepository $mappingCodeRepository)
    {
        $this->productManager = $manager;
        $this->categoryManager = $categoryManager;
        $this->familyRepository = $familyRepository;
        $this->mappingCodeRepository = $mappingCodeRepository;
        $this->scope = $this->getScope();
    }
   
    protected function getMapping($code, $type){
        if (array_key_exists($code, $this->mappingCodeList[$type])) {
        	return $this->mappingCodeList[$type][$code];
	}

        $mapping = $this->mappingCodeRepository->findOneBy(array('code'=>$code, 'type'=>$type, 'source'=>$this->getScope()));
        if(!$mapping){
	
            $mapping = new MappingCode();
            $mapping->setCode($code);
            $mapping->setInitialValue($code);
            $mapping->setType($type);
            $mapping->setSource($this->getScope());
	    if($type == 'attribute'){
		$mapping->setValue($this->guessAttributeMapping($code));
	    }
            $this->mappingCodeRepository->getDoctrineEntityManager()->persist($mapping);
            $this->mappingCodeRepository->getDoctrineEntityManager()->flush($mapping);
        }

        if ($mapping->getValue()) {
            // attributes is a special case as it indicates that the contents have to be further processed
		if ( $type=='attribute' && $mapping->getValue() != 'attributes' ){
			$this->mappingCodeList[$type][$code] = $this->getAttribute($mapping->getValue());
		}else{
            		$this->mappingCodeList[$type][$code] = $mapping->getValue();
		}
        } else {
            $this->mappingCodeList[$type][$code] = false;
        }
        return $this->mappingCodeList[$type][$code];
    }
    
    protected function guessAttributeMapping($code){
        $mapping2 = $this->mappingCodeRepository->findOneBy(array('code'=>$code, 'type'=>'attribute'));
        if ($mapping2 && $mapping2->getValue()) {
            return $mapping2->getValue();
        } else {
            $query = $this->productManager->getAttributeRepository()->createQueryBuilder('a')
                                                                        ->leftJoin('a.translations', 'translation')
                                                                        ->where('translation.label = :code')
                                                                        ->setParameter('code', $code)
                                                                        ->setMaxResults(1);
            $attribute = $query->getQuery()->getOneOrNullResult();
            if($attribute){
                return $attribute->getCode();
            }else{
                return null;
            }
        }
    }

    protected function getMappingAttribute($code) {
        if (!array_key_exists($code, $this->mappingCodeList['attribute'])) {
            $mapping = $this->getMapping($code, 'attribute');
        }
        return $this->mappingCodeList['attribute'][$code];
        
    }
    
    protected function updateProductAttribute($attributeCode, $value, $scope=true){
        
        if($scope){
            $scope = $this->scope;
        }
        if(is_string($attributeCode)){
            $Attribute = $this->getAttribute($attributeCode);
        }else{
            $Attribute = $attributeCode;
            $attributeCode = $attributeCode->getCode();
        }
        if($Attribute){
            if(!$this->product->getValue($attributeCode,null,$scope)){
                if($value){
                    $Value   = $this->productManager->createProductValue();
                    $Value->setAttribute($Attribute);
                    if($scope){
                        $Value->setScope($scope);
                    }
                    $Value->setData($value);
                    $this->product->addValue($Value);
                }
            }else{
                $this->product->getValue($attributeCode, null, $scope)->setData($value);
            }
        }else{
            $this->stepExecution->addWarning('unknow_attribute', 'The attribute does not exist', array(), array($attributeCode));
        }
        return $this;
    }
    
    protected function getFile($file){
        if(substr($file,0,4)!='http'){
            $file = $this->getImagePath().$file;
        }
        if(substr($file,0,4)=='http'){
           //$file =  rawurldecode($file) ;
            $file=str_replace(' ','%20', $file);
            $ctx = stream_context_create(array( 
                'http' => array( 
                    'timeout' => 60 
                    ) 
                ) 
            ); 
        }else{ 
            $ctx=null;
            if(!is_file($file)){
                 $this->stepExecution->addWarning('file_not_find', 'the file cannot be find', array(), array('product'=>$this->product->getIdentifier(),'file'=>$file));
                return  null;
            }
        }

        if($ctx){
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $filename = sys_get_temp_dir ().'/'.$this->getMappingScope().'-'.md5($file);
            if(strlen($ext)<5){
            $filename=$filename.'.'.$ext;
            }
            if(!is_file($filename)){
                $content = @file_get_contents($file, false, $ctx);
                if($content){
                    file_put_contents($filename, file_get_contents($file));
                }
                else{
                    $this->stepExecution->addWarning('file_not_find', 'the file cannot be find', array(), array('product'=>$this->product->getIdentifier(),'file'=>$file));
                    return  null;
                }

            }
        }else{
            $filename = $file;
        }

        return new \Symfony\Component\HttpFoundation\File\File($filename, false);
    }
    
    protected function updateProductFileAttribute($attributeCode, $value, $scope=true){
        
        if($scope){
            $scope = $this->scope;
        }
        if(is_string($attributeCode)){
            $Attribute = $this->getAttribute($attributeCode);
        }else{
            $Attribute = $attributeCode;
            $attributeCode = $attributeCode->getCode();
        }
        if(!is_file($value)){
            $this->stepExecution->addWarning('file', 'The file does not exist', array(), array($value));
        }
        if($Attribute){
            if(!$this->product->getValue($attributeCode,null,$scope)){
                if($value){
                    $Value   = $this->productManager->createProductValue();
                    $Value->setAttribute($Attribute);
                    if($scope){
                        $Value->setScope($scope);
                    }
                    $media = new \Pim\Bundle\CatalogBundle\Model\Media();
                    $file = $this->getFile($value);
                    if($file){
                        $media->setFile($file);
                    }
                    
                    $Value->setMedia($media);
                    $this->product->addValue($Value);
                }
            }else{
                $media = $this->product->getValue($attributeCode, null, $scope)->getMedia();
                if(!$media){
                    $media = new \Pim\Bundle\CatalogBundle\Model\Media();
                     $this->product->getValue($attributeCode, null, $scope)->setMedia($media); 
                }
                $file = $this->getFile($value);
                if($file){
                    $media->setFile($file);
                }
            }
            $this->productManager->handleMedia($this->product);
        }else{
            $this->stepExecution->addWarning('unknow_attribute', 'The attribute does not exist', array(), array($attributeCode));
        }
        return $this;
    }
    
    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }
    
    
    
    public function getAttribute($code){
        if(!array_key_exists($code, $this->attributeList)){
            $attribute = $this->productManager->getAttributeRepository()->findByReference($code);
            $this->attributeList[$code]=$attribute;
        }
        return $this->attributeList[$code];
    }

    abstract public function getScope();
    abstract protected function getProduct();
    abstract protected function updateProduct();
    
    public function process($item) {
        $this->item = $item;
        $this->product = null;
        if($this->getProduct()){
            $this->updateProduct();
        }
        return $this->product;
    }
    
    public function getConfigurationFields() {
        return array();
    }
    
    public function setScope($scope){
        $this->scope = $scope;
    }
    
    protected function getProductByAttributeValue($value, $attributeCode, $scope=true){
        $query   = $this->productManager->getProductRepository()->createQueryBuilder('p')
                            ->select('p')
                            ->join('p.values','v')
                            ->join('v.attribute', 'a')
                            ->where('a.code = :code')
                            ->andWhere('v.varchar = :value')
                            ->setParameter('code',$attributeCode)
                            ->setParameter('value', $value)
                            ;
        if($scope){
           $query->andWhere('v.scope = :channel')
                 ->setParameter('channel',$this->getScope());
        }
        $result = $query->getQuery()->getResult();
        if(count($result)>1){
            $this->product=null;
            $this->stepExecution->addWarning('product', count($result).' product was found', array(), array('attribute code'=>$attributeCode,'value'=>$value, 'channel'>$scope?$this->getScope():''));
        }elseif(count($result)==1){
            $this->product = $result[0];
        }else{
            $this->product=null;
        }
        return  $this->product;
    }
    
    protected function getProductByChannelId($value){
        return $this->getProductByAttributeValue($value, '_channelId');
    }
}
