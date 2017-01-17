<?php

namespace SeguridadBundle\Entity\Repository;

/**
 * PerfilRepository
 */
class PerfilRepository extends \Doctrine\ORM\EntityRepository
{
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
        $columns = ["p.id","p.legend","p.description","p.isActive"];
        $where = "(p.legend LIKE ?1) OR (p.name LIKE ?1) OR (p.description LIKE ?1)";
                
        return $this->getEntityManager()
                        ->createQuery(" SELECT p
                                        FROM SeguridadBundle:Perfil p
                                        WHERE $where
                                        ORDER BY ".$columns[$request->get('order')[0]['column']]." ".$request->get('order')[0]['dir']) 
                        ->setParameter(1,'%'.$request->get('search')['value'].'%')
                        ->setMaxResults($request->get('length'))
                        ->setFirstResult($request->get('start'))
                        ->getResult();
    }
    
    public function getFilteredRows($request)
    {
        $where = "(p.legend LIKE ?1) OR (p.name LIKE ?1) OR (p.description LIKE ?1)";
                
        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(p)
                                        FROM SeguridadBundle:Perfil p
                                        WHERE $where ")
                        ->setParameter(1,'%'.$request->get('search')['value'].'%')
                        ->getSingleScalarResult();
    }
    
    public function getTotalRows()
    {
        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(u)
                                        FROM SeguridadBundle:Perfil u
                                       ")
                        ->getSingleScalarResult();
    }
}

