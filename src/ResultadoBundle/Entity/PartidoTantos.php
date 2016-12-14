<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use ResultadoBundle\Form\PartidoTantosType;

/**
 * ResultadoBundle\Entity\PartidoTantos
 *
 * @ORM\Entity()
 *
 */
class PartidoTantos extends Partido
{ 
    /**
     * @var integer $tanteador
     *
     * @ORM\Column(name="tanteador", type="string")
     */
    private $tanteador;    

    /*
     * Construct
     */
    public function __construct($user = NULL,$nombre = "") {
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
        $this->setTanteador(NULL);
        parent::resetResultado();
    }
    
    /** 
     * Get template
     *
     * @return class
     */
    public function getTemplate()
    {
        return "editResultado.tantos.html.twig";
    }
    
    /** 
     * Get formtype
     *
     * @return class
     */
    public function getFormType()
    {
        return new PartidoTantosType();
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
            $row[1]++;//PG
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
                $row[8]+=$this->getResultadoSecundarioLocal();
                $row[9]+=$this->getResultadoSecundarioVisitante();                
            }else{
                $row[5]+=$this->getResultadoVisitante();
                $row[6]+=$this->getResultadoLocal();
                $row[8]+=$this->getResultadoSecundarioVisitante();
                $row[9]+=$this->getResultadoSecundarioLocal();
            }
            $row[7] = $row[5] - $row[6];
            $row[10] = $row[8] - $row[9];
        }
        return $row;
    }

    /**
     * Set tanteador
     *
     * @param string $tanteador
     * @return PartidoTantos
     */
    public function setTanteador($tanteador)
    {
        $this->tanteador = $tanteador;

        return $this;
    }

    /**
     * Get tanteador
     *
     * @return string 
     */
    public function getTanteador()
    {
        return $this->tanteador;
    }
}
