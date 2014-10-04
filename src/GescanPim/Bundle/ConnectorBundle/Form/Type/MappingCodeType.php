<?php

namespace GescanPim\Bundle\ConnectorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Type for color custom entity
 */
class MappingCodeType extends AbstractType
{
    /**
     *
     * @var \GescanPim\Bundle\ConnectorBundle\Services\MappingServices 
     */
    protected $mapper = null;
    /**
     *
     * @var \Pim\Bundle\CatalogBundle\Entity\Repository\AttributeRepository 
     */
    protected $attributeRepository = null;
    
    public function __construct(\GescanPim\Bundle\ConnectorBundle\Services\MappingServices $mapper,\Pim\Bundle\CatalogBundle\Entity\Repository\AttributeRepository $attributeRepository) {
        $this->mapper = $mapper;
        $this->attributeRepository=$attributeRepository;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $optionsparam = array('read_only'=>true);
        $builder->add('code', null, $optionsparam)
                ->add('source', null,$optionsparam)
                ->add('initialValue', null,$optionsparam)
                ->add('type', null,$optionsparam);
        switch($options['data']->getType()){
            case 'manufacturer':
                $builder->add('value','choice', array('empty_value' => '','choices'=>$this->mapper->getManufacturerList()));
                break;
            case 'color':
                $builder->add('value','choice', array('empty_value' => '','choices'=>$this->mapper->getColorList()));
                break;
            case 'attribute':
                $attributes = array();
                $attributes['GLOBAL']['attribute']='many attributes';
                foreach($this->attributeRepository->findWithGroups() as $att){
                    //print_r('<pre>');
                    //print_r(get_class($att));
                    //die();
                    $attributes[$att->getVirtualGroup()->getLabel()][$att->getCode()]=$att->getLabel();
                }
                
                $builder->add('value','choice', array('empty_value' => '','choices'=>$attributes));
                break;
            default:
                $builder->add('value');
        }
        
        $builder->add('ignored', 'choice',array('choices'=>array('0'=>'No','1'=>'Yes')));
        
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'gescan_connector_form_mappingcode';
    }
}
