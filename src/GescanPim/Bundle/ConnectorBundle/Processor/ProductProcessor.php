<?php
namespace GescanPim\Bundle\ConnectorBundle\Processor;

use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use GescanPim\Bundle\ConnectorBundle\Event\FinalChannelListener;

use Pim\Bundle\CatalogBundle\Manager\ProductManager;
use GescanPim\Bundle\ConnectorBundle\Entity\MappingCodeRepository;
use GescanPim\Bundle\ConnectorBundle\Entity\AttributeListRepository;
use Pim\Bundle\CatalogBundle\Manager\ChannelManager;
use Pim\Bundle\CatalogBundle\Entity\Repository\FamilyRepository;
use Pim\Bundle\CatalogBundle\Manager\CategoryManager;
use Pim\Bundle\CatalogBundle\Model\Media;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bridge\Monolog\Logger;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductProcessor
 *
 * @author ecoisne
 */
abstract class ProductProcessor
    extends AbstractConfigurableStepElement 
    implements ItemProcessorInterface, StepExecutionAwareInterface
{
    
    /** @var StepExecution */
    protected $stepExecution;
    /** @var Logger */
    private $logger;
    
    
    /*********************** ATTRIBUTES FOR CACHING VALUES ***********************************/
    protected $attributeList = array();
    protected $mappingCodeList = array();
    protected $mappingCodeIdList = array();
    protected $categoryList = array();
    
    /*********************** CURRENT PRODUCT *************************************************/
    /** @var ProductInterface **/
    protected $product = null;
    protected $item;
    
    /*********************** REPOSITORY & MANAGER ********************************************/
    /** @var ProductManager */
    protected $productManager;
    /** @var MappingCodeRepository */
    protected $mappingCodeRepository;
    /**  @var ChannelManager  */
    protected $channelManager;
    /**  @var FamilyRepository  */
    protected $familyRepository;
    /**  @var FinalChannelListener  */
    protected $finalChannelManager;
    /**  @var AttributeListRepository */
    protected $attributeListRepository;
    
    /*********************** BASE CONFIGURATION **********************************************/
    protected $skuColumn;
    public function getSkuColumn(){return $this->skuColumn;}
    public function setSkuColumn($skuColumn){$this->skuColumn= $skuColumn;return $this;}
    
    protected $checkManufacturer;
    public function getCheckManufacturer(){return $this->checkManufacturer;}
    public function setCheckManufacturer($checkManufacturer){$this->checkManufacturer= $checkManufacturer;return $this;}
    
    protected $manufacturerColumn;
    public function getManufacturerColumn(){return $this->manufacturerColumn;}
    public function setManufacturerColumn($manufacturerColumn){$this->manufacturerColumn= $manufacturerColumn;return $this;}
    
    protected $skuregexp;
    public function getSkuregexp(){return $this->skuregexp;}
    public function setSkuregexp($skuregexp){$this->skuregexp= $skuregexp;return $this;}
    
    protected $channel;
    public function getChannel(){return $this->channel;}
    public function setChannel($channel){$this->channel = $channel;}
    
    protected $channelIdColumn;
    public function getChannelIdColumn(){return $this->channelIdColumn;}
    public function setChannelIdColumn($channelIdColumn){$this->channelIdColumn = $channelIdColumn;return $this;}
    
    protected $mpnColumn;
    public function getMpnColumn(){return $this->mpnColumn;}
    public function setMpnColumn($mpnColumn){$this->mpnColumn = $mpnColumn;return $this;}
    
    protected $upcColumn;
    public function getUpcColumn(){return $this->upcColumn;}
    public function setUpcColumn($upcColumn){$this->upcColumn = $upcColumn;return $this;}
    
    protected $channelMapping;
    public function setChannelMapping($channelMapping){$this->channelMapping = $channelMapping; return $this;}
    public function getChannelMapping(){return $this->channelMapping?$this->channelMapping:$this->channel;}

    protected $documentDirectory;
    public function setDocumentDirectory($documentDirectory){$this->documentDirectory = $documentDirectory; return $this;}
    public function getDocumentDirectory(){return $this->documentDirectory;}
    
    protected $downloadDocument;
    public function setDownloadDocument($downloadDocument){$this->downloadDocument = $downloadDocument; return $this;}
    public function getDownloadDocument(){return $this->downloadDocument;}
    
    protected $createProduct;
    public function setCreateProduct($createProduct){$this->createProduct = $createProduct; return $this;}
    public function getCreateProduct(){return $this->createProduct;}
    
    protected $loggerCount = 100;
    public function setLoggerCount($loggerCount){$this->loggerCount = $loggerCount; return $this;}
    public function getLoggerCount(){return $this->loggerCount;}

    protected $updateFinalChannel = true;
    public function setUpdateFinalChannel($updateFinalChannel){$this->updateFinalChannel = $updateFinalChannel; return $this;}
    public function isUpdateFinalChannel(){return $this->updateFinalChannel;}
    
    protected $createAttributeList =  false;
    public function setCreateAttributeList($createAttributeList){$this->createAttributeList = $createAttributeList; return $this;}
    public function isCreateAttributeList(){return $this->createAttributeList;}

    protected $saveProduct = true;
    public function setSaveProduct($saveProduct){$this->saveProduct = $saveProduct; return $this;}
    public function isSaveProduct(){return $this->saveProduct;}

    protected $logProduct = true;
    public function setLogProduct($logProduct){$this->logProduct = $logProduct; return $this;}
    public function isLogProduct(){return $this->logProduct;}

    protected $logProductNotFound = true;
    public function setLogProductNotFound($logProductNotFound){$this->logProductNotFound = $logProductNotFound; return $this;}
    public function isLogProductNotFound(){return $this->logProductNotFound;}

    protected $addUnusedAttribute = false;
    public function setAddUnusedAttribute($addUnusedAttribute){$this->addUnusedAttribute = $addUnusedAttribute; return $this;}
    public function isAddUnusedAttribute(){return $this->addUnusedAttribute;}

    public function getConfigurationFields() 
    {
        return array(
            'channel' => array(
                'type'    => 'choice',
                'options' => array(
                    'choices'  => $this->channelManager->getChannelChoices(),
                    'required' => true,
                    'select2'  => true,
                    'label'    => 'pim_base_connector.export.channel.label',
                    'help'     => 'pim_base_connector.export.channel.help'
                )),
            'skuColumn' => array(
                'options' => array(
                    'label' => 'Sku Column',
                    'help'  => ''
                )
            ),
            'channelIdColumn' => array(
                'options' => array(
                    'label' => 'Channel Id Column',
                    'help'  => ''
                )
            ),
            'mpnColumn' => array(
                'options' => array(
                    'label' => 'MPN Column',
                    'help'  => ''
                )
            ),
            'upcColumn' => array(
                'options' => array(
                    'label' => 'UPC Column',
                    'help'  => ''
                )
            ),
            'channelMapping' => array(
                'options' => array(
                    'label' => 'Channel Mapping name',
                    'help'  => 'This is the name of the channel used to map column to attributes. If not set the channel will be used.'
                )
            ),
            'documentDirectory' => array(
                'options' => array(
                    'label' => 'Document directory/url',
                    'help'  => ''
                )
            ),
            'downloadDocument' => array(
                'type'    => 'switch',
                'options' => array(
                    'label' => 'Download Documents',
                    'help'  => ''
                )
            ),
            'createProduct' => array(
                'type'    => 'switch',
                'options' => array(
                    'label' => 'Create product',
                    'help'  => 'If not Exist, the product will be created. only if sku column is provided.'
                )
            ),
            'checkManufacturer' => array(
                'type'    => 'switch',
                'options' => array(
                    'label' => 'Check if the manufacturer is mapped',
                    'help'  => ''
                )
            ),
            'manufacturerColumn' => array(
                'options' => array(
                    'label' => 'Manufacturer Column',
                    'help'  => 'Used only if the manufacturer is checked'
                )
            ),
            'skuregexp' => array(
                'options' => array(
                    'label' => 'SKU Regexp',
                    'help'  => 'The product find sdku must matche this regexp'
                )
            ),
            'loggerCount' => array(
                'options' => array(
                    'label' => 'Logger range',
                    'help'  => 'Log will be generated every X product'
                )
            ),
            'updateFinalChannel' => array(
                'type'    => 'switch',
                'options' => array(
                    'label' => 'Update Final Channel',
                    'help'  => ''
                )
            ),
            'createAttributeList' => array(
                'type'    => 'switch',
                'options' => array(
                    'label' => 'Create the list of attribute by sku',
                    'help'  => ''
                )
            ),
            'addUnusedAttribute' => array(
                'type'    => 'switch',
                'options' => array(
                    'label' => 'Add unused attribute',
                    'help'  => 'Add unused Attribute as a json object'
                )
            ),
            'saveProduct' => array(
                'type'    => 'switch',
                'options' => array(
                    'label' => 'Save the Product',
                    'help'  => 'Enable to run the data without saving product. Just to update the mappings.'
                )
            ),
            'logProduct' => array(
                'type'    => 'switch',
                'options' => array(
                    'label' => 'Add product data to the log',
                    'help'  => ''
                )
            ),
            'logProductNotFound' => array(
                'type'    => 'switch',
                'options' => array(
                    'label' => 'Log product not found',
                    'help'  => 'Log the product that could not be mapped to a Sku.'
                )
            ),

        );
    }

    public function __construct(ProductManager $manager, 
                                CategoryManager $categoryManager,
                                FamilyRepository $familyRepository,
                                MappingCodeRepository $mappingCodeRepository,
                                AttributeListRepository $attributeListRepository,
                                ChannelManager $channelManager,
                                FinalChannelListener $finalChannelManager,
                                Logger $logger)
    {
        
        $this->productManager = $manager;
        $this->categoryManager = $categoryManager;
        $this->familyRepository = $familyRepository;
        $this->mappingCodeRepository = $mappingCodeRepository;
        $this->attributeListRepository = $attributeListRepository;
        $this->channelManager = $channelManager;
        $this->finalChannelManager = $finalChannelManager;
        $this->logger = $logger;
    }
    
    
    protected function createAttributeList($code,$value, $createIfnotExist= false, $guess = false)
    {
        
        if (!$this->isCreateAttributeList()) return false;
        $type = 'attribute';
        if (!array_key_exists($code . '_' . $type . '_' . $this->getChannelMapping(), $this->mappingCodeIdList) ||
            !$this->mappingCodeIdList[$code . '_' . $type . '_' . $this->getChannelMapping()]) {
            
            $this->getMappingValue($code, $type, $createIfnotExist, $guess);
            
            
            if (!array_key_exists($code . '_' . $type . '_' . $this->getChannelMapping(), $this->mappingCodeIdList) ||
                !$this->mappingCodeIdList[$code . '_' . $type . '_' . $this->getChannelMapping()]) return false;
        }
        $this->attributeListRepository->createOrUpdate($this->product->getIdentifier()->getData(),$this->mappingCodeIdList[$code . '_' . $type . '_' . $this->getChannelMapping()],$value);
        return true;
    }
    

    protected function getMappingValue($code, $type, $createIfnotExist= false, $guess = false)
    {
        if (!array_key_exists($type, $this->mappingCodeList)||!array_key_exists($code, $this->mappingCodeList[$type])){
            $mapping = $this->mappingCodeRepository->getMapping($code, $type, $this->getChannelMapping(), $createIfnotExist,$guess);

            if ($mapping && $createIfnotExist){
                if (!$mapping->getId()){
                    if (!$mapping->getValue()){
                        
                        $this->stepExecution->addWarning('new_attribute', 'The attribute mapping does not exist', array(), array('attribute'=>$code));
                    }
                    if ($type != 'sku') $this->log('New Attribute', array(  'Channel'=>$this->getChannelMapping(),
                                                                            'Type' => $type,
                                                                            'Code'=>$code,
                                                                            'Mapping'=>$mapping->getValue()));
                    $this->mappingCodeRepository->getDoctrineEntityManager()->persist($mapping);
                    $this->mappingCodeRepository->getDoctrineEntityManager()->flush($mapping);
                }
            }
            if ($mapping) $this->mappingCodeIdList[$code . '_' . $type . '_' . $this->getChannelMapping()] = $mapping->getId();
            $this->mappingCodeList[$type][$code] = ( $mapping ) ? (!$mapping->isIgnored()? $mapping->getValue(): false) : null;
            /* Clear a bit of momory */
            $this->mappingCodeRepository->getDoctrineEntityManager()->detach($mapping);
            unset($mapping);
        }
        return $this->mappingCodeList[$type][$code];
        
    }

    public function getMappingAttributeCode($val)
    {
        $mapping = $this->getMappingValue($val, 'attribute', true, true);
        if (!$mapping) return null;
        if ($mapping == 'attributes') return $mapping;

        return $this->getAttribute($mapping) ? $this->getAttribute($mapping)->getCode() : null;
    }

    public function getMappingAttribute($val)
    {
        $mapping = $this->getMappingValue($val, 'attribute', true, true);
        
        if (!$mapping) return null;
        return $this->getAttribute($mapping);
    }
    
    public function getCategory($code)
    {
        if (!array_key_exists($code, $this->categoryList)){
            $category = $this->categoryManager->getCategoryByCode($code);
            $this->categoryList[$code]=$category;
        }
        return $this->categoryList[$code];
    }
    
    /************************* METHODS **********************************************************/
    protected function findProductBySku($sku)
    {
        $this->product = null;

        if (!$sku) return null;

        $product   = $this->productManager->getProductRepository()->findOneBy([ ['attribute' => $this->productManager->getIdentifierAttribute(), 'value' => $sku]] );

        if (!$product && !$this->getCreateProduct()) return null;

        if (!$product) {
            $this->product = $this->productManager->createProduct();
            $this->updateProductAttribute($this->productManager->getIdentifierAttribute(), $sku, null);
            $this->itemCreatedCounter++;
        } else {
            $this->itemFoundBySkuCounter++;
            $this->product = $product;
        }
        return $this->product;
    }
    
    protected function findProductByMpn($val)
    {
        $this->product = null;
        if ($this->findProductByAttributeValue('mpn', $val, false)
            || $this->findProductByAttributeValue('mpn', str_replace(array('-',' '),array('',''),$val), false)) {
                $this->itemFoundByMpnCounter ++;
        }
        return $this->product;
    }
    
    protected function findProductByUpc($val) 
    {
        $this->product = null;
        if ($this->findProductByAttributeValue('upc', $val, false)) $this->itemFoundByUpcCounter++;
        return $this->product ;
    }

    protected function findProductByChannelId($val)
    {
        $this->product = null;
        if ($this->findProductByAttributeValue('_channelId', $val, true)) $this->itemFoundByChannelIdCounter++;
        return $this->product ;
    }


    private $manufacturerError = array();

    private function findProductByAttributeValue($attributeCode,$value, $useScope=true)
    {
        $query   = $this->productManager->getProductRepository()->createQueryBuilder('p')
                            ->select('p')
                            ->join('p.values','v')
                            ->join('v.attribute', 'a')
                            ->where('a.code = :code')
                            ->andWhere('v.varchar = :value')
                            ->setParameter('code',$attributeCode)
                            ->setParameter('value', $value)
                            ;
        if ($useScope) {
           $query->andWhere('v.scope = :channel')
                 ->setParameter('channel',$this->getChannel());

        }
        $result = $query->getQuery()->getResult();
        
        if(count($result)==0) return null;
        
        if ($this->skuregexp) {
            $tmp = array();
           foreach ($result as $key => $product) {
               if (preg_match($this->skuregexp, $product->getIdentifier()->getData())) {
                   $tmp[$product->getId()] = $product;
               }
           }
            $result = $tmp;
            unset($tmp);
        }
        
        if ( ($attributeCode == 'mpn' || $attributeCode == 'upc')&&
            $this->getCheckManufacturer() && 
            $this->getManufacturerColumn()){
            if (!$this->getColumnValue($this->getManufacturerColumn())){
                $this->log('Manufacturer could not be checked because it is empty');
                return null;
            }
           
            $manufacturer = $this->getMappingValue($this->getColumnValue($this->getManufacturerColumn()),'manufacturer',true, true);
            
            if (!$manufacturer){
                if ($manufacturer!==false && !array_key_exists($this->getColumnValue($this->getManufacturerColumn()),$this->manufacturerError)) {
                    $this->manufacturerError[$this->getColumnValue($this->getManufacturerColumn())]=true;
                    $this->log('Manufacturer "'.$this->getColumnValue($this->getManufacturerColumn()).'" could not be checked because it is not mapped');
                }
                return null;
            }

            $tmp = array();
            foreach ($result as $key => $product) {
                if ($product->getValue('manufacturer', null, 'final') &&
                    $product->getValue('manufacturer', null, 'final')->getData() &&
                    $product->getValue('manufacturer', null, 'final')->getData() == $manufacturer) {
                    $tmp[$product->getId()] = $product;
                }
            }
            $result = $tmp;
            unset($tmp);
            $tmp = array();
            /** REMOVE RENTAL, KIT and PRO FROM AUTOMATIC UPDATE */
            foreach ($result as $key => $product) {
                if (!preg_match('/.{3}(RENT|KIT|PRO).*/', $product->getIdentifier()->getData())) {
                    $tmp[$product->getId()] = $product;
                }
            }
            $result = $tmp;
            unset($tmp);
        }


        switch (count($result)) {
            case 0:
                return null;

            case 1:
                $this->product = array_pop($result);
                return $this->product;
                
            default:
                $skus = array();
                foreach ( $result as $val){
                    $skus[] = $val->getIdentifier()->getData();
                }
                $this->log('Multiple Matching by '.$attributeCode, array(   'attribute' =>$attributeCode,
                                                                            'value'     =>$value,
                                                                            'channel'   =>$this->getChannel(),
                                                                            'SKU Found' => implode(' | ',$skus)
                ),'warning');
                return null;
        }
        return  null;
    }



    public function getAttribute($code)
    {
        if(!$code) throw \Exception('No given code for attribute');
        if (!is_string($code)) {
            //print_r($code."\n");
            $this->attributeList[$code->getCode()]=$code;
            return $code;
        } else {
            if (!array_key_exists($code, $this->attributeList)) {
                $attribute = $this->productManager->getAttributeRepository()->findByReference($code);
                $this->attributeList[$code]=$attribute;
            }
        }
        return $this->attributeList[$code];
    }

    protected function getFile($file)
    {
        if ($this->documentDirectory && substr($file,0,4)!='http') {
            $file = $this->documentDirectory.$file;
        }
        if (substr($file,0,4)=='http') {
           //$file =  rawurldecode($file) ;
            $file=str_replace(' ','%20', $file);
            $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 60
                    )
                )
            );
        } else {
            $ctx=null;
        }

        if ($this->downloadDocument) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $filename = sys_get_temp_dir ().'/'.$this->getChannelMapping().'-'.md5($file);
            if (strlen($ext)<5) {
                $filename=$filename.'.'.$ext;
            }
            if (!is_file($filename)) {
                $content = @file_get_contents($file, false, $ctx);
                if ($content) {
                    file_put_contents($filename, file_get_contents($file));
                } else {
                    $this->stepExecution->addWarning('file_not_find', 'the file cannot be find', array(), array('product'=>$this->product->getIdentifier(),'file'=>$file));
                    return  false;
                }
            }
        } else {
            $filename = $file;
        }

        if(!is_file($filename)){
            return false;
        }

        return new File($filename, false);
    }

    protected function updateProductAttribute($attributeCode, $value)
    {
        if ( !$value ) return $this;
        /* Check the attribute */
        $Attribute = $this->getAttribute($attributeCode);
        if ($Attribute == null) return $this;

        $scope = $Attribute->isScopable()?$this->channel : null;

        /** Specific treatement for attribute of type media **/
        if ($Attribute->getBackendType()=='media') {
            $media = false;
            if ($this->product->getValue($Attribute->getCode(),null,$scope)&&
               $this->product->getValue($Attribute->getCode(), null, $scope)->getMedia()
		    ){
               $media = $this->product->getValue($Attribute->getCode(), null, $scope)->getMedia();
            } else {
                $media = new Media();
            }
            $file = $this->getFile($value);
            if ($file) {
                $media->setFile($file);
                $value = $media;
            } else {
                /** file does not exist **/
                return $this;
            }
        }

        if (!$this->product->getValue($Attribute->getCode(),null,$scope)) {
            /** Creation of the attribute value **/
            $Value   = $this->productManager->createProductValue();
            $Value->setAttribute($Attribute);
            $Value->setScope($scope);
            $Value->setData($value);
            $this->product->addValue($Value);
            $this->mappingCodeRepository->getDoctrineEntityManager()->persist($Value);

        } else {
            $this->product->getValue($Attribute->getCode(), null, $scope)->setData($value);
        }

        return $this;
    }

    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
        return $this;
    }
    
    protected function get_memory_usage() { 
        $mem_usage = memory_get_usage(true); 
        
        if ($mem_usage < 1024) 
            return  $mem_usage." bytes"; 
        elseif ($mem_usage < 1048576) 
            return round($mem_usage/1024,2)." kilobytes"; 
        else 
            return round($mem_usage/1048576,2)." megabytes"; 
        return '';
    } 


    protected function log($message, $data= array(), $type = 'info')
    {
        $data['Item number'] = $this->itemCounter;
        
        $data['data']=array(
            'mpn'=>$this->getColumnValue($this->getMpnColumn()),
            'sku'=>$this->getColumnValue($this->getSkuColumn())
        );
        if($this->product){
            $data['data']['sku']=$this->product->getIdentifier()->getData();
        }
        $this->logger->$type($message,array($data));
        //print_r($this->displayItem());
        if($this->isLogProduct()){
            $this->logger->$type($this->displayItem());
        }
    }

    private $itemCounter = 0;
    private $itemFoundCounter = 0;
    private $itemCreatedCounter = 0;
    private $itemFoundBySkuCounter = 0;
    private $itemFoundByUpcCounter = 0;
    private $itemFoundByChannelIdCounter = 0;
    private $itemFoundByMpnCounter = 0;
    private $itemNotFoundCounter = 0;
    private $itemWrongFormatCounter = 0;
    
    private function logCounter($force = false)
    {
        if (!$force && $this->itemCounter != 0 && $this->itemCounter%$this->loggerCount != 0) return null;
        //$this->mappingCodeRepository->getDoctrineEntityManager()->flush();
        //$this->mappingCodeRepository->getDoctrineEntityManager()->clear('GescanPim\Bundle\ConnectorBundle\Entity\AttributeList');
        //$this->mappingCodeRepository->getDoctrineEntityManager()->clear('GescanPim\Bundle\ConnectorBundle\Entity\MappingCode');
        $this->attributeList =  array();
        gc_collect_cycles();
        $data = array();
        
        if ($this->itemCounter)                 $data['Number Of Line']                 = $this->itemCounter;
        if ($this->itemWrongFormatCounter)      $data['Number Of Line with wrong format'] = $this->itemWrongFormatCounter;
        if ($this->itemFoundCounter)            $data['Number Item found']              = $this->itemFoundCounter;
        if ($this->itemCreatedCounter)          $data['Number Item Created']            = $this->itemCreatedCounter;
        if ($this->itemFoundBySkuCounter)       $data['Number Item found by Sku']       = $this->itemFoundBySkuCounter;
        if ($this->itemFoundByChannelIdCounter) $data['Number Item found by ChannelId'] = $this->itemFoundByChannelIdCounter;
        if ($this->itemFoundByUpcCounter)       $data['Number Item found by UPC']       = $this->itemFoundByUpcCounter;
        if ($this->itemFoundByMpnCounter)       $data['Number Item found by MPN']       = $this->itemFoundByMpnCounter;
        if ($this->itemNotFoundCounter)         $data['Number Item not found']          = $this->itemNotFoundCounter;
        
        
        
        $data['Memory Usage'] = $this->get_memory_usage();
        
        $this->logger->info('',$data);
    }
	
    protected function displayItem() 
    {
        if ($this->item ==null) return 'NULL';
        if (is_array($this->item)) return print_r($this->item, true);
        if (is_a($this->item,'SimpleXMLElement')) return $this->item->asXML();
        //die(get_class($this->item));
    }
    
    abstract protected function checkItem();
    protected function findProduct()
    {
        $this->product = null;
        /********** SEARCH BY SKU ********************/
        if ($this->getSkuColumn() && $this->getColumnValue($this->getSkuColumn())) {
            return $this->findProductBySku($this->getColumnValue($this->getSkuColumn()));
        }
        /********** SEARCH BY CHANNELID ********************/
        if ($this->getChannelIdColumn() && 
            $this->getColumnValue($this->getChannelIdColumn()) &&
            $this->findProductByChannelId($this->getColumnValue($this->getChannelIdColumn()))) {
            return $this->product;
        }

        /********** SEARCH BY MPN ********************/
        if ($this->getMpnColumn() &&
            $this->getColumnValue($this->getMpnColumn()) &&
            $this->findProductByMpn($this->getColumnValue($this->getMpnColumn()))) {
            //die('ici');
            return $this->product;
        }

        /********** SEARCH BY UPC ********************/
        if ($this->getUpcColumn() && 
            $this->getColumnValue($this->getUpcColumn()) &&
            $this->findProductByUpc($this->getColumnValue($this->getUpcColumn()))) {
            return $this->product;
        }


        if($this->isLogProductNotFound()){
            $this->log('Product not found', array(  'sku'=> $this->getColumnValue($this->getSkuColumn()),
                                                    'channelId' => $this->getColumnValue($this->getChannelIdColumn()),
                                                    'upc'=>$this->getColumnValue($this->getUpcColumn()),
                                                    'mpn'=>$this->getColumnValue($this->getMpnColumn())));
        }

        return null;
    }
    abstract protected function updateAttributes();
    
    abstract protected function getColumnValue($column);

    protected function updateChannelAttributes(){
        $value = $this->getChannelMapping();
        if ($this->product->getValue('_channels') && $this->product->getValue('_channels')->getData()) {
            $values = explode(' | ', $this->product->getValue('_channels')->getData());
            $values = array_flip($values);
            $values[$this->getChannelMapping()]= true;
            $value = implode(' | ', array_keys($values));
        }
        $this->updateProductAttribute('_channels', $this->getChannelMapping());
    }

    protected $unusedAttribute = null;

    protected function getUnusedValues(){
        if ($this->unusedAttribute !== null) return $this->unusedAttribute;
        if ($this->product->getValue('_unused_attribute_json') && $this->product->getValue('_unused_attribute_json')->getData()){
            $this->unusedAttribute = json_decode($this->product->getValue('_unused_attribute_json')->getData(), true);
        }else{
            $this->unusedAttribute = array();
        }
        return $this->unusedAttribute;
    }

    protected function addUnusedValue($key,$value){
        if(!$this->isAddUnusedAttribute()) return;
        $this->getUnusedValues();
        $this->unusedAttribute[$this->getChannel()][$this->getChannelMapping()][$key]= $value;
    }

    protected function removeUnusedValue($key){
        if(!$this->isAddUnusedAttribute()) return;
        $this->getUnusedValues();
        if (array_key_exists($this->getChannel(), $this->unusedAttribute)&&
            array_key_exists($this->getChannelMapping(), $this->unusedAttribute[$this->getChannel()])&&
            array_key_exists($key, $this->unusedAttribute[$this->getChannel()][$this->getChannelMapping()])
        ){
            unset($this->unusedAttribute[$this->getChannel()][$this->getChannelMapping()][$key]);
        }
    }

    public function process($item) 
    {
        $this->logCounter();
        $this->itemCounter++;
        $this->item = $item;
        $this->product = null;
        if (!$this->checkItem()) {
            $this->stepExecution->incrementSummaryInfo('skip');
            $this->itemWrongFormatCounter++;
            return null;
        }

        if (!$this->findProduct()) {
            $this->itemNotFoundCounter++;
            $this->stepExecution->incrementSummaryInfo('skip');
            return null;
        }
        $this->itemFoundCounter++;
        if (!$this->updateAttributes()) return null;

        $this->updateChannelAttributes();

        if ($this->isUpdateFinalChannel()) {
            $this->finalChannelManager->mergeFinalChannel($this->product);
            $this->finalChannelManager->completeScoring($this->product);
        }

        if($this->isAddUnusedAttribute()){
            $this->updateProductAttribute('_unused_attribute_json', json_encode($this->getUnusedValues()));
        }

            if($this->isSaveProduct()){
            return $this->product;
        }else{
            $this->stepExecution->incrementSummaryInfo('added');
            return null;
        }
    }
}