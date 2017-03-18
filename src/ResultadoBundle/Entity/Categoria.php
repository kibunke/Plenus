<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\Categoria
 * @ORM\Table(name="Categoria")
 * @ORM\Entity(repositoryClass="ResultadoBundle\Entity\Repository\CategoriaRepository")
 */
class Categoria
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
     * @Assert\NotNull()
     * @ORM\Column(name="nombre", type="string", length=100)
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
     * @ORM\Column(name="soloInscribe", type="boolean", nullable=true)
     */
    private $soloInscribe;
    
    /**
     * @ORM\OneToMany(targetEntity="InscripcionBundle\Entity\Segmento", mappedBy="categoria")
     */
    private $segmentos;
    
    /**
     * @ORM\OneToMany(targetEntity="Evento", mappedBy="categoria")
     */
    private $eventos;
    
    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;
    
    /**
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
     * @ORM\ManyToOne(targetEntity="SeguridadBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="updatedBy", referencedColumnName="id")
     */   
    private $updatedBy;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->eventos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt = new \DateTime();
    }
    
    /**
     * __toString
     */    
    public function __toString()
    {
        return $this->getNombre();
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
     * @return Categoria
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
        return ucfirst($this->nombre);
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombreSinDetalle()
    {
        return preg_replace('#\(([\w\s]+)\)#i','',$this->getNombre());
    }
    
    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Categoria
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Categoria
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
     * @return Categoria
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
     * Add eventos
     *
     * @param \ResultadoBundle\Entity\Evento $eventos
     * @return Categoria
     */
    public function addEvento(\ResultadoBundle\Entity\Evento $eventos)
    {
        $this->eventos[] = $eventos;

        return $this;
    }

    /**
     * Remove eventos
     *
     * @param \ResultadoBundle\Entity\Evento $eventos
     */
    public function removeEvento(\ResultadoBundle\Entity\Evento $eventos)
    {
        $this->eventos->removeElement($eventos);
    }

    /**
     * Get eventos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEventos()
    {
        return $this->eventos;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Categoria
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
     * @return Categoria
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
     * Set soloInscribe
     *
     * @param boolean $soloInscribe
     * @return Evento
     */
    public function setSoloInscribe($soloInscribe)
    {
        $this->soloInscribe = $soloInscribe;

        return $this;
    }

    /**
     * Get soloInscribe
     *
     * @return boolean
     */
    public function getSoloInscribe()
    {
        return $this->soloInscribe;
    }    

    /**
     * Add segmento
     *
     * @param \InscripcionBundle\Entity\Segmento $segmento
     *
     * @return Categoria
     */
    public function addSegmento(\InscripcionBundle\Entity\Segmento $segmento)
    {
        $this->segmentos[] = $segmento;

        return $this;
    }

    /**
     * Remove segmento
     *
     * @param \InscripcionBundle\Entity\Segmento $segmento
     */
    public function removeSegmento(\InscripcionBundle\Entity\Segmento $segmento)
    {
        $this->segmentos->removeElement($segmento);
    }

    /**
     * Get segmentos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSegmentos()
    {
        return $this->segmentos;
    }
}
