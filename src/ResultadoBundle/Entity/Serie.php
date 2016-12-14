<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\Serie
 * @ORM\Table(name="services_juegosba_final.Serie")
 * @ORM\Entity()
 */
class Serie
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
    protected $nombre;
    
     /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;
    
    /**
     * @ORM\ManyToOne(targetEntity="CompetenciaSerie", inversedBy="series")
     * @ORM\JoinColumn(name="serie", referencedColumnName="id")
     */       
    private $competencia;
    
    /**
     * @ORM\OneToMany(targetEntity="PlazaSerie", mappedBy="serie", cascade={"all"})
     * @ORM\OrderBy({"orden" = "ASC", "ordenNatural" = "ASC"})
     */
    private $plazas;
    
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
    public function __construct($user = null, $nombre = "")
    {
        $this->nombre = $nombre;        
        $this->createdAt = new \DateTime();
        $this->createdBy = $user;
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
     * Set Competencia
     *
     * @param \ResultadoBundle\Entity\CompetenciaSerie $competencia
     * @return Serie
     */
    public function setCompetencia(\ResultadoBundle\Entity\CompetenciaSerie $competencia = null)
    {
        $this->competencia = $competencia;

        return $this;
    }

    /**
     * Get competencia
     *
     * @return \ResultadoBundle\Entity\CompetenciaSerie
     */
    public function getCompetencia()
    {
        return $this->competencia;
    }
    
    /**
     * Add plaza
     *
     * @param \ResultadoBundle\Entity\PlazaSerie $plaza
     * @return Competencia
     */
    public function addPlazas(\ResultadoBundle\Entity\PlazaSerie $plaza)
    {
        $this->plazas[] = $plaza;
        $plaza->setSerie($this);

        return $this;
    }

    /**
     * Remove plaza
     *
     * @param \ResultadoBundle\Entity\PlazaSerie $plaza
     */
    public function removePlazas(\ResultadoBundle\Entity\PlazaSerie $plaza)
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
     * Get columnsRaw
     *
     * @return html
     */
    public function getColumnRaw()
    {
        return "<th>".$this->getCompetencia()->getEtiqueta()."</th>";
    }

    /**
     * Get DetalleSerieRaw
     *
     * @return string 
     */
    public function getDetalleSerieRaw($plaza,$plazaDeReferencia)
    {
        return "<td>".$this->getCompetencia()->aplicarMascara($plaza,$plazaDeReferencia)."</td>";
    }
    
    /**
     * recalcularOrdenNatural
     *
     * @return array
     */
    public function recalcularOrdenNatural()
    {
        return $this->getCompetencia()->recalcularOrdenNatural($this);
    }
    
    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        $percent = 0;
        return round($percent,1);
    }
}
