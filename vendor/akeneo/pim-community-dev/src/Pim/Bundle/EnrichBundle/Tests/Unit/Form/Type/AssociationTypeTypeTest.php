<?php

namespace Pim\Bundle\EnrichBundle\Tests\Unit\Form\Type;

use Pim\Bundle\EnrichBundle\Form\Type\AssociationTypeType;

/**
 * Test related class
 *
 * @author    Filips Alpe <filips@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AssociationTypeTypeTest extends AbstractFormTypeTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        // Create form type
        $this->type = new AssociationTypeType();
        $this->form = $this->factory->create($this->type);
    }

    /**
     * Test build of form with form type
     */
    public function testFormCreate()
    {
        // Assert fields
        $this->assertField('code', 'text');

        // Assert option class
        $this->assertEquals(
            'Pim\Bundle\CatalogBundle\Entity\AssociationType',
            $this->form->getConfig()->getDataClass()
        );

        // Assert name
        $this->assertEquals('pim_enrich_association_type', $this->form->getName());
    }
}
