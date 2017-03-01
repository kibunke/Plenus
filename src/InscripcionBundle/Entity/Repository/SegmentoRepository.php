<?php

namespace InscripcionBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * SegmentoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SegmentoRepository extends EntityRepository
{
    public function dataTable($request,$user,$auth_checker)
    {
        return array(
                      "total"    => $this->getTotalRows($user,$auth_checker),
                      "filtered" => $this->getFilteredRows($request,$user,$auth_checker),
                      "rows"     => $this->getRows($request,$user,$auth_checker),
                      "actives"   => $this->getActives($user,$auth_checker)
            );
    }
    
    public function getRows($request,$user,$auth_checker)
    {
        $columns = ["s.id",
                    "d.nombreRecursivo ".$request->get('order')[0]['dir'].
                    ",c.nombre ".$request->get('order')[0]['dir'].
                    ",g.nombre ".$request->get('order')[0]['dir'].
                    ",m.nombre ".$request->get('order')[0]['dir'].
                    ",s.nombre ",
                    "planillas",
                    "inscriptos",                    
                    "eventos",
                    "coordinadores"];
        $where = "( s.id LIKE ?1 OR
                    s.nombre LIKE ?1 OR
                    d.nombre LIKE ?1 OR
                    d.nombreRecursivo LIKE ?1 OR
                    t.nombre LIKE ?1 OR
                    g.nombre LIKE ?1 OR
                    c.nombre LIKE ?1 OR
                    m.nombre LIKE ?1)"
                    . $this->applyRoleFilter($user,$auth_checker);
                    
        return $this->getEntityManager()
                        ->createQuery(" SELECT s,s.id AS HIDDEN, COUNT(DISTINCT(e)) AS HIDDEN eventos, COUNT(DISTINCT(u)) AS HIDDEN coordinadores, COUNT(DISTINCT(com)) as HIDDEN inscriptos, COUNT(DISTINCT(p)) as HIDDEN planillas
                                        FROM InscripcionBundle:Segmento s
                                        JOIN s.disciplina d
                                        JOIN s.torneo t
                                        JOIN s.categoria c
                                        JOIN s.modalidad m
                                        JOIN s.genero g
                                        LEFT JOIN s.coordinadores u
                                        LEFT JOIN s.eventos e
                                        LEFT JOIN s.planillas p
                                        LEFT JOIN p.equipos eq
                                        LEFT JOIN eq.competidores com
                                        WHERE $where
                                        GROUP BY s.id
                                        ORDER BY ".$columns[$request->get('order')[0]['column']]." ".$request->get('order')[0]['dir'])
                        ->setParameter(1,'%'.$request->get('search')['value'].'%')
                        ->setMaxResults($request->get('length'))
                        ->setFirstResult($request->get('start'))
                        ->getResult();
    }
    
    public function getFilteredRows($request,$user,$auth_checker)
    {
        $where = "( s.id LIKE ?1 OR
                    s.nombre LIKE ?1 OR
                    d.nombre LIKE ?1 OR
                    d.nombreRecursivo LIKE ?1 OR
                    t.nombre LIKE ?1 OR
                    g.nombre LIKE ?1 OR
                    c.nombre LIKE ?1 OR
                    m.nombre LIKE ?1)"
                    . $this->applyRoleFilter($user,$auth_checker);
        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(DISTINCT(s))
                                        FROM InscripcionBundle:Segmento s
                                        JOIN s.disciplina d
                                        JOIN s.torneo t
                                        JOIN s.categoria c
                                        JOIN s.modalidad m
                                        JOIN s.genero g
                                        LEFT JOIN s.coordinadores u
                                        WHERE $where")
                        ->setParameter(1,'%'.$request->get('search')['value'].'%')
                        ->getSingleScalarResult();
    }
    
    public function getTotalRows($user,$auth_checker)
    {
        $where = "1 = 1". $this->applyRoleFilter($user,$auth_checker);
        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(DISTINCT(s))
                                        FROM InscripcionBundle:Segmento s
                                        LEFT JOIN s.coordinadores u
                                        WHERE $where ")
                        ->getSingleScalarResult();
    }

    public function getActives($user,$auth_checker)
    {
        $where = "s.isActive = 1". $this->applyRoleFilter($user,$auth_checker);
        
        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(DISTINCT(s))
                                        FROM InscripcionBundle:Segmento s
                                        LEFT JOIN s.coordinadores u
                                        WHERE $where")
                        ->getSingleScalarResult();
    }
    
    public function getTotalInscriptos($segmento,$user)
    {
        $where = "s.id = ". $segmento->getId();
        $estados = array_map('current',$this->getEntityManager()
                        ->createQuery(" SELECT MAX(e.id)
                                        FROM InscripcionBundle:Segmento s
                                        JOIN s.planillas p
                                        JOIN p.estados e
                                        WHERE $where
                                        GROUP BY p.id")
                        ->getArrayResult());
        $estados[]=0;
        $where .= " AND e.id IN (".implode(",",$estados).")". $this->applyRoleAndPlanillaFilter($user);

        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(com.id) as cant, e.nombre
                                        FROM InscripcionBundle:Segmento s
                                        LEFT JOIN s.planillas p
                                        LEFT JOIN p.estados e
                                        LEFT JOIN p.equipos eq
                                        LEFT JOIN eq.competidores com
                                        JOIN p.municipio municipio
                                        JOIN p.createdBy creador
                                        LEFT JOIN s.coordinadores coordinador                                        
                                        WHERE $where
                                        GROUP BY s.id,e.nombre ")
                        ->getScalarResult();
    }
    
    
    public function getTotalPlanillas($segmento,$user)
    {
        $where = "s.id = ". $segmento->getId(). $this->applyRoleAndPlanillaFilter($user);
        
        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(DISTINCT(p)) as cant
                                        FROM InscripcionBundle:Segmento s
                                        LEFT JOIN s.planillas p
                                        JOIN p.municipio municipio
                                        JOIN p.createdBy creador
                                        LEFT JOIN s.coordinadores coordinador                                        
                                        WHERE $where
                                        GROUP BY s.id")
                        ->getOneOrNullResult();
    }
    
    private function applyRoleFilter($user,$auth_checker)
    {
        $where = '';
        if(!$auth_checker->isGranted('ROLE_ADMIN')){
            if($auth_checker->isGranted('ROLE_COORDINADOR')){
                $where .= " AND (u.id = " . $user->getId() . ")";
            }else{
                $where .= " AND (s.isActive = 1)";
            }
        }
        return $where;
    }
    
    private function applyRoleAndPlanillaFilter($user)
    {
        $where = '';
        if(!$user->hasRole('ROLE_ADMIN')){
            if($user->hasRole('ROLE_COORDINADOR')){
                //COORDINADORES ven todas las planillas de sus Segmentos
                $where .= " AND (coordinador.id = " . $user->getId() . ")";
            }elseif($user->hasRole('ROLE_ORGANIZADOR')){
                //ORGANIZADORES ven todas las planillas de su Municipio
                $where .= " AND (municipio.id = " . $user->getMunicipio()->getId() . ")";
            }else{
                //INSCRIPTORES ven todas las planillas que crearon
                $where .= " AND (creador.id = " . $user->getId() . ")";
            }
        }
        return $where;
    }
}