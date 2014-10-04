<?php
namespace GescanPim\Bundle\ConnectorBundle\Entity;

use Doctrine\ORM\EntityRepository;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MappingCodeRepository
 *
 * @author ecoisne
 */
class MappingCodeRepository extends EntityRepository {
    //put your code here
    public function mappingTypeExists($type){
        switch($type){
            case 'manufacturer':
            case 'attribute':
            case 'sku':
            case 'color':
                return true;
            default:
                return false;
        }
    }

    public function getMappingValue($code, $type, $source = false){
        $qb = $this->createQueryBuilder('mc')
                ->select('mc')
                ->where('mc.code = :code')
                ->andWhere('mc.type = :type');
        if($source){
            if(is_array($source)){
                $qb->andWhere('mc.source IN (:source)')
                   ->setParameter('source', $source);
            }else{
                $qb->andWhere('mc.source = :source')
                   ->setParameter('source', $source);
            }
        }
        $qb->setParameter('code', $code)
           ->setParameter('type', $type);
        
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    public function getDoctrineEntityManager() {
        if (!$this->_em->isOpen()) {
            $this->_em = $this->_em->create(
                $this->_em->getConnection(),
                $this->_em->getConfiguration()
            );
        }
        return parent::getEntityManager();
    }
    
    public function getMapping($code, $type, $source, $createIfnotExist= false, $guess = false){
        $mapping = $this->getMappingValue($code,$type,$source);
        if(!$mapping && $createIfnotExist){
           $mapping = $this->createMapping($code, $type, $source,$guess);
        }
        return $mapping;
        
    }
    
    public function createMapping($code, $type, $source, $guess = false){
        $mapping = new MappingCode();
        $mapping->setCode($code)
                ->setInitialValue($code)
                ->setType($type)
                ->setSource($source)
                ->setIgnored(false);
        
        if($guess){
            $guess = $this->guessMapping($code, $type);
            if($guess instanceof MappingCode){
                $mapping->setValue($guess->getValue());
                $mapping->setIgnored($guess->isIgnored());
            }else{
                $mapping->setValue($guess);
            }
            if($mapping->getValue()){
                $mapping->setUser('system');
            }
        }
        return $mapping;
    }
    
    public function guessMapping($code, $type){
        $mapping = $this->findOneBy(array('code'=>$code, 'type'=>$type));
        if($mapping && $mapping->getValue() && $mapping->getValue() != 'sku'){
            return $mapping;
        }
        $mapping = $this->findOneBy(array('initialValue'=>$code, 'type'=>$type));
        if($mapping && $mapping->getValue() && $mapping->getValue() != 'sku'){
            return $mapping;
        }
        $mapping = $this->findOneBy(array('value'=>$code, 'type'=>$type));
        if($mapping && $mapping->getValue() && $mapping->getValue() != 'sku'){
            return $mapping;
        }
        switch($type){
            case 'attribute':
                $query = $this->getEntityManager()->getRepository('PimCatalogBundle:Attribute')->createQueryBuilder('a')
                                                                        ->leftJoin('a.translations', 'translation')
                                                                        ->where('translation.label = :code')
                                                                        ->andWhere('a.code NOT LIKE :code_reject')
                                                                        ->andWhere('a.code NOT LIKE :code_identifier')
                                                                        ->setParameter('code', $code)
                                                                        ->setParameter('code_reject', '\_%')
                                                                        ->setParameter('code_identifier', 'sku')
                        ->setMaxResults(1);
                $attribute = $query->getQuery()->getOneOrNullResult();
                if($attribute){
                    return $attribute->getCode();
                }
                break;
        }
        return null;
    }
    
    
    
}
