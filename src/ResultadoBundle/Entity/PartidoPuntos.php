<?php
namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use ResultadoBundle\Form\PartidoPuntosType;

/**
 * ResultadoBundle\Entity\PartidoPuntos
 *
 * @ORM\Entity()
 *
 */
class PartidoPuntos extends Partido
{
    /*
     * Construct
     */
    public function __construct($user = NULL, $nombre = "") {
        $this->setNombre($nombre);
        parent::__construct($user);
    }
    
    /**
     * resetResultado
     *
     * @return boolean
     */
    public function resetResultado()
    {
        parent::resetResultado();
    }
    
    /** 
     * Get template
     *
     * @return class
     */
    public function getTemplate()
    {
        return "editResultado.puntos.html.twig";
    }
    
    /** 
     * Get formtype
     *
     * @return class
     */
    public function getFormType()
    {
        return new PartidoPuntosType();
    }   
    
    /** 
     * Get empato
     *
     * @return boolean
     */
    public function empato($plaza)
    {    
        if ( ($this->getResultadoLocal() == $this->getResultadoVisitante()) && ! $this->getResultadoSecundarioLocal())
            return true;
        return false;
    }
    
    /** 
     * Get gano
     *
     * @return boolean
     */
    public function gano($plaza)
    {
        if ($this->soyLocal($plaza)){
            if ( $this->getResultadoLocal() > $this->getResultadoVisitante() || (
                    $this->getResultadoLocal() == $this->getResultadoVisitante() &&
                    $this->getResultadoSecundarioLocal() > $this->getResultadoSecundarioVisitante()
                    )
                )
                return true;
        }else{
            if ( $this->getResultadoLocal() < $this->getResultadoVisitante() || (
                    $this->getResultadoLocal() == $this->getResultadoVisitante() &&
                    $this->getResultadoSecundarioLocal() < $this->getResultadoSecundarioVisitante()
                    )
                )
                return true;
        }
        return false;
    }
    
    /** 
     * Get puntos
     *
     * @return array
     */
    public function calculate($row,$plaza)
    {
        if ($this->jugado())
        {
            $parametros = json_decode($this->getZona()->getLiga()->getParametros());
            $row[1]++;//PJ
            //Sumar puntos y PG,PE,PP
            if ($this->empato($plaza)){
                $row[0] = $row[0] + $parametros->PE;//Pts
                $row[3]++;//PE
            }else if ($this->gano($plaza)){
                $row[0] = $row[0] + $parametros->PG;//Pts
                $row[2]++;//PG
            }else{
                $row[0] = $row[0] + $parametros->PP;//Pts
                $row[4]++;//PP
            }
            if ($this->soyLocal($plaza)){
                $row[5]+=$this->getResultadoLocal();
                $row[6]+=$this->getResultadoVisitante();
            }else{
                $row[5]+=$this->getResultadoVisitante();
                $row[6]+=$this->getResultadoLocal();
            }
            $row[7] = $row[5] - $row[6];
        }
        return $row;
    }
}
