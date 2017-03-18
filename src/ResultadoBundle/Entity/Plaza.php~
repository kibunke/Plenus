<?php
namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\Plaza
 *
 * @ORM\Entity(repositoryClass="ResultadoBundle\Entity\Repository\PlazaRepository")
 * @ORM\Table(name="Plaza")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *                          "plaza"         = "Plaza",
 *                          "plazaPartido"  = "PlazaPartido", 
 *                          "plazaCopa"     = "PlazaCopa",
 *                          "plazaZona"     = "PlazaZona",
 *                          "plazaSerie"    = "PlazaSerie",
 *                          "plazaOrden"    = "PlazaOrden",
 *                          "plazaMedallero"= "PlazaMedallero"
 *                      })
 */ 
class  Plaza
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
    * @var integer $orden
    *
    * @ORM\Column(name="orden", type="integer")
    */
   protected $orden;
   
    /**
    * @var integer $ordenNatural
    *
    * @ORM\Column(name="ordenNatural", type="integer", nullable=true)
    */
   private $ordenNatural;
   
    /**
    * @var string $descripcion
    *
    * @ORM\Column(name="descripcion", type="text", nullable=true)
    */
   private $descripcion;
   
    /**
    * @ORM\ManyToOne(targetEntity="Equipo", inversedBy="plazas")
    * @ORM\JoinColumn(name="equipo", referencedColumnName="id")
    */       
    private $equipo;
   
    /**
     * @ORM\ManyToOne(targetEntity="Competencia", inversedBy="plazas")
     * @ORM\JoinColumn(name="competencia", referencedColumnName="id" )
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
    public function __construct($user = NULL,$nombre = "",$orden = 99,$competencia = null) {
        $this->createdAt = new \DateTime();
        $this->createdBy = $user;
        $this->setNombre($nombre);
        $this->setOrden($orden);
        $this->setOrdenNatural(99);
        $this->setCompetencia($competencia);
    }

    /**
     * __toString
     *
     * @return string 
     */
    public function __toString()
    {
        return $this->nombre;
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
     * @return Plaza
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
     * Get nombre
     *
     * @return string 
     */
    public function getNombreCompleto()
    {
        if ($this->getEquipo())
            return $this->getEquipo()->getMunicipio()->getNombre()."(".$this->nombre.")";
        return $this->nombre;
    }    

    /**
     * Get nombreMedalleroRaw
     *
     * @return string 
     */
    public function getNombreMedalleroRaw()
    {
        if ($this->getEquipo())
            return "<div>
                        <small>".$this->nombre."</small><br>
                        <strong>".$this->getEquipo()->getNombre()."</strong>
                    </div>";
        return "<div>
                    <small>".$this->nombre."</small><br>
                    <strong class='text-danger'>Esta medalla no está asignada aún</strong>
                </div>";
    }

    /**
     * Get nombreCompetenciaRaw
     *
     * @return string 
     */
    public function getNombreCompetenciaRaw()
    {
        $equipo = $this->getEquipo();
        if ($equipo){
            $desc = $this->getDescripcion();
            if ($desc){
                $desc = " (".$desc.")"; 
            }
            $nom = $equipo->getNombre();
            if (count($equipo->getParticipantes())==1)
                $nom = $equipo->getParticipantes()[0]->getNombreCompleto();
            if (count($equipo->getParticipantes())==2)
                $nom = $equipo->getParticipantes()[0]->getNombreCompleto()." y ".$equipo->getParticipantes()[1]->getNombreCompleto();
            return "<div>
                        <strong>".$equipo->getMunicipio()->getNombre()."</strong><small>".$desc."</small><br>
                        <small>".$nom."</small>
                    </div>";
        }
        return "<div>
                    <small>".$this->nombre."</small><br>
                    <strong class='text-danger'>Sin asignar</strong>
                </div>";
    }
    
    /**
     * Get getNombreCompetenciaMinRaw
     *
     * @return string 
     */
    public function getNombreCompetenciaMinRaw()
    {
        $equipo = $this->getEquipo();
        if ($equipo){
            $nom = $equipo->getNombre();
            return "<div><strong>".$equipo->getMunicipio()->getNombre()."</strong></div>";
        }
        return "";
    }    
    
    /**
     * Set orden
     *
     * @param integer $orden
     * @return Plaza
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return integer 
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set ordenNatural
     *
     * @param integer $ordenNatural
     * @return Plaza
     */
    public function setOrdenNatural($ordenNatural)
    {
        $this->ordenNatural = $ordenNatural;

        return $this;
    }

    /**
     * Get ordenNatural
     *
     * @return integer 
     */
    public function getOrdenNatural()
    {
        return $this->ordenNatural;
    }
    
    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Plaza
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
     * Set equipo
     *
     * @param \ResultadoBundle\Entity\Equipo $equipo
     * @return Plaza
     */
    public function setEquipo(\ResultadoBundle\Entity\Equipo $equipo = null)
    {
        $this->equipo = $equipo;

        return $this;
    }

    /**
     * Get equipo
     *
     * @return \ResultadoBundle\Entity\Equipo 
     */
    public function getEquipo()
    {
        return $this->equipo;
    }
    
    /**
     * Set competencia
     *
     * @param \ResultadoBundle\Entity\Competencia $competencia
     * @return Plaza
     */
    public function setCompetencia(\ResultadoBundle\Entity\Competencia $competencia = null)
    {
        $this->competencia = $competencia;

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
     * Get medallaRaw
     *
     * @return html
     */
    public function getMedalla()
    {
        return $this->getCompetencia()->getMedalla($this);
    }
    
    /**
     * Get idReload
     *
     * @return integer
     */
    public function getIdReload()
    {
        return $this->getCompetencia()->getIdReload($this);
    }
    
    /**
     * Get evento
     *
     * @return \ResultadoBundle\Entity\Evento
     */
    public function getEvento()
    {
        return $this->getCompetencia()->getEtapa()->getEvento();
    }
    
    /**
     * Get medallaRaw
     *
     * @return html
     */
    public function hasMedalla()
    {
        if ($this->getCompetencia()->getMedalla($this)){
            return true;
        }
        return false;
    }    
}
