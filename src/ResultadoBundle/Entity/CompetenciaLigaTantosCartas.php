<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\CompetenciaLigaTantosCartas
 *
 * @ORM\Entity()
 *
 */
class CompetenciaLigaTantosCartas extends CompetenciaLiga
{
    /*                   00    01   02   03   04   05   06   07 */
    private $columns = ["Pts","PJ","PG","PE","PP","TF","TC","DT"];
    
    private $row = [0,0,0,0,0,0,0,0];
    
    /*
     * CONSTRUCT
     */
    public function __construct($user = NULL) {
       parent::__construct($user);
    }
    
   /**
     * recalcularOrdenNatural
     *
     * @return array
     */
    public function recalcularOrdenNatural($zona)
    {
        $pl = $zona->getPlazas()->toArray();
        // Obtener una lista de columnas
        foreach ( $pl as $plaza){
            $aux = $zona->getDetalleZona($plaza);
            $pts[] = $aux[0];
            $dt[] = $aux[7];
            $tf[] = $aux[5];
            $te[] = $aux[6];
            $plAux[] = $plaza->getId();
        }
        $parametros = json_decode($this->getParametros());
        if ($parametros->gana == "mayor"){        
            $arr = array_multisort(
                        $pts, SORT_DESC, SORT_NUMERIC,
                        $dt, SORT_DESC, SORT_NUMERIC,
                        $tf, SORT_DESC, SORT_NUMERIC,
                        $te, SORT_ASC, SORT_NUMERIC,
                        $plAux
                    );
        }else{
            $arr = array_multisort(
                        $pts, SORT_DESC, SORT_NUMERIC,
                        $dt, SORT_ASC, SORT_NUMERIC,
                        $tf, SORT_ASC, SORT_NUMERIC,
                        $te, SORT_DESC, SORT_NUMERIC,
                        $plAux
                    );            
        }
        foreach ( $pl as $plaza){
            $plaza->setOrdenNatural(array_search($plaza->getId(), $plAux));
        }        
        return $arr;
    }
    
    /** 
     * Get columns
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }
    
    /** 
     * Get columns
     *
     * @return array
     */
    public function getRow()
    {
        return $this->row;
    }
    
    /** 
     * Get newPartido
     *
     * @return \ResultadoBundle\Entity\Partido
     */
    public function newPartido($user = null, $nombre = "")
    {
        return new PartidoTantosCartas($user, $nombre);
    }    
}
