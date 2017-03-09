<?php

namespace InscripcionBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PlanillaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlanillaRepository extends EntityRepository
{
    public function getDashboard()
    {
        return  $this->getEntityManager()
                        ->createQuery(" SELECT t.id, t.nombre, sex.nombre as sexoNombre, COUNT(DISTINCT(p)) as planillas, COUNT(DISTINCT(e)) as equipos, COUNT(DISTINCT(c)) as inscriptos, COUNT(DISTINCT(sex)) as sexo
                                        FROM InscripcionBundle:Planilla p
                                        JOIN p.segmento s
                                        JOIN s.torneo t
                                        LEFT JOIN p.equipos e
                                        LEFT JOIN e.competidores c
                                        LEFT JOIN c.genero sex
                                        GROUP BY t.id,sex.id")
                        ->getArrayResult();
    }    
    
    private $onlyPendientes = false;
    private $idEstatos;
    
    public function dataTable($request,$user,$auth_checker,$onlyPendientes)
    {
        //SELECT MAX(PlanillaEstado.id) FROM PlanillaEstado INNER JOIN Planilla ON (PlanillaEstado.planilla=Planilla.id) GROUP BY Planilla.id

        $estados = array_map('current',$this->getEntityManager()
                        ->createQuery(" SELECT MAX(e.id)
                                        FROM InscripcionBundle:PlanillaEstado e
                                        GROUP BY e.planilla")
                        ->getArrayResult());
        $estados[]=0;
        $this->idEstatos = implode(",",$estados);
        
        $this->onlyPendientes = $onlyPendientes;
        return array(
                      "total"    => $this->getTotalRows($user,$auth_checker),
                      "filtered" => $this->getFilteredRows($request,$user,$auth_checker),
                      "rows"     => $this->getRows($request,$user,$auth_checker),
            );
    }
    
    public function getRows($request,$user,$auth_checker)
    {
        $columns = ["p.id",
                    "t.nombre ".$request->get('order')[0]['dir'].
                    ",d.nombre ".$request->get('order')[0]['dir'].
                    ",c.nombre ".$request->get('order')[0]['dir'].
                    ",g.nombre ".$request->get('order')[0]['dir'].
                    ",m.nombre ".$request->get('order')[0]['dir'].
                    ",s.nombre ",
                    "eventos"];
        $where = "( p.id LIKE ?1 OR
                    s.nombre LIKE ?1 OR
                    municipio.nombre LIKE ?1 OR
                    d.nombre LIKE ?1 OR
                    t.nombre LIKE ?1 OR
                    g.nombre LIKE ?1 OR
                    c.nombre LIKE ?1 OR
                    m.nombre LIKE ?1)". $this->applyRoleFilter($user,$auth_checker);;
                
        return $this->getEntityManager()
                        ->createQuery(" SELECT p
                                        FROM InscripcionBundle:Planilla p
                                        JOIN p.municipio municipio
                                        JOIN p.segmento s
                                        JOIN p.createdBy creador
                                        LEFT JOIN s.coordinadores coordinador
                                        JOIN s.disciplina d
                                        JOIN s.torneo t
                                        JOIN s.categoria c
                                        JOIN s.modalidad m
                                        JOIN s.genero g
                                        LEFT JOIN s.eventos e
                                        JOIN p.estados est
                                        WHERE $where
                                        ORDER BY ".$columns[$request->get('order')[0]['column']]." ".$request->get('order')[0]['dir'])
                        ->setParameter(1,'%'.$request->get('search')['value'].'%')
                        ->setMaxResults($request->get('length'))
                        ->setFirstResult($request->get('start'))
                        ->getResult();
    }
    
    public function getFilteredRows($request,$user,$auth_checker)
    {
        $where = "( p.id LIKE ?1 OR
                    s.nombre LIKE ?1 OR
                    municipio.nombre LIKE ?1 OR
                    d.nombre LIKE ?1 OR
                    t.nombre LIKE ?1 OR
                    g.nombre LIKE ?1 OR
                    c.nombre LIKE ?1 OR
                    m.nombre LIKE ?1)". $this->applyRoleFilter($user,$auth_checker);;
                
        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(DISTINCT(p))
                                        FROM InscripcionBundle:Planilla p
                                        JOIN p.municipio municipio
                                        JOIN p.segmento s
                                        JOIN p.createdBy creador
                                        LEFT JOIN s.coordinadores coordinador
                                        JOIN s.disciplina d
                                        JOIN s.torneo t
                                        JOIN s.categoria c
                                        JOIN s.modalidad m
                                        JOIN s.genero g
                                        JOIN p.estados est
                                        WHERE $where ")
                        ->setParameter(1,'%'.$request->get('search')['value'].'%')
                        ->getSingleScalarResult();
    }
    
    public function getTotalRows($user,$auth_checker)
    {
        $where = "1 = 1". $this->applyRoleFilter($user,$auth_checker);
        return $this->getEntityManager()
                        ->createQuery(" SELECT COUNT(DISTINCT(p))
                                        FROM InscripcionBundle:Planilla p
                                        JOIN p.municipio municipio
                                        JOIN p.segmento s
                                        JOIN p.createdBy creador
                                        LEFT JOIN s.coordinadores coordinador
                                        JOIN p.estados est
                                        WHERE $where")
                        ->getSingleScalarResult();
    }
    
    private function applyRoleFilter($user,$auth_checker)
    {
        $where = " AND est.id IN (".$this->idEstatos.")";
        if(!$auth_checker->isGranted('ROLE_ADMIN')){
            if($auth_checker->isGranted('ROLE_COORDINADOR')){
                //COORDINADORES ven todas las planillas de sus Segmentos
                $where .= " AND (coordinador.id = " . $user->getId() . ")";
                if ($this->onlyPendientes){
                    $where .= " AND (est.nombre = 'Presentada' )";
                }
            }elseif($auth_checker->isGranted('ROLE_ORGANIZADOR')){
                //ORGANIZADORES ven todas las planillas de su Municipio
                $where .= " AND (municipio.id = " . $user->getMunicipio()->getId() . ")";
                if ($this->onlyPendientes){
                    $where .= " AND (est.nombre = 'Enviada' OR est.nombre = 'Observada' OR (creador.id = " . $user->getId() ." AND est.nombre = 'Cargada'))";
                }
            }else{
                //INSCRIPTORES ven todas las planillas que crearon
                $where .= " AND (creador.id = " . $user->getId() . ")";
                if ($this->onlyPendientes){
                    $where .= " AND (est.nombre = 'Cargada' OR est.nombre = 'En Revisión')";
                }
            }
        }else{
            if ($this->onlyPendientes){
                    //$where .= " AND (est.nombre = 'Publicada')";
                }
        }
        return $where;
    }
    
    //public function findAllByEvento($evento)
    //{
    //    return $this->createQueryBuilder('i')
    //        ->where('i.evento = ?1')
    //        ->setParameter(1, $evento)->getQuery()->getResult();
    //}
    //
    //public function countInscriptosByTorneo()
    //{
    //    $q=$this->getEntityManager()->createQuery(' SELECT t.id,t.nombre, SUM(i.cantidadFemeninos) as fem,SUM(i.cantidadMasculinos) as mas,SUM(i.cantidadMasculinos+i.cantidadFemeninos) as total
    //                                                FROM InscripcionBundle:Inscripto i
    //                                                JOIN i.evento e
    //                                                JOIN e.torneo t
    //                                                GROUP BY t.id
    //                                                ORDER BY t.id DESC'
    //            );
    //    return $q->getResult();
    //}
    //
    //public function getMunicipiosSinInscriptos()
    //{
    //    $q=$this->getEntityManager()->createQuery('
    //                                                SELECT m
    //                                                FROM CommonBundle:Partido m
    //                                                WHERE m.provincia = 1 AND m.id NOT IN (
    //                                                    SELECT m1.id
    //                                                    FROM InscripcionBundle:Inscripto i
    //                                                    JOIN i.municipio m1)'
    //            );
    //    return $q->getResult();
    //}
    //
    //public function getProgreso()
    //{
    //    $em=$this->getEntityManager();
    //    $q='SELECT SUM(i.cantidadMasculinos + i.cantidadFemeninos) as cant, DATE_FORMAT(i.createdAt,"%d-%m-%Y") as fecha
    //        FROM services_juegosba_final.Inscripto i
    //        WHERE DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)<= DATE(i.createdAt)
    //        GROUP BY fecha
    //        ORDER BY i.createdAt ASC';
    //    return $em->getConnection()->query($q)->fetchAll();
    //}
}