<?php

namespace Pim\Bundle\EnrichBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Pim\Bundle\EnrichBundle\Form\Subscriber\AddAttributeAsLabelSubscriber;
use Pim\Bundle\EnrichBundle\Form\Subscriber\AddAttributeRequirementsSubscriber;
use Pim\Bundle\EnrichBundle\Form\Subscriber\DisableFieldSubscriber;

/**
 * Type for family form
 *
 * @author    Filips Alpe <filips@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FamilyType extends AbstractType
{
    /**
     * @var string
     */
    protected $attributeClass;

    /**
     * @var AddAttributeRequirementsSubscriber
     */
    protected $requireSubscriber;

    /**
     * Constructor
     *
     * @param AddAttributeRequirementsSubscriber $requireSubscriber
     * @param string                             $attributeClass
     */
    public function __construct(AddAttributeRequirementsSubscriber $requireSubscriber, $attributeClass)
    {
        $this->requireSubscriber = $requireSubscriber;
        $this->attributeClass    = $attributeClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addCodeField($builder)
            ->addLabelField($builder)
            ->addAttributeRequirementsField($builder)
            ->addEventSubscribers($builder);
    }

    /**
     * Add code field
     *
     * @param FormBuilderInterface $builder
     *
     * @return \Pim\Bundle\EnrichBundle\Form\Type\FamilyType
     */
    protected function addCodeField(FormBuilderInterface $builder)
    {
        $builder->add('code');

        return $this;
    }

    /**
     * Add label field
     *
     * @param FormBuilderInterface $builder
     *
     * @return \Pim\Bundle\EnrichBundle\Form\Type\FamilyType
     */
    protected function addLabelField(FormBuilderInterface $builder)
    {
        $builder->add(
            'label',
            'pim_translatable_field',
            array(
                'field'             => 'label',
                'translation_class' => 'Pim\\Bundle\\CatalogBundle\\Entity\\FamilyTranslation',
                'entity_class'      => 'Pim\\Bundle\\CatalogBundle\\Entity\\Family',
                'property_path'     => 'translations'
            )
        );

        return $this;
    }

    /**
     * Add attribute requirements field
     *
     * @param FormBuilderInterface $builder
     *
     * @return \Pim\Bundle\EnrichBundle\Form\Type\FamilyType
     */
    protected function addAttributeRequirementsField(FormBuilderInterface $builder)
    {
        $builder->add('attributeRequirements', 'collection', array('type' => 'pim_enrich_attribute_requirement'));

        return $this;
    }

    /**
     * Add event subscribers to form type
     *
     * @param FormBuilderInterface $builder
     *
     * @return \Pim\Bundle\EnrichBundle\Form\Type\FamilyType
     */
    protected function addEventSubscribers(FormBuilderInterface $builder)
    {
        $factory = $builder->getFormFactory();

        $builder
            ->addEventSubscriber(new AddAttributeAsLabelSubscriber($this->attributeClass, $factory))
            ->addEventSubscriber($this->requireSubscriber)
            ->addEventSubscriber(new DisableFieldSubscriber('code'));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Pim\Bundle\CatalogBundle\Entity\Family'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pim_enrich_family';
    }
}
