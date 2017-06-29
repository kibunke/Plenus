<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\Torneo
 * @ORM\Table(name="Torneo")
 * @ORM\Entity(repositoryClass="ResultadoBundle\Entity\Repository\TorneoRepository")
 */
class Torneo
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
     * @ORM\OneToMany(targetEntity="Evento", mappedBy="torneo")
     */
    private $eventos;

    /**
     * @ORM\OneToMany(targetEntity="InscripcionBundle\Entity\Segmento", mappedBy="torneo")
     */
    private $segmentos;

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
     * @ORM\Column(name="isAdultosMayores", type="boolean", options={"default" : false})
     */
    protected $isAdultosMayores;

    /**
     * @var boolean $isActive
     *
     * @ORM\Column(name="isCultura", type="boolean", options={"default" : false})
     */
    protected $isCultura;

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
     * @return Torneo
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
     * Get area
     *
     * @return string
     */
    public function getArea()
    {
        $nom = explode("(",$this->nombre)[0];
        return trim($nom);
    }

    /**
     * Get area
     *
     * @return string
     */
    public function getSubArea()
    {
        $nom = explode(")",explode("(",$this->nombre)[1])[0];
        return trim($nom);
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Torneo
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
     * @return Torneo
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
     * @return Torneo
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
     * @return Torneo
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
     * @return Torneo
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
     * @return Torneo
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
     * Set isAdultosMayores
     *
     * @param boolean $isAdultosMayores
     *
     * @return Torneo
     */
    public function setIsAdultosMayores($isAdultosMayores)
    {
        $this->isAdultosMayores = $isAdultosMayores;

        return $this;
    }

    /**
     * Get isAdultosMayores
     *
     * @return boolean
     */
    public function getIsAdultosMayores()
    {
        return $this->isAdultosMayores;
    }

    /**
     * Set isCultura
     *
     * @param boolean $isCultura
     *
     * @return Torneo
     */
    public function setIsCultura($isCultura)
    {
        $this->isCultura = $isCultura;

        return $this;
    }

    /**
     * Get isCultura
     *
     * @return boolean
     */
    public function getIsCultura()
    {
        return $this->isCultura;
    }
    /**
     * Add segmento
     *
     * @param \InscripcionBundle\Entity\Segmento $segmento
     *
     * @return Torneo
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

    public function toArray()
    {
        return array(
                'id' => $this->getId(),
                'nombre' => $this->getNombre(),
                'datos' => array(
                    'planillas' => 0,
                    'equipos' => 0,
                    'inscriptos' => array(
                        'total' => 0,
                        'Femenino' => 0,
                        'Masculino' => 0,
                    )
                )
            );
    }

    /*
     * RESTRICCIONES PARA LA ETAPA MUNICIPAL
     * Juveniles
     * 2 eventos deportivos mas 1 evento cultural, con excepción de los Municipios
     * que tengan menos de 30.000 habitantes Según el Censo Nacional de Población 2010;
     * en cuyo caso podrán avanzar en 3 eventos deportivos y 1 cultural.
     *
     * Adultos Mayores
     * 1 evento deportivo mas 1 evento cultural.
    */
    public function validarGanadorMunicipal($equipo)
    {
        /*
         * IMPORTANTE !!!
         * Chequea todos los titulares del equipo, dejando afuera los reemplazos y sustitutos
         */
        foreach ($equipo->getEquipoCompetidores() as $eqCom) {
            if ($eqCom->getRol() == 'inscripto'){
                $this->validarCompetidorGanadorMunicipal($equipo->getPlanilla(),$eqCom->getCompetidor());
            }
        }
        return true;
    }

    /*
     * RESTRICCIONES PARA LA ETAPA MUNICIPAL
     * Juveniles
     * 2 eventos deportivos mas 1 evento cultural, con excepción de los Municipios
     * que tengan menos de 30.000 habitantes Según el Censo Nacional de Población 2010;
     * en cuyo caso podrán avanzar en 3 eventos deportivos y 1 cultural.
     *
     * Adultos Mayores
     * 1 evento deportivo mas 1 evento cultural.
    */
    public function validarCompetidorGanadorMunicipal($planilla,$competidor)
    {
        $contTorneos = [
            "cultura" => 0,
            "deportes" => 0
        ];

        foreach ($competidor->getTorneosParticipaGandorMunicipal() as $torneo) {
            if ($torneo->getIsCultura()){
                $contTorneos["cultura"] ++;
            }else{
                $contTorneos["deportes"] ++;
            }
        }
        if ($this->getIsAdultosMayores()){
            if ($contTorneos["cultura"] > 0 && $this->getIsCultura()){
                throw new \Exception('Plenus: El competidor '.$competidor->getNombreCompleto().'('.$competidor->getDni().') ya está registrado en un evento cultural.');
            }elseif($contTorneos["deportes"] > 0 && !$this->getIsCultura()){
                throw new \Exception('Plenus: El competidor '.$competidor->getNombreCompleto().'('.$competidor->getDni().') ya está registrado en un evento deportivo.');
            }
        }else{
            if ($contTorneos["cultura"] > 0 && $this->getIsCultura()){
                throw new \Exception('Plenus: El competidor '.$competidor->getNombreCompleto().'('.$competidor->getDni().') ya está registrado en un evento cultural.');
            }
            if ($planilla->getMunicipio()->getHabitantes() > 30000){
                if ($contTorneos["deportes"] > 1 && !$this->getIsCultura()){
                    throw new \Exception('Plenus: El competidor '.$competidor->getNombreCompleto().'('.$competidor->getDni().') ya está registrado en dos eventos deportivos.');
                }
            }elseif($contTorneos["deportes"] > 2 && !$this->getIsCultura()){
                throw new \Exception('Plenus: El competidor '.$competidor->getNombreCompleto().'('.$competidor->getDni().') ya está registrado en tres eventos deportivos.');
            }
        }
    }
}
