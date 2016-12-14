<?php
namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\PlazaSerie
 *
 * @ORM\Entity
 * 
 */ 
class  PlazaSerie extends Plaza
{
    /**
    * @var string $descripcion
    *
    * @ORM\Column(name="marca", type="text", nullable=true)
    */
    private $marca;
    
    /**
     * @ORM\ManyToOne(targetEntity="Serie", inversedBy="plazas")
     * @ORM\JoinColumn(name="serie", referencedColumnName="id")
     */
    private $serie;
    
    /**
     * Construct
     */
    public function __construct($user,$competencia) {
        $this->setMarca($competencia->getDefaultMarca());
        $this->setCompetencia($competencia);
        //$competencia->getId();
        //die();
        parent::__construct($user);
    }
    
    /**
     * Set serie
     *
     * @param \ResultadoBundle\Entity\Serie $serie
     * @return Plaza
     */
    public function setSerie(\ResultadoBundle\Entity\Serie $serie = null)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie
     *
     * @return \ResultadoBundle\Entity\Serie 
     */
    public function getSerie()
    {
        return $this->serie;
    }
    
    /** 
     * Set marca
     *
     * @return Serie
     */
    public function setMarca($marca)
    {
        $this->marca = $marca;
        
        return $this;
    }
    
    /** 
     * Get marca
     *
     * @return String
     */
    public function getMarca()
    {
        return $this->marca;
        //return "00:00:01";
    }
    
    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        if ($this->marca != '' && $this->getEquipo())
            return 100;
        elseif ($this->marca != '' || $this->getEquipo())
            return 50;
        return 0;
    }    
}
