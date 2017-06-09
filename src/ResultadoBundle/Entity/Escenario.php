<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\Escenario
 * @ORM\Table(name="Escenario")
 * @ORM\Entity(repositoryClass="ResultadoBundle\Entity\Repository\EscenarioRepository")
 */
class Escenario
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
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string")
     */
    private $nombre;

    /**
     * @var string $calle
     *
     * @ORM\Column(name="calle", type="string")
     */
    private $calle;

    /**
     * @var string $numero
     *
     * @ORM\Column(name="numero", type="string", nullable=true)
     */
    private $numero;

    /**
     * @var string $entreCalle1
     *
     * @ORM\Column(name="entreCalle1", type="string", nullable=true)
     */
    private $entreCalle1;

    /**
     * @var string $entreCalle2
     *
     * @ORM\Column(name="entreCalle2", type="string", nullable=true)
     */
    private $entreCalle2;

    /**
     * @var string $esquina
     *
     * @ORM\Column(name="esquina", type="string", nullable=true)
     */
    private $esquina;

    /**
     * @var string $latlng
     *
     * @ORM\Column(name="latlng", type="string", nullable=true)
     */
    private $latlng;

    /**
     * @ORM\ManyToOne(targetEntity="\CommonBundle\Entity\Localidad")
     * @ORM\JoinColumn(name="localidad", referencedColumnName="id")
     */
    private $localidad;

    /**
     * @ORM\OneToMany(targetEntity="Cronograma", mappedBy="escenario")
     */
    private $cronogramas;

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
     * __toString
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
     * @return Escenario
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
     * Get direccion
     *
     * @return string
     */
    public function getDireccionRaw()
    {
        if ($this->numero)
            return "<strong>".$this->calle."</strong><small> N° ".$this->numero.", ".$this->getLocalidad()->getNombre()."</small>";
        else
            return "<strong>".$this->calle."</strong><small> esq. ".$this->esquina.", ".$this->getLocalidad()->getNombre()."</small>";
    }

    /**
     * Set calle
     *
     * @param string $calle
     * @return Escenario
     */
    public function setCalle($calle)
    {
        $this->calle = $calle;

        return $this;
    }

    /**
     * Get calle
     *
     * @return string
     */
    public function getCalle()
    {
        return $this->calle;
    }

    /**
     * Set latlng
     *
     * @param string $latlng
     * @return Escenario
     */
    public function setLatlng($latlng)
    {
        $this->latlng = $latlng;

        return $this;
    }

    /**
     * Get latlng
     *
     * @return string
     */
    public function getLatlng()
    {
        return $this->latlng;
    }

    /**
     * Add cronograma
     *
     * @param \ResultadoBundle\Entity\Cronograma $plaza
     * @return Escenario
     */
    public function addCronograma(\ResultadoBundle\Entity\Cronograma $cronograma)
    {
        $this->cronogramas[] = $cronograma;
        $cronograma->setEscenario($this);

        return $this;
    }

    /**
     * Remove cronograma
     *
     * @param \ResultadoBundle\Entity\Cronograma $cronograma
     */
    public function removeCronograma(\ResultadoBundle\Entity\Cronograma $cronograma)
    {
        $this->cronogramas->removeElement($cronograma);
    }

    /**
     * Get cronogramas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCronogramas()
    {
        return $this->cronogramas;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Escenario
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
     * @return Escenario
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
     * @return Escenario
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
     * @return Escenario
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
     * Constructor
     */
    public function __construct($user = null)
    {
        $this->createdAt = new \DateTime();
        $this->createdBy = $user;
        $this->cronogramas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Escenario
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
     * Set numero
     *
     * @param string $numero
     * @return Escenario
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set entreCalle1
     *
     * @param string $entreCalle1
     * @return Escenario
     */
    public function setEntreCalle1($entreCalle1)
    {
        $this->entreCalle1 = $entreCalle1;

        return $this;
    }

    /**
     * Get entreCalle1
     *
     * @return string
     */
    public function getEntreCalle1()
    {
        return $this->entreCalle1;
    }

    /**
     * Set entreCalle2
     *
     * @param string $entreCalle2
     * @return Escenario
     */
    public function setEntreCalle2($entreCalle2)
    {
        $this->entreCalle2 = $entreCalle2;

        return $this;
    }

    /**
     * Get entreCalle2
     *
     * @return string
     */
    public function getEntreCalle2()
    {
        return $this->entreCalle2;
    }

    /**
     * Set localidad
     *
     * @param \CommonBundle\Entity\Localidad $localidad
     * @return Escenario
     */
    public function setLocalidad(\CommonBundle\Entity\Localidad $localidad = null)
    {
        $this->localidad = $localidad;

        return $this;
    }

    /**
     * Get localidad
     *
     * @return \CommonBundle\Entity\Localidad
     */
    public function getLocalidad()
    {
        return $this->localidad;
    }

    /**
     * Set esquina
     *
     * @param string $esquina
     * @return Escenario
     */
    public function setEsquina($esquina)
    {
        $this->esquina = $esquina;

        return $this;
    }

    /**
     * Get esquina
     *
     * @return string
     */
    public function getEsquina()
    {
        return $this->esquina;
    }
}
