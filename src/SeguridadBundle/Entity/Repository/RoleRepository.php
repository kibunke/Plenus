<?php

namespace SeguridadBundle\Entity\Repository;

/**
 * RoleRepository
 */
class RoleRepository extends \Doctrine\ORM\EntityRepository
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
        $columns = ["id","name","description","isActive","perfiles"];
        $where = "(u.name LIKE ?1) OR (u.id LIKE ?1) OR (u.description LIKE ?1)";
                
        return $this->getEntityManager()
                        ->createQuery(" SELECT u
                                        FROM SeguridadBundle:Role u
                                        WHERE $where
                                        ORDER BY u.".$columns[$request->get('order')[0]['column']]." ".$request->get('order')[0]['dir']
                                        )                        
                        ->setParameter(1,'%'.$request->get('search')['value'].'%')
                        ->setMaxResults($request->get('length'))
                        ->setFirstResult($request->get('start'))
                        ->getResult();
    }
    
    public function getFilteredRows($request)
    {
        $where = "(u.name LIKE ?1) OR (u.id LIKE ?1) OR (u.description LIKE ?1)";
                
        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(u)
                                        FROM SeguridadBundle:Role u
                                        WHERE $where ")
                        ->setParameter(1,'%'.$request->get('search')['value'].'%')
                        ->getSingleScalarResult();
    }
    
    public function getTotalRows()
    {
        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(u)
                                        FROM SeguridadBundle:Role u
                                       ")
                        ->getSingleScalarResult();
    }
}

