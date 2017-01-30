<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InscripcionBundle\Entity\Segmento
 * @ORM\Table(name="Segmento")
 * @ORM\Entity(repositoryClass="InscripcionBundle\Entity\Repository\SegmentoRepository")
 */
class Segmento
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
     * @ORM\OneToMany(targetEntity="ResultadoBundle\Entity\Evento", mappedBy="segmento")
     */
    private $eventos;
    
    /**
     * @ORM\OneToMany(targetEntity="Planilla", mappedBy="segmento")
     */
    private $planillas;
    
    /**
     * @var integer $maxIntegrantes
     *
     * @ORM\Column(name="maxIntegrantes", type="integer", nullable=true)
     */
    protected $maxIntegrantes;

    /**
     * @var integer $minIntegrantes
     *
     * @ORM\Column(name="minIntegrantes", type="integer", nullable=true)
     */
    protected $minIntegrantes;
    
    /**
     * @var integer $maxReemplazos
     *
     * @ORM\Column(name="maxReemplazos", type="integer", nullable=true)
     */
    protected $maxReemplazos;
    
    /**
     * @var datetime $minFechaNacimiento
     *
     * @ORM\Column(name="minFechaNacimiento", type="datetime")
     */
    private $minFechaNacimiento;
    
    /**
     * @var datetime $maxFechaNacimiento
     *
     * @ORM\Column(name="maxFechaNacimiento", type="datetime")
     */
    private $maxFechaNacimiento;
    
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
     *
     * @return Segmento
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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Segmento
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
     * Add evento
     *
     * @param \ResultadoBundle\Entity\Evento $evento
     *
     * @return Segmento
     */
    public function addEvento(\ResultadoBundle\Entity\Evento $evento)
    {
        $this->eventos[] = $evento;

        return $this;
    }

    /**
     * Remove evento
     *
     * @param \ResultadoBundle\Entity\Evento $evento
     */
    public function removeEvento(\ResultadoBundle\Entity\Evento $evento)
    {
        $this->eventos->removeElement($evento);
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
     * Set maxIntegrantes
     *
     * @param integer $maxIntegrantes
     *
     * @return Segmento
     */
    public function setMaxIntegrantes($maxIntegrantes)
    {
        $this->maxIntegrantes = $maxIntegrantes;

        return $this;
    }

    /**
     * Get maxIntegrantes
     *
     * @return integer
     */
    public function getMaxIntegrantes()
    {
        return $this->maxIntegrantes;
    }

    /**
     * Set minIntegrantes
     *
     * @param integer $minIntegrantes
     *
     * @return Segmento
     */
    public function setMinIntegrantes($minIntegrantes)
    {
        $this->minIntegrantes = $minIntegrantes;

        return $this;
    }

    /**
     * Get minIntegrantes
     *
     * @return integer
     */
    public function getMinIntegrantes()
    {
        return $this->minIntegrantes;
    }

    /**
     * Set maxReemplazos
     *
     * @param integer $maxReemplazos
     *
     * @return Segmento
     */
    public function setMaxReemplazos($maxReemplazos)
    {
        $this->maxReemplazos = $maxReemplazos;

        return $this;
    }

    /**
     * Get maxReemplazos
     *
     * @return integer
     */
    public function getMaxReemplazos()
    {
        return $this->maxReemplazos;
    }

    /**
     * Set minFechaNacimiento
     *
     * @param \DateTime $minFechaNacimiento
     *
     * @return Segmento
     */
    public function setMinFechaNacimiento($minFechaNacimiento)
    {
        $this->minFechaNacimiento = $minFechaNacimiento;

        return $this;
    }

    /**
     * Get minFechaNacimiento
     *
     * @return \DateTime
     */
    public function getMinFechaNacimiento()
    {
        return $this->minFechaNacimiento;
    }

    /**
     * Set maxFechaNacimiento
     *
     * @param \DateTime $maxFechaNacimiento
     *
     * @return Segmento
     */
    public function setMaxFechaNacimiento($maxFechaNacimiento)
    {
        $this->maxFechaNacimiento = $maxFechaNacimiento;

        return $this;
    }

    /**
     * Get maxFechaNacimiento
     *
     * @return \DateTime
     */
    public function getMaxFechaNacimiento()
    {
        return $this->maxFechaNacimiento;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Segmento
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
     *
     * @return Segmento
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
     * Add planilla
     *
     * @param \InscripcionBundle\Entity\Planilla $planilla
     *
     * @return Segmento
     */
    public function addPlanilla(\InscripcionBundle\Entity\Planilla $planilla)
    {
        $this->planillas[] = $planilla;

        return $this;
    }

    /**
     * Remove planilla
     *
     * @param \InscripcionBundle\Entity\Planilla $planilla
     */
    public function removePlanilla(\InscripcionBundle\Entity\Planilla $planilla)
    {
        $this->planillas->removeElement($planilla);
    }

    /**
     * Get planillas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlanillas()
    {
        return $this->planillas;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     *
     * @return Segmento
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
     *
     * @return Segmento
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
}
