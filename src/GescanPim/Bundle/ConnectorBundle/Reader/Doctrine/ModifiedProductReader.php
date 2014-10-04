<?php
namespace GescanPim\Bundle\ConnectorBundle\Reader\Doctrine;

use Pim\Bundle\BaseConnectorBundle\Reader\Doctrine\Reader;
use Pim\Bundle\CatalogBundle\Repository\ProductRepository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FakeProductReader
 *
 * @author ecoisne
 */
class ModifiedProductReader extends Reader{
    //put your code here
    
    /**
     *
     * @var ProductRepository
     */
    protected $repository;


    public function __construct(
        ProductRepository $repository
    ) {
        $this->repository          = $repository;
    }
   
    public function read()
    {
        $this->repository->createQueryBuilder('p')
                            ->select('p')
                            ->where('p.updated > :code')
                            ->andWhere('p.updated = :value')
                            ->setParameter('code',$attributeCode)
                            ->setParameter('value', $value)
                            ;

        $this->query = $this->repository
            ->buildByChannelAndCompleteness($this->channel)
            ->getQuery();

        $product = parent::read();

        if ($product) {
            $this->metricConverter->convert($product, $this->channel);
        }

        return $product;
    }
    
    public function getConfigurationFields()
    {
        return array(
            'channel' => array(
                'type'    => 'text',
                'options' => array(
                    'required' => false,
                    'label'    => 'delta date in day',
                    'help'     => 'delta date in day'
                )
            )
        );
    }
}
