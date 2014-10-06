<?php

namespace Pim\Bundle\InstallerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Load fixtures data
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LoadFixturesData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->getLoader()->load($manager, $this->referenceRepository, $this->getFiles());
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 100;
    }

    /**
     * @return \Pim\Bundle\InstallerBundle\FixtureLoader\MultipleLoader
     */
    protected function getLoader()
    {
        return $this->container->get('pim_installer.fixture_loader.multiple_loader');
    }

    /**
     * Returns an array of fixture files
     *
     * @return array
     */
    protected function getFiles()
    {
        $dataParam = $this->container->getParameter('installer_data');
        preg_match('/^(?P<bundle>\w+):(?P<directory>\w+)$/', $dataParam, $matches);
        $bundles    = $this->container->getParameter('kernel.bundles');
        $reflection = new \ReflectionClass($bundles[$matches['bundle']]);
        $dataPath   = dirname($reflection->getFilename()) . '/Resources/fixtures/' . $matches['directory'];

        $paths = glob($dataPath.'/*');
        if ('doctrine/mongodb-odm' === $this->container->getParameter('pim_catalog.storage_driver')) {
            // Do not load products and associations with the ORM fixtures when mongodb support is activated
            foreach ($paths as $key => $path) {
                if (false !== strpos($path, 'products.') || false !== strpos($path, 'associations.')) {
                    unset($paths[$key]);
                }
            }
        }

        return $paths;
    }
}
