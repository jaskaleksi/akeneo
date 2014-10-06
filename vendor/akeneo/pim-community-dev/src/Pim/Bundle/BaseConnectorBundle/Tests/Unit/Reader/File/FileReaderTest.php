<?php

namespace Pim\Bundle\BaseConnectorBundle\Tests\Unit\Reader\File;

use Pim\Bundle\BaseConnectorBundle\Reader\File\FileReader;

/**
 * Test related class
 *
 * @author    Gildas Quemener <gildas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FileReaderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->reader = new FileReader();
    }

    public function testFilePath()
    {
        $this->reader->setFilePath('foo');
        $this->assertSame('foo', $this->reader->getFilePath());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Not implemented yet.
     */
    public function testRead()
    {
        $this->reader->read();
    }
}
