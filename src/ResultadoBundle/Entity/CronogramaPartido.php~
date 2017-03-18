<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\Cronograma
 *
 * @ORM\Entity()
 */
class CronogramaPartido extends Cronograma
{
    /**
     * @ORM\OneToOne(targetEntity="Partido", inversedBy="cronograma")
     * @ORM\JoinColumn(name="partido", referencedColumnName="id")
     **/
    private $partido;
    
    /**
     * Constructor
     */
    public function __construct($user = null)
    {
        parent::__construct($user);
    }
    
    /**
     * Set partido
     *
     * @param \ResultadoBundle\Entity\Partido $partido
     * @return CronogramaPartido
     */
    public function setPartido(\ResultadoBundle\Entity\Partido $partido = null)
    {
        $this->partido = $partido;

        return $this;
    }

    /**
     * Get partido
     *
     * @return \ResultadoBundle\Entity\Partido 
     */
    public function getPartido()
    {
        return $this->partido;
    }

    /**
     * Get evento
     *
     * @return \ResultadoBundle\Entity\Evento 
     */
    public function getEvento()
    {
        return $this->getPartido()->getEvento();
    }
    
    /**
     * Get idReload
     *
     * @return integer
     */
    public function getIdReload()
    {
        return $this->getPartido()->getPlaza1()->getIdReload();
    }
    
    /**
     * Can Delete
     *
     * @return boolean
     */
    public function canDelete()
    {
        return false;
    }    
}
