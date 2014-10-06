<?php

namespace Pim\Bundle\TransformBundle\Tests\Unit\Normalizer;

use Pim\Bundle\TransformBundle\Normalizer\FlatChannelNormalizer;

/**
 * Test related class
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FlatChannelNormalizerTest extends ChannelNormalizerTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->normalizer = new FlatChannelNormalizer();
        $this->format     = 'csv';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSupportNormalizationData()
    {
        return array(
            array('Pim\Bundle\CatalogBundle\Entity\Channel', 'json', false),
            array('Pim\Bundle\CatalogBundle\Entity\Channel', 'xml', false),
            array('Pim\Bundle\CatalogBundle\Entity\Channel', 'csv', true),
            array('stdClass', 'json', false),
            array('stdClass', 'xml', false),
            array('stdClass', 'csv', false)
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getNormalizeData()
    {
        return array(
            array(
                array(
                    'code'             => 'channel_code',
                    'label'            => 'channel_label',
                    'currencies'       => 'EUR,USD',
                    'locales'          => 'fr_FR,en_US',
                    'category'         => 'My_Tree',
                    'conversion_units' => 'weight: KILOGRAM, washing_temperature: '
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        $data['currencies'] = explode(',', $data['currencies']);
        $data['locales']    = explode(',', $data['locales']);

        return parent::createEntity($data);
    }
}
