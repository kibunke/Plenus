<?php

namespace ResultadoBundle\Entity;

use ResultadoBundle\Form\PlazaCopaType;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\CompetenciaEliminacionDirecta
 *
 * @ORM\Entity()
 *
 */
class CompetenciaEliminacionDirecta extends Competencia
{    
    /**
     * @ORM\OneToMany(targetEntity="Partido", mappedBy="copa", cascade={"all"})
     */
    private $partidos;

    /** 
     * Get PlazaType
     *
     * @return \ResultadoBundle\Form\PlazaCopaType
     */
    public function getPlazaType()
    {
        return new PlazaCopaType();
    }
    
    /**
     * Get folder
     *
     * @return CompetenciaEliminacionDirecta
     */
    public function aplicarTemplate($user, $desde)
    {
        $desde = explode("-",$desde);
        //$partidos[]
        switch ($desde[0]){
            case "8vos":
                for( $i=1;$i<=8;$i++){
                    $partido = $this->newPartidoCopa($user);
                    $partido->setNombre("Partido 8vos de Final");
                    $partido->setNivel(4);
                    $partido->setOrden($i);
                    $this->addPartido($partido);
                }
            case "4tos":
                for( $i=1;$i<=4;$i++){
                    $partido = $this->newPartidoCopa($user);
                    $partido->setNombre("Partido 4tos de Final");
                    $partido->setNivel(3);
                    $partido->setOrden($i);
                    $this->addPartido($partido);
                }
            case "semi":
                for( $i=1;$i<=2;$i++){
                    $partido = $this->newPartidoCopa($user);
                    $partido->setNombre("Partido de Semfinal");
                    $partido->setNivel(2);
                    $partido->setOrden($i);
                    $this->addPartido($partido);
                }
            case "final":
                if ($desde[1]=="3ro"){
                    $partido = $this->newPartidoCopa($user);
                    $partido->setNombre("Partido por 3er puesto");
                    $partido->setNivel(0);
                    $partido->setOrden(1);
                    $this->addPartido($partido);                    
                }
                $partido = $this->newPartidoCopa($user);
                $partido->setNombre("Partido Final");
                $partido->setNivel(1);
                $partido->setOrden(1);
                $this->addPartido($partido);
        }
    }    

    /** 
     * Get newPartido
     *
     * @return \ResultadoBundle\Entity\Partido
     */
    public function newPartidoCopa($user = null, $nombre = "")
    {
        $partido = $this->newPartido($user, $nombre);
        $partido->setPlaza1(new PlazaCopa($user,"Plaza",99,$this));
        $partido->setPlaza2(new PlazaCopa($user,"Plaza",99,$this));            
        return $partido;
    }
    
    /** 
     * Get partidoCopa
     *
     * @return Array
     */
    public function getPartidosCopa()
    {
        $partidos = [];
        foreach($this->getPartidos() as $item){
            $partidos[$item->getNivelTexto()][]=$item;
        }
        return $partidos;
    }     

    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder()
    {
        return "Competencia/EliminacionDirecta";
    }
    /**
     * Constructor
     */
    public function __construct($user=null)
    {
        $this->partidos = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct($user);
    }

    /**
     * Add partidos
     *
     * @param \ResultadoBundle\Entity\Partido $partidos
     * @return CompetenciaEliminacionDirecta
     */
    public function addPartido(\ResultadoBundle\Entity\Partido $partidos)
    {
        $this->partidos[] = $partidos;
        $partidos->setCopa($this);

        return $this;
    }

    /**
     * Remove partidos
     *
     * @param \ResultadoBundle\Entity\Partido $partidos
     */
    public function removePartido(\ResultadoBundle\Entity\Partido $partidos)
    {
        $this->partidos->removeElement($partidos);
    }

    /**
     * Get partidos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartidos()
    {
        return $this->partidos->toArray();
    }
    
    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        $percent = 0;
        $plazas = [];
        $partidos = $this->getPartidos();
        foreach ($partidos as $partido){
            $percent += $partido->getState();
            $plazas[]=$partido->getPlaza1();
            $plazas[]=$partido->getPlaza2();
        }
        if ($percent)
            $percent = $percent/count($partidos);
            
        $plazaAsignada = 0;
        foreach ($plazas as $plaza){
            if ($plaza->getEquipo())
                $plazaAsignada++;
        }
        if ($plazaAsignada)
            $percent += ($plazaAsignada*100)/count($plazas);

        return round($percent/2,1);
        
    } 
}
