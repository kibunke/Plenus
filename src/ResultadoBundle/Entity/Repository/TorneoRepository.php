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
class TorneoRepository extends EntityRepository
{
    public function getEventosPorTorneo()
    {
        return $this->getEntityManager()
                        ->createQuery(" SELECT t.id,t.nombre, COUNT(e)
                                        FROM ResultadoBundle:Evento e
                                        JOIN e.torneo t
                                        WHERE e.soloInscribe = 0 OR e.soloInscribe IS NULL
                                        GROUP BY t.id")
                        ->getArrayResult();
        ;
    }
}