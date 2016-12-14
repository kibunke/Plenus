<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\CompetenciaLigaTantos
 *
 * @ORM\Entity()
 *
 */
class CompetenciaLigaTantos extends CompetenciaLiga
{
    /*                   00    01   02   03   04   05   06   07   08   09   10  */
    private $columns = ["Pts","PJ","PG","PE","PP","SF","SC","DS","TF","TC","DT"];
    
    private $row = [0,0,0,0,0,0,0,0,0,0,0];
    
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
            $ds[] = $aux[7];
            $sf[] = $aux[5];
            $se[] = $aux[6];
            $dt[] = $aux[10];
            $tf[] = $aux[8];
            $te[] = $aux[9];
            $plAux[] = $plaza->getId();
        }
        
        $arr = array_multisort(
                    $pts, SORT_DESC, SORT_NUMERIC,
                    $ds, SORT_DESC, SORT_NUMERIC,
                    $sf, SORT_DESC, SORT_NUMERIC,
                    $se, SORT_ASC, SORT_NUMERIC,
                    $dt, SORT_DESC, SORT_NUMERIC,
                    $tf, SORT_DESC, SORT_NUMERIC,
                    $te, SORT_ASC, SORT_NUMERIC,
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
        return new PartidoTantos($user, $nombre);
    }    
}
