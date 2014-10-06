<?php

namespace Pim\Bundle\CatalogBundle\AttributeType;

use Symfony\Component\Form\FormFactoryInterface;
use Pim\Bundle\CatalogBundle\Model\ProductValueInterface;
use Pim\Bundle\CatalogBundle\Model\AbstractAttribute;

/**
 * The attribute type interface
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface AttributeTypeInterface
{
    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Build form type for flexible entity value
     *
     * @param FormFactoryInterface  $factory the form factory
     * @param ProductValueInterface $value   the flexible value
     *
     * @return FormInterface the form
     */
    public function buildValueFormType(FormFactoryInterface $factory, ProductValueInterface $value);

    /**
     * Build form types for custom properties of an attribute
     *
     * @param FormFactoryInterface $factory   the form factory
     * @param AbstractAttribute    $attribute the attribute
     *
     * @return FormInterface the form
     */
    public function buildAttributeFormTypes(FormFactoryInterface $factory, AbstractAttribute $attribute);
}
