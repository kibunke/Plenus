<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\Equipo
 * @ORM\Table(name="Equipo")
 * @ORM\Entity()
 */
class Equipo
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=true)
     */
    protected $nombre;
    
     /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="sortea", type="boolean", nullable=true)
     */
    private $sortea;
    
    /**
     * @ORM\OneToMany(targetEntity="Plaza", mappedBy="equipo")
     */
    private $plazas;
    
    /**
     * @ORM\ManyToMany(targetEntity="Participante", mappedBy="equipos", cascade={"persist"})
     */
    private $participantes;
   
    /**
     * @ORM\ManyToOne(targetEntity="InscripcionBundle\Entity\Planilla", inversedBy="competidores", cascade={"persist"})
     * @ORM\JoinColumn(name="planilla", referencedColumnName="id")
     */       
    private $planilla;
    
    /**
     * @ORM\ManyToOne(targetEntity="Evento", inversedBy="equipos")
     * @ORM\JoinColumn(name="evento", referencedColumnName="id")
     */       
    private $evento;

    /**
     * @ORM\ManyToOne(targetEntity="\CommonBundle\Entity\Municipio")
     * @ORM\JoinColumn(name="municipio", referencedColumnName="id")
     */       
    private $municipio;
    
    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var SeguridadBundle\Entity\Usuario $createdBy
     * 
     * @ORM\ManyToOne(targetEntity="SeguridadBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="createdBy", referencedColumnName="id")
     */
    private $createdBy;
    
    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    private $updatedAt;
    
    /**
     * @var SeguridadBundle\Entity\Usuario $updatedBy
     * 
     * @ORM\ManyToOne(targetEntity="SeguridadBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="updatedBy", referencedColumnName="id")
     */   
    private $updatedBy;
    
    /*
     * Construct
     */
    public function __construct($user = NULL,$evento = NULL)
    {
        $this->createdAt = new \DateTime();
        $this->createdBy = $user;
        $this->setEvento($evento);
        $this->setNombre("Equipo ".(count($evento->getEquipos()) + 1));
        $this->plazas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->participantes = new \Doctrine\Common\Collections\ArrayCollection();        
    }    

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {

        if ($this->nombre != '')
            return $this->getMunicipio()->getRegionDeportiva()." - ".$this->getMunicipio()->getNombre()." - ".$this->nombre;
        else
            return $this->getMunicipio()->getRegionDeportiva()." - ".$this->getMunicipio()->getNombre();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Equipo
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }
    
    /**
     * Get nombreCompletoRaw
     *
     * @return string 
     */
    public function getNombreCompletoRaw()
    {
        return "<strong>".$this->getMunicipio()."</strong><br><small>".$this->nombre."</small>";
    }    

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Equipo
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set sortea
     *
     * @param boolean $sortea
     * @return Equipo
     */
    public function setSortea($sortea)
    {
        $this->sortea = $sortea;

        return $this;
    }

    /**
     * Get sortea
     *
     * @return boolean 
     */
    public function getSortea()
    {
        return $this->sortea;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Equipo
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Equipo
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }



    /**
     * Add plaza
     *
     * @param \ResultadoBundle\Entity\Plaza $plaza
     * @return Equipo
     */
    public function addPlaza(\ResultadoBundle\Entity\Plaza $plaza)
    {
        $this->plazas[] = $plaza;
        $plaza->setEquipo($this);

        return $this;
    }

    /**
     * Remove plaza
     *
     * @param \ResultadoBundle\Entity\Plaza $plaza
     */
    public function removePlaza(\ResultadoBundle\Entity\Plaza $plaza)
    {
        $this->plazas->removeElement($plaza);
    }

    /**
     * Get plazas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlazas()
    {
        return $this->plazas;
    }

    /**
     * Set evento
     *
     * @param \ResultadoBundle\Entity\Evento $evento
     * @return Equipo
     */
    public function setEvento(\ResultadoBundle\Entity\Evento $evento = null)
    {
        $this->evento = $evento;

        return $this;
    }

    /**
     * Get evento
     *
     * @return \ResultadoBundle\Entity\Evento 
     */
    public function getEvento()
    {
        return $this->evento;
    }

    /**
     * Set municipio
     *
     * @param \CommonBundle\Entity\Municipio $municipio
     * @return Equipo
     */
    public function setMunicipio(\CommonBundle\Entity\Municipio $municipio = null)
    {
        $this->municipio = $municipio;

        return $this;
    }

    /**
     * Get municipio
     *
     * @return \CommonBundle\Entity\Municipio 
     */
    public function getMunicipio()
    {
        return $this->municipio;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Equipo
     */
    public function setCreatedBy(\SeguridadBundle\Entity\Usuario $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \SeguridadBundle\Entity\Usuario 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param \SeguridadBundle\Entity\Usuario $updatedBy
     * @return Equipo
     */
    public function setUpdatedBy(\SeguridadBundle\Entity\Usuario $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \SeguridadBundle\Entity\Usuario 
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * hasPlazas
     *
     * @return boolean
     */
    public function hasPlazas()
    {
        return (count($this->getPlazas())>0);
    }    
    
    /**
     * Add participante
     *
     * @param \ResultadoBundle\Entity\Participante $participante
     * @return Equipo
     */
    public function addParticipante(\ResultadoBundle\Entity\Participante $participante)
    {
        $this->participantes[] = $participante;
        $participante->addEquipo($this);

        return $this;
    }

    /**
     * Remove participante
     *
     * @param \ResultadoBundle\Entity\Participante $participante
     */
    public function removeParticipante(\ResultadoBundle\Entity\Participante $participante)
    {
        $this->participantes->removeElement($participante);
    }

    /**
     * Get participantes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParticipantes()
    {
        return $this->participantes;
    }
    
    /**
     * Get mejorResultado
     *
     * @return string
     */
    public function getMejorResultado()
    {
        $mejor = null;
        foreach ($this->getPlazas() as $plaza){
            if (!$mejor){
                $mejor = $plaza;
            }else{
                if ($mejor->getCompetencia()->getEtapa()->getTipoValor() < $plaza->getCompetencia()->getEtapa()->getTipoValor()){
                    $mejor = $plaza;
                }
            }
        }
        if ($mejor){
            return $mejor->getCompetencia()->getPerformance($mejor);
        }else{
            return "No participó.";
        }
    }
    
    /**
     * Get hasMedalla
     *
     * @return array
     */
    public function hasMedalla()
    {
        foreach ($this->getPlazas() as $plaza){
            if ($plaza->hasMedalla()) {
                return true;
            }
        }
        return false;
    }    
}
