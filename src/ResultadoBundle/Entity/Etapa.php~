<?php
namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResultadoBundle\Entity\Etapa
 *
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *                          "etapa"             = "Etapa",
 *                          "etapaClasificacion"= "EtapaClasificacion", 
 *                          "etapaFinal"        = "EtapaFinal",
 *                          "etapaMedallero"        = "EtapaMedallero"
 *                      })
 */
abstract class  Etapa
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
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;
    
     /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;
    
    /**
     * @ORM\ManyToOne(targetEntity="Evento", inversedBy="etapas")
     * @ORM\JoinColumn(name="evento", referencedColumnName="id")
     */       
    private $evento;
    
    /**
     * @ORM\OneToOne(targetEntity="Competencia", mappedBy="etapa", cascade={"all"})
     */
    private $competencia;    
    
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
    
    /**
     * Construct
     */
    public function __construct($user = NULL) {
       $this->createdAt = new \DateTime();
       $this->createdBy = $user;
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
     * @return Etapa
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
     * @return Etapa
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
     * @return Etapa
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
     * @return Etapa
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
     * Set evento
     *
     * @param \ResultadoBundle\Entity\Evento $evento
     * @return Etapa
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
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Etapa
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
     * @return Etapa
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
     * Set competencia
     *
     * @param \ResultadoBundle\Entity\Competencia $competencia
     * @return Competencia
     */
    public function setCompetencia(\ResultadoBundle\Entity\Competencia $competencia = null)
    {
        $this->competencia = $competencia;
        $competencia->setEtapa($this);

        return $this;
    }

    /**
     * Get competencia
     *
     * @return \ResultadoBundle\Entity\Competencia 
     */
    public function getCompetencia()
    {
        return $this->competencia;
    }
    
    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        $percent = 0;
        if ($this->getCompetencia())
            $percent = $this->getCompetencia()->getState();
        //return $percent;
        return round($percent,1);
        
    }
    
    /**
     * Get state
     *
     * @return string
     */
    public function getStateBadgeRaw()
    {
        $percent = 0;
        if ($this->getCompetencia())
            $percent = $this->getCompetencia()->getState();
        if ($percent<40)// < 40 % completed danger
            return '<span class="badge badge-danger wobble animated">'.$percent.' %</span>';
        if ($percent<70)// between 40 % and 70 % completed warning
            return '<span class="badge badge-warning wobble animated">'.$percent.' %</span>';
        if ($percent<99)// between 70 % and 99 % completed default
            return '<span class="badge badge-info wobble animated">'.$percent.' %</span>';
        // 100 % completed success
        return '<span class="badge badge-success wobble animated">'.$percent.' %</span>';
        //return $percent;
    }
    
    /**
     * Get partidos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartidos()
    {
        if ($this->getCompetencia())
            return $this->getCompetencia()->getPartidos();
        return [];
    }
    
    /**
     * get tipoValor
     * 10 - Medallero
     * 7 - Final
     * 4 - Clasificacion
     * @return integer
     */
    public function getTipoValor()
    {
    }    
}
