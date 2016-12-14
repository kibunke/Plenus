<?php

namespace ResultadoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * TorneoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlazaMedalleroRepository extends EntityRepository
{
    public function getAllByMunicipio($municipio)
    {
        return $this->getEntityManager()
                        ->createQuery(" SELECT p
                                        FROM ResultadoBundle:Plaza p
                                        JOIN p.equipo e
                                        JOIN e.municipio m
                                        WHERE p INSTANCE OF ResultadoBundle:PlazaMedallero AND m = ?1
                                        ORDER BY p.orden")
                        ->setParameter(1,$municipio)
                        ->getResult();
        ;
    }  
}