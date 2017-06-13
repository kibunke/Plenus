<?php

namespace ResultadoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * CompetidorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompetidorRepository extends EntityRepository
{
    public function dataTable($request,$user)
    {
        return array(
                        "total"    => $this->getTotalRows($request,$user),
                        "filtered" => $this->getFilteredRows($request,$user),
                        "rows"     => $this->getRows($request,$user)
                    );
    }

    public function getRows($request,$user)
    {
        $columns = ["c.id",
                    "c.apellido ".$request->get('order')[0]['dir'].",c.nombre" ,
                    "c.dni",
                    "m.nombre","","","actions"];
        $query = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('c')
                        ->from('ResultadoBundle:Competidor', 'c')
                        ->join('c.competidorEquipos', 'ce')
                        ->join('ce.equipo', 'e')
                        ->join('e.planilla', 'p')
                        ->join('p.municipio', 'mp')
                        ->join('c.municipio', 'mc');
        $searchByPlanilla = !(strpos($request->get('search')['value'],'P:') === false);
        $searchValue = $searchByPlanilla ? explode('P:',$request->get('search')['value'])[1] : '%'.$request->get('search')['value'].'%';
        if ($searchByPlanilla){
            $query->where('p.id = ?1');
        }else {
            $query->where('c.apellido LIKE ?1 OR c.nombre LIKE ?1 OR c.dni LIKE ?1 OR mc.nombre LIKE ?1');
        }
        if (!$user->hasRole('ROLE_INSCRIPCION_COMPETIDORES_LIST_ALL')){
            $query
                    ->andwhere('mp = ?2')
                    ->setParameter(2,$user->getMunicipio());
        }
        return $query->setParameter(1,$searchValue)
                    ->orderBy($columns[$request->get('order')[0]['column']],$request->get('order')[0]['dir'])
                    ->setMaxResults($request->get('length'))
                    ->setFirstResult($request->get('start'))
                    ->getQuery()
                    ->getResult();
    }

    public function getFilteredRows($request,$user)
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('COUNT(DISTINCT(c))')
                        ->from('ResultadoBundle:Competidor', 'c')
                        ->join('c.competidorEquipos', 'ce')
                        ->join('ce.equipo', 'e')
                        ->join('e.planilla', 'p')
                        ->join('p.municipio', 'mp')
                        ->join('c.municipio', 'mc');
        $searchByPlanilla = !(strpos($request->get('search')['value'],'P:') === false);
        $searchValue = $searchByPlanilla ? explode('P:',$request->get('search')['value'])[1] : '%'.$request->get('search')['value'].'%';
        if ($searchByPlanilla){
            $query->where('p.id = ?1');
        }else {
            $query->where('c.apellido LIKE ?1 OR c.nombre LIKE ?1 OR c.dni LIKE ?1 OR mc.nombre LIKE ?1');
        }
        if (!$user->hasRole('ROLE_INSCRIPCION_COMPETIDORES_LIST_ALL')){
            $query
                    ->andwhere('mp = ?2')
                    ->setParameter(2,$user->getMunicipio());
        }
        return $query->getQuery()->setParameter(1,$searchValue)->getSingleScalarResult();
    }


    public function getTotalRows($request,$user)
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('COUNT(DISTINCT(c))')
                        ->from('ResultadoBundle:Competidor', 'c');
        if (!$user->hasRole('ROLE_INSCRIPCION_COMPETIDORES_LIST_ALL')){
            $query->join('c.competidorEquipos', 'ce')
                    ->join('ce.equipo', 'e')
                    ->join('e.planilla', 'p')
                    ->join('p.municipio', 'm')
                    ->where('m = ?1')
                    ->setParameter(1,$user->getMunicipio());
        }
        return $query->getQuery()->getSingleScalarResult();
    }

    public function count()
    {
        return $this->getEntityManager()
                    ->createQuery("SELECT COUNT(c)
                                    FROM ResultadoBundle:Competidor c")
                    ->getSingleScalarResult();
    }

    // public function getCompetidoresSospechosos()
    // {
    //     return $this->getEntityManager()
    //                     ->createQuery(' SELECT p
    //                                     FROM ResultadoBundle:Competidor p
    //                                     JOIN p.equipos e
    //                                     GROUP BY p.id
    //                                     HAVING COUNT(p.id) > 1
    //                                     ORDER BY p.apellido,p.nombre'
    //                                 )
    //                     ->getResult();
    //     ;
    // }
    //
    // public function getCompetidorByDni($competidor)
    // {
    //     return $this->getEntityManager()
    //                     ->createQuery(' SELECT p
    //                                     FROM ResultadoBundle:Competidor p
    //                                     WHERE p.documentoNro = ?1 AND p.id <> ?2')
    //                     ->setParameter(1,$competidor->getDocumentoNro())
    //                     ->setParameter(2,$competidor->getId()?$competidor->getId():0)
    //                     ->getoneornullresult();
    //     ;
    // }
    //
    // public function getNombresDpl()
    // {
    //     return $this->getEntityManager()
    //                     ->createQuery(' SELECT p
    //                                     FROM ResultadoBundle:Competidor p
    //                                     WHERE  EXISTS (SELECT p1.id
    //                                                     FROM ResultadoBundle:Competidor p1
    //                                                     WHERE UPPER(p1.nombre) = UPPER(p.nombre) AND UPPER(p.apellido) = UPPER(p1.apellido) AND p.id <> p1.id
    //                                                 )
    //                                     ORDER BY p.apellido,p.nombre'
    //                                 )
    //                     ->getResult();
    //     ;
    // }
    //
    // public function getDniMay99M()
    // {
    //     return $this->getEntityManager()
    //                     ->createQuery(' SELECT p
    //                                     FROM ResultadoBundle:Competidor p
    //                                     WHERE p.documentoNro >99000000
    //                                     ORDER BY p.apellido,p.nombre'
    //                                 )
    //                     ->getResult();
    //     ;
    // }
    //
    // public function getDniMen1M()
    // {
    //     return $this->getEntityManager()
    //                     ->createQuery(' SELECT p
    //                                     FROM ResultadoBundle:Competidor p
    //                                     WHERE p.documentoNro < 1000000
    //                                     ORDER BY p.apellido,p.nombre'
    //                                 )
    //                     ->getResult();
    //     ;
    // }
}
