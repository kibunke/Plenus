<?php

namespace InscripcionBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * OrigenRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrigenRepository extends EntityRepository
{
    public function findAllOrderedByMunicipio()
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.municipio')->getQuery()->getResult();
    }
    public function findByNameAndTipoAndMunicipio($nombre,$tipo,$municipio)
    {
        $query = $this->createQueryBuilder('o')
                        ->where('o.nombre = ?1')
                        ->andWhere('o INSTANCE OF ?2')
                        ->andWhere('o.municipio = ?3')
                        ->setParameter(1, $nombre)
                        ->setParameter(2, $tipo)
                        ->setParameter(3, $municipio);
    
        try {
            return $query->getQuery()->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }catch (\Doctrine\ORM\NonUniqueResultException $e) {
            return null;
        }
    }     
}