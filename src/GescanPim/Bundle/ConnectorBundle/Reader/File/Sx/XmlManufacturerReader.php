<?php

namespace GescanPim\Bundle\ConnectorBundle\Reader\File\Sx;

use Akeneo\Bundle\BatchBundle\Item\ItemReaderInterface;
use Pim\Bundle\BaseConnectorBundle\Reader\File\FileReader;

/**
 * Description of PimXmlProductReader
 *
 * @author ecoisne
 */
class XmlManufacturerReader extends FileReader implements ItemReaderInterface {
    
    protected $xml;

    public function read()
    {
        if (null === $this->xml) {
            // for exemple purpose, we should use XML Parser to read line per line
            $this->xml = simplexml_load_file($this->filePath, 'SimpleXMLIterator');
            $path = 'tt-apsvRow';
            $this->xml = $this->xml->$path;
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
