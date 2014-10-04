<?php

namespace GescanPim\Bundle\ConnectorBundle\Reader\File\Pim;

use Akeneo\Bundle\BatchBundle\Item\ItemReaderInterface;
use Pim\Bundle\BaseConnectorBundle\Reader\File\FileReader;

/**
 * Description of PimXmlProductReader
 *
 * @author ecoisne
 */
class XmlProductReader extends FileReader implements ItemReaderInterface {
    
    protected $xml;

    public function read()
    {
        if (null === $this->xml) {
            // for exemple purpose, we should use XML Parser to read line per line
            $this->xml = simplexml_load_file($this->filePath, 'SimpleXMLIterator');
            $this->xml = $this->xml->T_NEW_CATALOG->PRODUCT;
            $this->xml->rewind();
        }

        if ($data = $this->xml->current()) {
            $this->xml->next();
            return $data;
        }

        return null;
    }

    public function getConfigurationFields()
    {
        return array(
            'filePath' => array(
                'options' => array(
                    'label' => 'gescan_pimconnector.steps.import.filePath.label',
                    'help'  => 'gescan_pimconnector.steps.import.filePath.help'
                )
            ),
        );
    }
}
