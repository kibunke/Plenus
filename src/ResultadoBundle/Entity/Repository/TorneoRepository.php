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
    public function getResumen()
    {
        return $this->getEntityManager()
                    ->createQuery(" SELECT t.id,t.nombre,COUNT(eqc.id) as total
                                    FROM ResultadoBundle:Torneo t
                                    JOIN t.segmentos seg
                                    JOIN seg.planillas pla
                                    JOIN pla.equipos eq
                                    JOIN eq.equipoCompetidores eqc
                                    GROUP BY t.id")
                    ->getArrayResult();
    }

    public function getResumenPorMunicipio()
    {
        return array(
                        'torneos' => $this->getResumen(),
                        'totalPorMunicipio' => $this->getEntityManager()
                                                    ->createQuery(" SELECT t.id as torneoId, mun.id as municipioId, mun.nombre as municipio, t.nombre,COUNT(eqc.id) as total
                                                                    FROM ResultadoBundle:Torneo t
                                                                    JOIN t.segmentos seg
                                                                    JOIN seg.planillas pla
                                                                    JOIN pla.municipio mun
                                                                    JOIN pla.equipos eq
                                                                    JOIN eq.equipoCompetidores eqc
                                                                    GROUP BY t.id,mun.id")
                                                    ->getArrayResult()
                    );
    }

    public function dataTable($request)
    {
        return array(
                      "total"    => $this->getTotalRows(),
                      "filtered" => $this->getFilteredRows($request),
                      "rows"     => $this->getRows($request)
            );
    }

    public function getRows($request)
    {
        $columns = ["t.id","t.nombre"];
        $where = "(t.id LIKE ?1 OR t.nombre LIKE ?1)";

        return $this->getEntityManager()
                        ->createQuery(" SELECT t
                                        FROM ResultadoBundle:Torneo t
                                        WHERE $where
                                        ORDER BY ".$columns[$request->get('order')[0]['column']]." ".$request->get('order')[0]['dir'])
                        ->setParameter(1,'%'.$request->get('search')['value'].'%')
                        ->setMaxResults($request->get('length'))
                        ->setFirstResult($request->get('start'))
                        ->getResult();
    }

    public function getFilteredRows($request)
    {
        $where = "(t.id LIKE ?1 OR t.nombre LIKE ?1)";

        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(t)
                                        FROM ResultadoBundle:Torneo t
                                        WHERE $where ")
                        ->setParameter(1,'%'.$request->get('search')['value'].'%')
                        ->getSingleScalarResult();
    }

    public function getTotalRows()
    {
        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(t)
                                        FROM ResultadoBundle:Torneo t")
                        ->getSingleScalarResult();
    }
}
