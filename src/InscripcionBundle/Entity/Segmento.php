<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InscripcionBundle\Entity\Segmento
 * @ORM\Table(name="Segmento",indexes={
 *                                      @ORM\Index(name="search_nombre", columns={"nombre"})}
 *)
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
     * @Assert\NotNull()
     * @Assert\Range(
     *      max = 24
     * )
     * @ORM\Column(name="maxIntegrantes", type="integer")
     */
    protected $maxIntegrantes;

    /**
     * @var integer $minIntegrantes
     * @Assert\NotNull()
     * @Assert\Range(
     *      min = 0
     * )
     * @ORM\Column(name="minIntegrantes", type="integer")
     */
    protected $minIntegrantes;

    /**
     * @var integer $maxReemplazos
     * @Assert\NotNull()
     * @Assert\Range(
     *      min = 0,
     *      max = 5
     * )
     * @ORM\Column(name="maxReemplazos", type="integer")
     */
    protected $maxReemplazos;

    /**
     * @var integer $maxEquiposPorPlanilla
     * @Assert\NotNull()
     * @Assert\Range(
     *      min = 1,
     *      max = 12
     * )
     * @ORM\Column(name="maxEquiposPorPlanilla", type="integer", options={"default" : 1})
     */
    protected $maxEquiposPorPlanilla;

    /**
     * @var datetime $minFechaNacimiento
     * @Assert\NotNull()
     * @Assert\Date()
     * @ORM\Column(name="minFechaNacimiento", type="datetime")
     */
    private $minFechaNacimiento;

    /**
     * @var datetime $maxFechaNacimiento
     * @Assert\NotNull()
     * @Assert\Date()
     * @ORM\Column(name="maxFechaNacimiento", type="datetime")
     */
    private $maxFechaNacimiento;

    /**
     * @ORM\ManyToOne(targetEntity="ResultadoBundle\Entity\Torneo", inversedBy="segmentos")
     * @ORM\JoinColumn(name="torneo", referencedColumnName="id")
     */
    private $torneo;

    /**
     * @ORM\ManyToOne(targetEntity="ResultadoBundle\Entity\Categoria", inversedBy="segmentos")
     * @ORM\JoinColumn(name="categoria", referencedColumnName="id")
     */
    private $categoria;

    /**
     * @ORM\ManyToOne(targetEntity="ResultadoBundle\Entity\Modalidad", inversedBy="segmentos")
     * @ORM\JoinColumn(name="modalidad", referencedColumnName="id")
     */
    private $modalidad;

    /**
     * @ORM\ManyToOne(targetEntity="ResultadoBundle\Entity\Genero", inversedBy="segmentos")
     * @ORM\JoinColumn(name="genero", referencedColumnName="id")
     */
    private $genero;

    /**
     * @ORM\ManyToOne(targetEntity="ResultadoBundle\Entity\Disciplina", inversedBy="segmentos")
     * @ORM\JoinColumn(name="disciplina", referencedColumnName="id")
     */
    private $disciplina;

    /**
     * @ORM\ManyToMany(targetEntity="SeguridadBundle\Entity\Usuario", inversedBy="coordina", cascade={"persist"})
     * @ORM\JoinTable(name="usuario_coordina_segmento")
     **/
    private $coordinadores;

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
     * @var boolean $isActive
     *
     * @ORM\Column(name="isActive", type="boolean", options={"default" : false})
     */
    protected $isActive;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->eventos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->coordinadores = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isActive = false;
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
     * Get nombreCompleto
     *
     * @return string
     */
    public function getNombreCompleto()
    {
        $name = "";
        if ($this->getTorneo())
            $name .= $this->getTorneo()->getNombre();
        if ($this->getDisciplina())
            $name .= " - ".$this->getDisciplina()->getNombreCompleto();
        if ($this->getCategoria())
            $name .= " - ".$this->getCategoria()->getNombre();
        if ($this->getGenero())
            $name .= " - ".$this->getGenero()->getNombre();
        if ($this->getModalidad())
            $name .= " - ".$this->getModalidad()->getNombre();
        if ($this->getNombre())
            $name = $name." - ".$this->getNombre();
        return $name;
    }

    /**
     * Get nombreCompletoRaw
     *
     * @return string
     */
    public function getNombreCompletoRaw()
    {
        $nomAux = '';
        if ($this->nombre)
            $nomAux = " - <small>".$this->getNombre()."</small>";
        $nom =  "<div title='".$this->getNombreCompleto()."'>".
                    "<strong>".$this->getDisciplina()->getNombreCompleto()."</strong><br>".
                    "<small>".$this->getCategoria()->getNombre()." - ".$this->getGenero()->getNombre()."</small><br>".
                    "<strong><small>".$this->getModalidad()->getNombre()."</small></strong>".$nomAux.
                "</div>";

        return $nom;
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
        $evento->setSegmento($this);
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
     * Set maxEquiposPorPlanilla
     *
     * @param integer $maxEquiposPorPlanilla
     *
     * @return Segmento
     */
    public function setMaxEquiposPorPlanilla($maxEquiposPorPlanilla)
    {
        $this->maxEquiposPorPlanilla = $maxEquiposPorPlanilla;

        return $this;
    }

    /**
     * Get maxEquiposPorPlanilla
     *
     * @return integer
     */
    public function getMaxEquiposPorPlanilla()
    {
        return $this->maxEquiposPorPlanilla;
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

    /**
     * Set torneo
     *
     * @param \ResultadoBundle\Entity\Torneo $torneo
     *
     * @return Segmento
     */
    public function setTorneo(\ResultadoBundle\Entity\Torneo $torneo = null)
    {
        $this->torneo = $torneo;

        return $this;
    }

    /**
     * Get torneo
     *
     * @return \ResultadoBundle\Entity\Torneo
     */
    public function getTorneo()
    {
        return $this->torneo;
    }

    /**
     * Set categoria
     *
     * @param \ResultadoBundle\Entity\Categoria $categoria
     *
     * @return Segmento
     */
    public function setCategoria(\ResultadoBundle\Entity\Categoria $categoria = null)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return \ResultadoBundle\Entity\Categoria
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Set modalidad
     *
     * @param \ResultadoBundle\Entity\Modalidad $modalidad
     *
     * @return Segmento
     */
    public function setModalidad(\ResultadoBundle\Entity\Modalidad $modalidad = null)
    {
        $this->modalidad = $modalidad;

        return $this;
    }

    /**
     * Get modalidad
     *
     * @return \ResultadoBundle\Entity\Modalidad
     */
    public function getModalidad()
    {
        return $this->modalidad;
    }

    /**
     * Set genero
     *
     * @param \ResultadoBundle\Entity\Genero $genero
     *
     * @return Segmento
     */
    public function setGenero(\ResultadoBundle\Entity\Genero $genero = null)
    {
        $this->genero = $genero;

        return $this;
    }

    /**
     * Get genero
     *
     * @return \ResultadoBundle\Entity\Genero
     */
    public function getGenero()
    {
        return $this->genero;
    }

    /**
     * Set disciplina
     *
     * @param \ResultadoBundle\Entity\Disciplina $disciplina
     *
     * @return Segmento
     */
    public function setDisciplina(\ResultadoBundle\Entity\Disciplina $disciplina = null)
    {
        $this->disciplina = $disciplina;

        return $this;
    }

    /**
     * Get disciplina
     *
     * @return \ResultadoBundle\Entity\Disciplina
     */
    public function getDisciplina()
    {
        return $this->disciplina;
    }

    /**
     * Add coordinadores
     *
     * @param \SeguridadBundle\Entity\Usuario $coordinadores
     * @return Evento
     */
    public function addCoordinadore(\SeguridadBundle\Entity\Usuario $coordinadores)
    {
        $this->coordinadores[] = $coordinadores;

        return $this;
    }

    /**
     * Remove coordinadores
     *
     * @param \SeguridadBundle\Entity\Usuario $coordinadores
     */
    public function removeCoordinadore(\SeguridadBundle\Entity\Usuario $coordinadores)
    {
        $this->coordinadores->removeElement($coordinadores);
    }

    /**
     * Get coordinadores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCoordinadores()
    {
        return $this->coordinadores;
    }
/**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Usuario
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * stateToggle
     *
     * @return Segmento
     */
    public function stateToggle()
    {
        $this->isActive = !$this->isActive;

        return $this;
    }

    /**
     * esCoordinador
     *
     * @return Boolean
     */
    public function esCoordinador($user)
    {
        foreach ($this->coordinadores as $coodinador){
            if ($coodinador->getId() == $user->getId()){
                return true;
            }
        }
        return false;
    }

    /**
     * getTotalInscriptosFromQuery
     *
     * @return Boolean
     */
    public function getTotalInscriptosFromQuery($arr)
    {
        $result = array(
            "aprobadas" => 0,
            "total" => 0
        );
        //echo "#####";
        foreach ($arr as $item){
            //echo $item['nombre']."/".$item['cant']."-";
            $result['total'] += $item['cant'];
            if ($item['nombre'] == 'Aprobada'){
                $result['aprobadas'] += $item['cant'];
            }
        }
        return $result;
    }
        /**
     * Get equipos
     *
     * @return json
     */
    public function toArray()
    {
        return array(
                        "id" => $this->getId(),
                        "nombre" => $this->getNombreCompleto(),
                        "torneo" => $this->getTorneo()->getNombre(),
                        "categoria" => $this->getCategoria()->getNombre(),
                        "disciplina" => $this->getDisciplina()->getNombreCompleto(),
                        "genero" => $this->getGenero()->getNombre()
                    );
    }

}
