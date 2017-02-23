<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\Equipos
 * @ORM\Table(name="Equipo")
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *                          "Equipo"       = "Equipo",
 *                          "Individual"   = "Individual"
 *                      })
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
     * @ORM\ManyToMany(targetEntity="Competidor", mappedBy="equipos", cascade={"persist"})
     */
    private $competidores;
    
    /**
     * @ORM\ManyToOne(targetEntity="DirectorTecnico", inversedBy="equipos", cascade={"persist"})
     * @ORM\JoinColumn(name="tecnico", referencedColumnName="id")
     */       
    private $directorTecnico;
    
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
     * @ORM\ManyToOne(targetEntity="InscripcionBundle\Entity\Planilla", inversedBy="equipos", cascade={"persist"})
     * @ORM\JoinColumn(name="planilla", referencedColumnName="id")
     */       
    private $planilla;
    
    /**
     * @ORM\ManyToOne(targetEntity="Evento", inversedBy="equipos")
     * @ORM\JoinColumn(name="evento", referencedColumnName="id")
     */       
    private $evento;
    
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
    public function __construct($user = NULL)
    {
        $this->createdAt = new \DateTime();
        $this->createdBy = $user;
        //$this->setEvento($evento);
        //$this->setNombre("Equipo ".(count($evento->getEquipos()) + 1));
        $this->competidores = new \Doctrine\Common\Collections\ArrayCollection();        
        $this->plazas = new \Doctrine\Common\Collections\ArrayCollection();
    }    

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return "lal";
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
     * Add Competidor
     *
     * @param \ResultadoBundle\Entity\Competidor $competidor
     * @return Equipo
     */
    public function addCompetidor(\ResultadoBundle\Entity\Competidor $competidor)
    {
        $this->competidores[] = $competidor;
        $competidor->addEquipo($this);

        return $this;
    }

    /**
     * Add Integrante
     *
     * @param \ResultadoBundle\Entity\Competidor $integrante
     * @return Equipo
     */
    public function addIntegrante($integrante)
    {
        return $this->addCompetidor($integrante);
    }
    
    /**
     * get Integrante
     *
     * @param \ResultadoBundle\Entity\Competidor $integrante
     * @return Equipo
     */
    public function getIntegrantes()
    {
        return $this->getCompetidores() ;
    }
    
    /**
     * Remove Competidor
     *
     * @param \ResultadoBundle\Entity\Competidor $competidor
     */
    public function removeCompetidor(\ResultadoBundle\Entity\Competidor $competidor)
    {
        $this->competidores->removeElement($competidor);
    }

    /**
     * Get competidores
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCompetidores()
    {
        return $this->competidores;
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

    /**
     * Add competidore
     *
     * @param \ResultadoBundle\Entity\Competidor $competidore
     *
     * @return Equipo
     */
    public function addCompetidore(\ResultadoBundle\Entity\Competidor $competidore)
    {
        $this->competidores[] = $competidore;

        return $this;
    }

    /**
     * Remove competidore
     *
     * @param \ResultadoBundle\Entity\Competidor $competidore
     */
    public function removeCompetidore(\ResultadoBundle\Entity\Competidor $competidore)
    {
        $this->competidores->removeElement($competidore);
    }

    /**
     * Set directorTecnico
     *
     * @param \ResultadoBundle\Entity\DirectorTecnico $directorTecnico
     *
     * @return Equipo
     */
    public function setDirectorTecnico(\ResultadoBundle\Entity\DirectorTecnico $directorTecnico = null)
    {
        $this->directorTecnico = $directorTecnico;

        return $this;
    }

    /**
     * Get directorTecnico
     *
     * @return \ResultadoBundle\Entity\DirectorTecnico
     */
    public function getDirectorTecnico()
    {
        return $this->directorTecnico;
    }

    /**
     * Set planilla
     *
     * @param \InscripcionBundle\Entity\Planilla $planilla
     *
     * @return Equipo
     */
    public function setPlanilla(\InscripcionBundle\Entity\Planilla $planilla = null)
    {
        $this->planilla = $planilla;

        return $this;
    }

    /**
     * Get planilla
     *
     * @return \InscripcionBundle\Entity\Planilla
     */
    public function getPlanilla()
    {
        return $this->planilla;
    }
    
    public function getJson()
    {
        return array(
                "id" => $this->getId(),
                "nombre" => $this->getNombre(),
                "integrantes" => $this->getIntegrantesJson(),
                "tecnico"=> $this->getDirectorTecnico() ? $this->getDirectorTecnico()->getJson() : []
        );
    }
    public function getIntegrantesJson()
    {
        $integrantes = [];
        foreach($this->getIntegrantes() as $integrante){
            $integrantes[] = $integrante->getJson();
        }
        return $integrantes;
    }    
}
