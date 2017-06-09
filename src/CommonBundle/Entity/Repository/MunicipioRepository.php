<?php

namespace CommonBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * MunicipioRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MunicipioRepository extends EntityRepository
{
    public function getAllArray()
    {
        return $this->getEntityManager()
                    ->createQuery(" SELECT mun.id, mun.nombre,mun.regionDeportiva as region
                                    FROM CommonBundle:Municipio mun")
                    ->getArrayResult();
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
        $columns = ["m.id","m.nombre","m.habitantes","m.seccionElectoral","m.regionDeportiva","m.cruceRegional"];
        $where = "( m.id LIKE ?1 OR
                    m.nombre LIKE ?1 OR
                    m.habitantes LIKE ?1 OR
                    m.regionDeportiva LIKE ?1 OR
                    m.seccionElectoral LIKE ?1 OR
                    m.cruceRegional LIKE ?1)";

        return $this->getEntityManager()
                        ->createQuery(" SELECT m
                                        FROM CommonBundle:Municipio m
                                        WHERE $where
                                        ORDER BY ".$columns[$request->get('order')[0]['column']]." ".$request->get('order')[0]['dir'])
                        ->setParameter(1,'%'.$request->get('search')['value'].'%')
                        ->setMaxResults($request->get('length'))
                        ->setFirstResult($request->get('start'))
                        ->getResult();
    }

    public function getFilteredRows($request)
    {
        $where = "( m.id LIKE ?1 OR
                    m.nombre LIKE ?1 OR
                    m.habitantes LIKE ?1 OR
                    m.regionDeportiva LIKE ?1 OR
                    m.seccionElectoral LIKE ?1 OR
                    m.cruceRegional LIKE ?1)";

        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(m)
                                        FROM CommonBundle:Municipio m
                                        WHERE $where")
                        ->setParameter(1,'%'.$request->get('search')['value'].'%')
                        ->getSingleScalarResult();
    }

    public function getTotalRows()
    {
        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(m)
                                        FROM CommonBundle:Municipio m")
                        ->getSingleScalarResult();
    }
}
