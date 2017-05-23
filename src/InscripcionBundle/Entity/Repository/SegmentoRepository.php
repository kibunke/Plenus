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
                                        LEFT JOIN eq.equipoCompetidores eqc
                                        LEFT JOIN eqc.competidor com
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
                        ->createQuery(" SELECT COUNT(DISTINCT(com.id)) as cant, e.nombre
                                        FROM InscripcionBundle:Segmento s
                                        LEFT JOIN s.planillas p
                                        LEFT JOIN p.estados e
                                        LEFT JOIN p.equipos eq
                                        LEFT JOIN eq.equipoCompetidores eqc
                                        LEFT JOIN eqc.competidor com
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


    public function dataTableInscripcion($request,$user,$auth_checker)
    {
        return array(
                      "total"    => $this->getTotalInscripcionRows($user,$auth_checker),
                      "filtered" => $this->getFilteredInscripcionRows($request,$user,$auth_checker),
                      "rows"     => $this->getInscripcionRows($request,$user,$auth_checker),
                      "actives"   => $this->getActives($user,$auth_checker)
            );
    }

    public function getInscripcionRows($request,$user,$auth_checker)
    {
        $columns = ["s.id",
                    "d.nombreRecursivo ".$request->get('order')[0]['dir'].
                    ",s.nombre ",
                    "",
                    "",
                    "",
                    ""];
        $where = "( s.id LIKE ?1 OR
                    s.nombre LIKE ?1 OR
                    d.nombre LIKE ?1 OR
                    d.nombreRecursivo LIKE ?1)";
        if($user->hasRole('ROLE_COORDINADOR')){
            //COORDINADORES ven los sus Segmentos
            $where .= " AND (coordinador.id = " . $user->getId().")";
        }
        return $this->getEntityManager()
                        ->createQuery(" SELECT s
                                        FROM InscripcionBundle:Segmento s
                                        JOIN s.disciplina d
                                        LEFT JOIN s.coordinadores coordinador
                                        WHERE $where
                                        GROUP BY s
                                        ORDER BY ".$columns[$request->get('order')[0]['column']]." ".$request->get('order')[0]['dir'])
                        ->setParameter(1,'%'.$request->get('search')['value'].'%')
                        ->setMaxResults($request->get('length'))
                        ->setFirstResult($request->get('start'))
                        ->getResult();
    }

    public function getFilteredInscripcionRows($request,$user,$auth_checker)
    {
        $where = "( s.id LIKE ?1 OR
                    s.nombre LIKE ?1 OR
                    d.nombre LIKE ?1 OR
                    d.nombreRecursivo LIKE ?1)";
        if($user->hasRole('ROLE_COORDINADOR')){
            //COORDINADORES ven los sus Segmentos
            $where .= " AND (coordinador.id = " . $user->getId().")";
        }
        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(DISTINCT(s))
                                        FROM InscripcionBundle:Segmento s
                                        JOIN s.disciplina d
                                        LEFT JOIN s.coordinadores coordinador
                                        WHERE $where")
                        ->setParameter(1,'%'.$request->get('search')['value'].'%')
                        ->getSingleScalarResult();
    }

    public function getTotalInscripcionRows($user,$auth_checker)
    {
        $where = '';
        if($user->hasRole('ROLE_COORDINADOR')){
            //COORDINADORES ven los sus Segmentos
            $where = " coordinador.id = " . $user->getId();
        }
        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(DISTINCT(s))
                                        FROM InscripcionBundle:Segmento s
                                        LEFT JOIN s.coordinadores coordinador
                                        WHERE $where ")
                        ->getSingleScalarResult();
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

    /*
     * Se utiliza para armar el arbol de segmentos de inscripcion
    */
    public function getAllConNombre($security = NULL)
    {
        $q=" SELECT t.id as torneo,
                    d.id as disciplina,
                    s.id as segmento,
                    CONCAT(t.nombre,'-',d.nombre,'-',c.nombre,'-',g.nombre,'-',m.nombre) as nombre
            FROM InscripcionBundle:Segmento s
            JOIN s.torneo t
            JOIN s.disciplina d
            JOIN s.categoria c
            JOIN s.modalidad m
            JOIN s.genero g
            WHERE 1 = 1";

        if ($security && !$security->isGranted('ROLE_DIRECTOR')){
            $q = $this->getEntityManager()->createQuery($q." AND ?1 MEMBER OF s.coordinadores ORDER BY t.id,d.id,s.id")
                        ->setParameter(1, $security->getToken()->getUser()->getId());
        }else{
            $q = $this->getEntityManager()->createQuery($q." ORDER BY t.id,d.id,s.id");
        }

        return $q->getArrayResult();
    }

    public function getResumenPorSegmentos($segmentos)
    {
        if (!$segmentos) return [];
        $query= '
                SELECT m.id,m.nombre,m.cruceRegional,m.regionDeportiva,pla.segmento, pla.planillas, pla.equipos, pla.inscriptos
                FROM Municipio as m
                LEFT JOIN (
                    SELECT p.segmento, p.municipio, COUNT(p.id) as "planillas", COUNT(Equipo.id) as "equipos", COUNT(EquiposCompetidores.id) as "inscriptos"
                    FROM Planilla as p
                    INNER JOIN Equipo ON Equipo.planilla = p.id
                    INNER JOIN EquiposCompetidores ON EquiposCompetidores.equipo_id = Equipo.id
                    WHERE p.segmento in ('.implode(',', $segmentos).')
                    GROUP BY p.segmento,p.municipio
                ) as pla ON pla.municipio = m.id
                WHERE m.idProvincia = 1
                ORDER BY m.id,pla.segmento
            ';
        return $this->getEntityManager()->getConnection()->query($query)->fetchAll();
    }
}
