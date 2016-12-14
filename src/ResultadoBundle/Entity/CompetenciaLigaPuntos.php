<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\CompetenciaLigaPuntos
 *
 * @ORM\Entity()
 *
 */
class CompetenciaLigaPuntos extends CompetenciaLiga
{
    private $columns = ["Pts","PJ","PG","PE","PP","GF","GC","DG"];
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
        $pts = [];
        $dg = [];
        $gf = [];
        $ge = [];
        $pl = $zona->getPlazas()->toArray();
        // Obtener una lista de columnas
        foreach ( $pl as $plaza){
            $aux = $zona->getDetalleZona($plaza);
            $pts[] = $aux[0];
            $dg[] = $aux[7];
            $gf[] = $aux[5];
            $ge[] = $aux[6];
            $plAux[] = $plaza->getId();
        }
        
        $arr = array_multisort(
                    $pts, SORT_DESC, SORT_NUMERIC,
                    $dg, SORT_DESC, SORT_NUMERIC,
                    $gf, SORT_DESC, SORT_NUMERIC,
                    $ge, SORT_ASC, SORT_NUMERIC,
                    $plAux
                );
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
        return new PartidoPuntos($user,$nombre);
    }
    
    /**
     * Get performance
     *
     * @return string
     */
    public function getPerformance($plaza)
    {
        switch ($plaza->getOrden()){
            case 1: return "Medalla de Oro";
            case 2: return "Medalla de Plata";
            case 3: return "Medalla de Bronce";
            default: return null;
        }
    }    
}
