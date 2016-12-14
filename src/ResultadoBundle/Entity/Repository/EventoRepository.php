<?php

namespace ResultadoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * EventoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventoRepository extends EntityRepository
{
    public function getAllPorUsuarioSinSoloInscribe($security){
        return $this->getAllPorUsuarioSinSoloInscribeBuilder($security)->getQuery()->getResult();
    }
    
    public function getAllPorUsuarioSinSoloInscribeBuilder($security)
    {
        $q = $this->createQueryBuilder('e')
            ->where('e.soloInscribe = 0 OR e.soloInscribe IS NULL');
        //if (!$security->isGranted('ROLE_DIRECTOR')){
        if (!($security->isGranted('ROLE_DIRECTOR') || $security->isGranted('ROLE_DATAENTRY') || $security->isGranted('ROLE_CONTROL_COMPETENCIA'))){
            $q->andwhere('?1 MEMBER OF e.coordinadores')->setParameter(1, $security->getToken()->getUser()->getId());
        }
        return $q->orderBy('e.nombre');        
    }
    
    public function getAllPorUsuarioSinDesiertos($security){
        return $this->getAllPorUsuarioSinDesiertosBuilder($security)->getQuery()->getResult();
    }
    
    public function getAllPorUsuarioSinDesiertosBuilder($security)
    {
        $q = $this->createQueryBuilder('e')
            ->where('e.soloInscribe = 0 OR e.soloInscribe IS NULL AND SIZE(e.equipos) <> 0');
            //->where('e.soloInscribe = 0 OR e.soloInscribe IS NULL');
        if (!($security->isGranted('ROLE_DIRECTOR') || $security->isGranted('ROLE_DATAENTRY') || $security->isGranted('ROLE_CONTROL_COMPETENCIA'))){
            $q->andwhere('?1 MEMBER OF e.coordinadores')->setParameter(1, $security->getToken()->getUser()->getId());
        }
        return $q->orderBy('e.nombre');        
    }
    
    
    public function getAllByUserAndInscribe($security){
        return $this->getAllByUserAndInscribeBuilder($security)->getQuery()->getResult();
    }
    
    public function getAllByUserAndInscribeBuilder($security)
    {
        $q = $this->createQueryBuilder('e')
            ->where('e.inscribe = 1');
        //if (!$security->isGranted('ROLE_DIRECTOR')){
        if (!($security->isGranted('ROLE_DIRECTOR') || $security->isGranted('ROLE_DATAENTRY') || $security->isGranted('ROLE_CONTROL_COMPETENCIA'))){
            $q->andwhere('?1 MEMBER OF e.coordinadores')->setParameter(1, $security->getToken()->getUser()->getId());
        }
        return $q;//
    }
    
    public function getAllConNombre($security=NULL,$conSoloInscribe=true)
    {
        $q=" SELECT t.id as torneo,
                    d.id as disciplina,
                    e.id as evento,
                    CONCAT(t.nombre,'-',d.nombre,'-',c.nombre,'-',g.nombre,'-',m.nombre) as nombre
            FROM ResultadoBundle:Evento e
            JOIN e.torneo t
            JOIN e.disciplina d
            JOIN e.categoria c
            JOIN e.modalidad m
            JOIN e.genero g";
        if ($conSoloInscribe){
            $q.=" WHERE e.inscribe = 1";
        }else{
            $q.=" WHERE 1 = 1";
        }
        if ($security && !$security->isGranted('ROLE_DIRECTOR')){
            $q = $this->getEntityManager()->createQuery($q." AND ?1 MEMBER OF e.coordinadores ORDER BY t.id,d.id,e.id")
                        ->setParameter(1, $security->getToken()->getUser()->getId());
        }else{
            $q = $this->getEntityManager()->createQuery($q." ORDER BY t.id,d.id,e.id");
        }
        
        return $q->getArrayResult();
    }
    
    public function getCoordinadoresAsArray($evento)
    {
        return $this->getEntityManager()
                        ->createQuery(' SELECT e.id FROM ResultadoBundle:Evento e 
                                        JOIN e.coordinadores c
                                        WHERE e = ?1')
                        ->setParameter(1,$evento)
                        ->getArrayResult();
        ;
    }
    
    public function getAnaliticoDeEvento($evento=NULL)
    {
        if (!$evento) return [];
        $query= '
                SELECT q.municipio,mun.nombre as municipioNombre, mun.cruceRegional as regional, 
                    SUM(q.h1) as cantidadMasculinosMunicipio, 
                    SUM(q.m1) as cantidadFemeninosMunicipio, 
                    SUM(q.h2) as cantidadMasculinosEscuela,
                    SUM(q.m2) as cantidadFemeninosEscuela,
                    SUM(q.h3) as cantidadMasculinosOtro,
                    SUM(q.m3) as cantidadFemeninosOtro
                FROM
                (
                    SELECT i.evento, i.municipio,
                    SUM(i.cantidadMasculinos) as h1,
                    SUM(i.cantidadFemeninos) as m1,
                    0 as h2,
                    0 as m2,
                    0 as h3,
                    0 as m3,
                    o.discr
                    FROM services_juegosba_final.Inscripto as i
                    LEFT JOIN services_juegosba_final.Origen as o ON i.origen = o.id
                    WHERE o.discr = "Municipio"
                    GROUP BY i.evento, i.municipio
                    
                    UNION ALL
                    
                    SELECT i.evento, i.municipio,
                    0 as h1,
                    0 as m1,
                    SUM(i.cantidadMasculinos) as h2,
                    SUM(i.cantidadFemeninos) as m2,
                    0 as h3,
                    0 as m3,
                    o.discr
                    FROM services_juegosba_final.Inscripto as i
                    LEFT JOIN services_juegosba_final.Origen as o ON i.origen = o.id
                    WHERE o.discr = "Escuela"
                    GROUP BY i.evento, i.municipio
                    
                    UNION ALL
                    
                    SELECT i.evento, i.municipio,
                    0 as h1,
                    0 as m1,
                    0 as h2,
                    0 as m2,
                    SUM(i.cantidadMasculinos) as h3,
                    SUM(i.cantidadFemeninos) as m3,
                    o.discr
                    FROM services_juegosba_final.Inscripto as i
                    LEFT JOIN services_juegosba_final.Origen as o ON i.origen = o.id
                    WHERE o.discr = "Otro"
                    GROUP BY i.evento, i.municipio
                ) as q
                LEFT JOIN services_juegosba_admin.Partido as mun ON q.municipio=mun.id';
        if ($evento)
            $query .= " WHERE q.evento in (".implode(",", $evento).")";
        $query.=" GROUP BY q.municipio";
        return $this->getEntityManager()->getConnection()->query($query)->fetchAll();
    }
    
    public function getAnaliticoDeFinalistas($evento=NULL)
    {
        if (!$evento) return [];
        $query= '
                SELECT q.municipio,mun.nombre as municipioNombre, mun.cruceRegional as regional,
                    mun.regionDeportiva as reg,
                    SUM(q.cantEquipos) as cantEquipos, 
                    SUM(q.cantParticipantes) as cantParticipantes
                FROM
                (
                    SELECT e.evento, e.municipio,
                        COUNT(e.id) as cantEquipos,
                        0 as cantParticipantes
                    FROM services_juegosba_final.Equipo as e
                    GROUP BY e.municipio,e.evento
                    
                    UNION ALL
                    
                    SELECT e.evento, e.municipio,
                        0 as cantEquipos,
                        COUNT(p.id) as cantParticipantes
                    FROM services_juegosba_final.Equipo as e
                    LEFT JOIN services_juegosba_final.equipo_participante as r ON r.equipo_id = e.id
                    LEFT JOIN services_juegosba_final.Persona as p ON r.participante_id = p.id
                    WHERE p.discr = "Participante"
                    GROUP BY e.municipio,e.evento
                ) as q
                LEFT JOIN services_juegosba_admin.Partido as mun ON q.municipio=mun.id';
        if ($evento)
            $query .= " WHERE q.evento in (".implode(",", $evento).")";
        $query.=" GROUP BY q.municipio";
        return $this->getEntityManager()->getConnection()->query($query)->fetchAll();
    }
    
    public function getConEquiposDelMunicipio($municipio,$security){
        $q="SELECT e
            FROM ResultadoBundle:Evento e 
            JOIN e.equipos eq
            JOIN eq.municipio mun
            WHERE mun = ?1";
        if ($security && !$security->isGranted('ROLE_DIRECTOR')){
            $q = $this->getEntityManager()->createQuery($q." AND ?2 MEMBER OF e.coordinadores")
                        ->setParameter(1,$municipio)
                        ->setParameter(2, $security->getToken()->getUser()->getId());
        }else{
            $q = $this->getEntityManager()->createQuery($q)->setParameter(1,$municipio);
        }
        
        return $q->getResult();      
    }
    
    public function getResumenRegionalPorEventos($evento=NULL)
    {
        if (!$evento) return [];
        $query= '
                SELECT m.id,m.nombre,m.cruceRegional,m.regionDeportiva,i.evento,SUM(i.cantidadMasculinos + i.cantidadFemeninos) as inscripcion
                FROM services_juegosba_admin.Partido as m
                LEFT JOIN (
                    SELECT *
                    FROM services_juegosba_final.Inscripto as ii';
        if ($evento)
            $query .= ' WHERE ii.evento in ('.implode(',', $evento).')';
        $query.=') as i ON i.municipio = m.id
                WHERE m.idProvincia = 1
                GROUP BY m.id,i.evento
                ORDER BY m.id,i.evento
            ';
        //print_r($this->getEntityManager()->getConnection()->query($query));die();
        return $this->getEntityManager()->getConnection()->query($query)->fetchAll();
    }
    
    
    /*
     * ESTO VA EN BLOQUE ES PARA ARMAR EL ARBOL DE EVENTOS DESDE DISCIPLINA    
    */
    
    public function getArbolAsArrayByEventos($eventos)
    {
        $eventosValidosIds=[];
        $disciplinasValidasIds=[];
        foreach($eventos as $evento)
        {
            $eventosValidosIds[]=$evento->getId();
            $disciplinasValidasIds = array_merge($disciplinasValidasIds,$evento->getDisciplina()->getIdRecursivo());
        }
        $disciplinasValidasIds = array_unique($disciplinasValidasIds);
        $disciplinas = $this->getOnlyRoot();
        return $this->getArbolAsArray($disciplinas,$disciplinasValidasIds,$eventosValidosIds);
    }
    public function getOnlyRoot()
    {          
        return $this->getEntityManager()
                        ->createQuery(' SELECT d
                                        FROM ResultadoBundle:Disciplina d
                                        WHERE d.padre IS NULL
                                        ORDER BY d.nombre')
                        ->getResult();
    }
    
    public function getArbolAsArray($disciplinas,$disciplinasValidasIds,$eventosValidosIds)
    {
        $resultado = array();
        foreach ($disciplinas as $item)
        {
            if (in_array($item->getId(), $disciplinasValidasIds)) {
                $aux = array();
                $evAux = [];
                $aux['text']  = $item->getNombre();
                $aux['di-']  = $item->getId();
                foreach($item->getEventos() as $ev){
                    if (in_array($ev->getId(), $eventosValidosIds)) {
                        $evAux[]=array(
                                       'text'=>$ev->getCategoria()."-".$ev->getGenero()."-".$ev->getModalidad(),
                                       'id'=>"ev-".$ev->getId(),
                                       'icon'=>"fa fa-thumb-tack text-green fa-lg"
                                       );
                    }
                }                
                if (count($item->getHijos()))
                {
                    $aux['children'] = array_merge($this->getArbolAsArray($item->getHijos(),$disciplinasValidasIds,$eventosValidosIds),$evAux);
                    //$aux['state'] = array('opened'=>false);
                    $aux["icon"] = "fa fa-folder text-red fa-lg";
                }else
                {
                    //$evAux = [];
                    //foreach($item->getEventos() as $ev){
                    //    if (in_array($ev->getId(), $eventosValidosIds)) {
                    //        $evAux[]=array(
                    //                       'text'=>$ev->getCategoria()."-".$ev->getGenero()."-".$ev->getModalidad(),
                    //                       'id'=>"ev-".$ev->getId(),
                    //                       'icon'=>"fa fa-thumb-tack text-green fa-lg"
                    //                       );
                    //    }
                    //}
                    $aux['children'] = $evAux;
                    $aux["icon"] = "fa fa-folder text-red fa-lg";
                        
                }
                $resultado[]=$aux;
            }
        }
        
        return $resultado;
    }
    
    /*
     * HASTA ACA EL BLOQUE PARA ARMAR EL ARBOL DE EVENTOS DESDE DISCIPLINA    
    */
    
    public function getByIds($ids)
    {
        return $this->getEntityManager()
                    ->createQuery(" SELECT ev
                                    FROM ResultadoBundle:Evento ev
                                    WHERE ev.id IN (?1)")
                    ->setParameter(1,$ids)
                    ->getResult();
        ;
    }
    
    public function getAllSinSoloInscribe()
    {
        $q = $this->createQueryBuilder('e')
            ->where('e.soloInscribe = 0 OR e.soloInscribe IS NULL');
        return $q->orderBy('e.nombre');        
    }
    
    public function getAllPorMunicipio($municipio)
    {
        return $this->getEntityManager()
                    ->createQuery(" SELECT ev
                                    FROM ResultadoBundle:Evento ev
                                    JOIN ev.equipos eq
                                    WHERE eq.municipio <> ?1")
                    ->setParameter(1,$municipio)
                    ->getResult();
        ;      
    }     
}