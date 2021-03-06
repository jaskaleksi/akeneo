<?php

namespace Pim\Bundle\CatalogBundle\AttributeType;

use Pim\Bundle\CatalogBundle\Model\ProductValueInterface;
use Pim\Bundle\CatalogBundle\Model\AbstractAttribute;

/**
 * Multi options (select) attribute type
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class OptionMultiSelectType extends AbstractAttributeType
{
    /**
     * {@inheritdoc}
     */
    protected function prepareValueFormOptions(ProductValueInterface $value)
    {
        $options = parent::prepareValueFormOptions($value);
        $attribute = $value->getAttribute();
        $options['class']                = 'PimCatalogBundle:AttributeOption';
        $options['collection_id']        = $attribute->getId();
        $options['multiple']             = true;
        $options['minimum_input_length'] = $attribute->getMinimumInputLength();

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareValueFormData(ProductValueInterface $value)
    {
        if ($value->getData() && $value->getData()->isEmpty()) {
            return $value->getAttribute()->getDefaultValue();
        }

        return $value->getData();
    }

    /**
     * {@inheritdoc}
     */
    protected function defineCustomAttributeProperties(AbstractAttribute $attribute)
    {
        return parent::defineCustomAttributeProperties($attribute) + [
            'minimumInputLength' => [
                'name'      => 'minimumInputLength',
                'fieldType' => 'number'
            ],
            'options' => [
                'name'      => 'options',
                'fieldType' => 'pim_enrich_options'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pim_catalog_multiselect';
    }
}
