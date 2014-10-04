<?php
namespace GescanPim\Bundle\ConnectorBundle\Processor\Magento;

use Symfony\Component\Validator\Constraints as Assert;
use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use GescanPim\Bundle\ConnectorBundle\Entity\MappingCodeRepository;
use Pim\Bundle\CatalogBundle\Manager\ChannelManager;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductCsvProcessor
 *
 * @author ecoisne
 */
class ProductCsvProcessor  extends AbstractConfigurableStepElement implements
    ItemProcessorInterface,
    StepExecutionAwareInterface 
{
    protected $product;
    protected $attributeRepository;
    
    protected $attributeList = array();
    /**
     * @var ChannelManager
     */
    protected $channelManager;
     protected $em;
    /**
     * @Assert\NotBlank
     * @Assert\Choice(choices={",", ";", "|"}, message="The value must be one of , or ; or |")
     */
    protected $delimiter = ';';

    /**
     * @Assert\NotBlank
     * @Assert\Choice(choices={"""", "'"}, message="The value must be one of "" or '")
     */
    protected $enclosure = '"';
    
    protected $channel;
    
    protected $channelObject;
    
    protected $stepExecution;
    protected  $headersended=false;
    protected $filePathSpecReplacement;
    protected $filePathImageReplacement;
    protected $defaultStore;
    protected $defaultWebsite;
	protected $promotionCategory;
    
    protected $counter = 0;
    /**
     * @Assert\NotBlank
     */
    protected $escape = '\\';

    public function __construct(ChannelManager $channelManager, \Pim\Bundle\CatalogBundle\Entity\Repository\AttributeRepository $attributeRepository, \Doctrine\ORM\EntityManager $em ) {
        $this->channelManager = $channelManager;
        $this->attributeRepository = $attributeRepository;
        $this->em = $em;
    }
    
    public function getConfigurationFields() {
        return array(
            'delimiter' => array(
                'options' => array(
                    'label' => 'pim_base_connector.import.delimiter.label',
                    'help'  => 'pim_base_connector.import.delimiter.help'
                )
            ),
            'enclosure' => array(
                'options' => array(
                    'label' => 'pim_base_connector.import.enclosure.label',
                    'help'  => 'pim_base_connector.import.enclosure.help'
                )
            ),
            'channel' => array(
                'type'    => 'choice',
                'options' => array(
                    'choices'  => $this->channelManager->getChannelChoices(),
                    'required' => true,
                    'select2'  => true,
                    'label'    => 'pim_base_connector.export.channel.label',
                    'help'     => 'pim_base_connector.export.channel.help'
                )
            ),
            
            'filePathSpecReplacement' => array(
                'options' => array(
                    'label'    => 'Value replaced for spec sheet',
                    'help'     => 'Value replaced separate by,'
                )
            ),
            
            'filePathImageReplacement' => array(
                'options' => array(
                    'label'    => 'Value replaced for image',
                    'help'     => 'Value replaced separate by,'
                )
            ),
            'defaultStore' => array(
                'options' => array(
                    'label'    => 'Default Store',
                    'help'     => 'Default Store,'
                )
            ),
            'defaultWebsite' => array(
                'options' => array(
                    'label'    => 'Default Website',
                    'help'     => 'Default Website,'
                )
            ),
			
			'promotionCategory' => array(
                'options' => array(
                    'label'    => 'Promotion Category',
                    'help'     => 'Promotion Category,'
                )
            ),
            
            );
    }
    public function setPromotionCategory($promotionCategory){
        $this->promotionCategory = $promotionCategory;
    }
    
    public function getPromotionCategory(){
        return $this->promotionCategory;
    }
    public function setDefaultStore($defaultStore){
        $this->defaultStore = $defaultStore;
    }
    
    public function getDefaultStore(){
        return $this->defaultStore;
    }
    public function setDefaultWebsite($defaultStore){
        $this->defaultWebsite = $defaultStore;
    }
    
    public function getDefaultWebsite(){
        return $this->defaultWebsite;
    }
    
    public function setFilePathSpecReplacement($filePathSpecReplacement){
        $this->filePathSpecReplacement = $filePathSpecReplacement;
    }
    
    public function getFilePathSpecReplacement(){
        return $this->filePathSpecReplacement;
    }
    
    public function setFilePathImageReplacement($filePathImageReplacement){
        $this->filePathImageReplacement = $filePathImageReplacement;
    }
    
    public function getFilePathImageReplacement(){
        return $this->filePathImageReplacement;
    }
    
    public function setDelimiter($delimiter){
        $this->delimiter = $delimiter;
    }
    
    public function getDelimiter(){
        return $this->delimiter;
    }
    
    public function setChannel($channel){
        $this->channel = $channel;
    }
    
    public function getChannel(){
        return $this->channel;
    }
    
    public function setEnclosure($enclosure){
        $this->enclosure = $enclosure;
    }
    
    public function getEnclosure(){
        return $this->enclosure;
    }
    
    public function getTreePath($category, $separator='/'){
        if(!$category->isRoot()){
            return $this->getTreePath($category->getParent()).$separator.$category->getTranslation('en_CA')->getLabel();
        }else{
            return 'Products';
        }
    }
    
    public function getChannelObject(){
       if(!$this->channelObject){
        $this->channelObject=  $this->channelManager->getChannelByCode($this->channel);
       }
         return $this->channelObject;
    }
    
    protected function replacePath($val, $type){
        switch($type){
            case 'image':
                $replacement = $this->filePathImageReplacement; 
                break;
            case 'spec_sheet':
                $replacement = $this->filePathSpecReplacement;
                break;
            default:
                return $val;
        }
        list($search,$replace) = explode(',',$replacement,2);
        if(strtolower(substr($replace,0,4))=='http'&&$type == 'image'){
            $val = str_replace('/','-',str_replace($search, '', $val));
            $val = $replace.$val;
        }else{
            $val = str_replace($search, $replace, $val);
        }
        $val = str_replace($search, $replace, $val);
        return $val;
    }
    
    

    public function process($item) {
        $this->counter++;
        /** @var $item ProductInterface**/
        $this->product = $item;
        $return['sku'] = $item->getIdentifier();
        $return['store'] = $this->getDefaultStore();
        $return['product_website'] = $this->getDefaultWebsite();
        $return['attribute_set'] = $item->getFamily()->getTranslation('en_CA')->getLabel();
        $return['type']='simple';
        $return['status']=$item->isEnabled()?'1':'0';
        $return['weight']='0.0';
        $return['tax_class_id']='0';
        
        
        $return ['_media_image']=array();
        $productName = $this->getProductValue('name');
		$promotion_start = false;
		$promotion_end = false;
        foreach ($this->attributeRepository->findAll() as $Attribute){
            switch($Attribute->getCode()){
                case 'sku':
                    break;
                case 'image_1':
                    $return['image'] = '';
                    $val = $this->getProductValue($Attribute);
                    
                    if($val){
                       $return ['image']='+'.$this->replacePath($val,'image'); 
                    }else{
                       $return ['image']='';
                    }
                    break;
                case '_promotion_start':
                        $val = $this->getProductValue($Attribute);
                        if($val){
                            $diff = date_create('now')->diff( $val );
                            if($diff->invert==1){
                             $promotion_start= true;
                            }
                        }
                        break;
                case '_promotion_end':
                        $val = $this->getProductValue($Attribute);
                        if($val){
                            $diff = date_create('now')->diff( $val );
                            if($diff->invert==0){
                             $promotion_end= true;
                            }
                        }else{
                            // no end to the promotions
                            $promotion_end= true;
                        }
                        break;
                case 'spec_sheet':
                    $val = $this->getProductValue($Attribute);
                    if($val){
                       $return ['spec_sheet']=$this->replacePath($val,'spec_sheet'); 
                    }else{
                        $return ['spec_sheet']='';
                    }
                    break;
                default:
                    if(substr($Attribute->getCode(),0,1)=='_') break;
                    
                    if(substr($Attribute->getCode(),0,6)=='image_'){
                        $val = $this->getProductValue($Attribute);
                        if($val){
                            $return ['_media_image'][$this->replacePath($val,'image')]=$this->replacePath($val,'image').'::'.$productName;
                        }
                    }else{
                        $return [$Attribute->getCode()]=$this->getProductValue($Attribute);
                    }
                    
                    break;
            }
        }
		
		
        
        if(!$return['thumbnail']){
            $return['thumbnail']=$return ['image'];
        }
        $return['thumbnail_label']=$return ['name'];
        $return['small_image_label']=$return['name'];
        $return['small_image']=$return ['image'];
        $return['image_label']=$return['name'];
        if(count($return['_media_image'])){
            $return['_media_image']= implode(';',$return['_media_image']);
        }else{
            $return['_media_image'] = '';
        }
        
        if($return['short_description']==''){
            $return['short_description']='&nbsp;';
        }
		
		$return['categories'] = array();
        foreach ($item->getCategories() as $val){
            if($val->getRoot()==$this->getChannelObject()->getCategory()->getId()){
                $cat = $this->getTreePath($val);
                if($cat){
                    $return['categories'][$cat]=true;
                }
                //$return['categories'][$this->getTreePath($val)];
            }
        }
		if($promotion_start&&$promotion_end&&$this->getPromotionCategory()){
			$return['categories'][$this->getPromotionCategory()]=true;
		}
        $return['categories']= implode(';;',array_keys($return['categories']));
        
        $csv = '';
        if(!$this->headersended){
            $this->headersended = true;
             $csv .= $this->getCSV(array_keys($return)).PHP_EOL;
        }
        $csv .= $this->getCSV($return);
        //$this->em->detach($item);
        //$this->clearProduct();
        if($this->counter%1000==0){
            echo "Memory usage: " . (memory_get_usage() / 1048576) . " MB" . PHP_EOL;
        }
        return $csv;

        
    }
    
    protected function clearProduct(){
        
        //$this->product->getValues()
        foreach ($this->product->getCategories() as $val){
            $this->em->detach($val);
        }
        foreach ($this->product->getGroups() as $val){
            $this->em->detach($val);
        }
        foreach ($this->product->getAssociations() as $val){
            $this->em->detach($val);
        }
        foreach ($this->product->getCompletenesses() as $val){
            $this->em->detach($val);
        }
        foreach ($this->product->getMedia() as $val){
            $this->em->detach($val);
        }
        foreach ($this->product->getValues() as $val){
            $this->em->detach($val);
        }
        
        if($this->product){
            $this->em->detach($this->product);
        }
    }
    
    protected function getCSV($array){
        static $fp = false;
        if ($fp === false){
            $fp = fopen('php://temp', 'r+'); // see http://php.net/manual/en/wrappers.php.php - yes there are 2 '.php's on the end.
            // NB: anything you read/write to/from 'php://temp' is specific to this filehandle
        }
        if (fputcsv($fp, $array, $this->delimiter, $this->enclosure) === false){
            return false;
        }
        rewind($fp);
        $csv = '';
        while($line = fgets($fp)){
            $csv.=$line;
        }
        ftruncate($fp,0);
        //$csv = fread($fp,filesize('php://temp'));
        return $csv;
    }


    protected function getProductValue($code){
        if(is_string($code)){
            $attribute = $this->getAttribute($code);
        }else{
            $attribute = $code;
        }
        if(!$attribute->isScopable()){
            $value = $this->product->getValue($attribute->getCode());
        }else{
            $value = $this->product->getValue($attribute->getCode(), null, $this->channel);
        }
        if($value){
            if($attribute->getBackendType()=='media'&&$value->getData()){
                return $value->getData()->getFilePath();
            }else{
               return $value->getData(); 
            }
            
        }else{
            return '';
        }   
    }
    
    public function getAttribute($code){
        if(!array_key_exists($code, $this->attributeList)){
            $attribute = $this->attributeRepository->findByReference($code);
            $this->attributeList[$code]=$attribute;
        }
        return $this->attributeList[$code];
    }

    protected function getProduct() {
        return false;
    }

    public function getScope() {
        return $this->scope;
    }

    protected function updateProduct() {
        return false;
    }

    public function setStepExecution(StepExecution $stepExecution) {
        $this->stepExecution = $stepExecution;
        return $this;
    }

//put your code here
}
