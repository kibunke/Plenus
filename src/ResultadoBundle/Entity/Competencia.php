<?php
namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResultadoBundle\Entity\Competencia
 * @ORM\Table(name="Competencia")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *                          "competenciaLiga"                           = "CompetenciaLiga",
 *                          "competenciaLigaPuntos"                     = "CompetenciaLigaPuntos",
 *                          "competenciaLigaTantos"                     = "CompetenciaLigaTantos",
 *                          "competenciaLigaTantosCartas"               = "CompetenciaLigaTantosCartas",
 *                          "competenciaSerie"                          = "CompetenciaSerie",
 *                          "CompetenciaSerieTiempo"                    = "CompetenciaSerieTiempo",
 *                          "CompetenciaSerieDistancia"                 = "CompetenciaSerieDistancia",
 *                          "CompetenciaSeriePuntos"                    = "CompetenciaSeriePuntos",
 *                          "competenciaOrden"                          = "CompetenciaOrden",
 *                          "competenciaEliminacionDirecta"             = "CompetenciaEliminacionDirecta",
 *                          "competenciaEliminacionDirectaPuntos"       = "CompetenciaEliminacionDirectaPuntos",
 *                          "competenciaEliminacionDirectaTantos"       = "CompetenciaEliminacionDirectaTantos",
 *                          "competenciaEliminacionDirectaTantosCartas" = "CompetenciaEliminacionDirectaTantosCartas",
 *                          "competenciaMedallero"                      = "CompetenciaMedallero"
 *                      })
 */
abstract class Competencia
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
    * @var string $parametros
    *
    * @ORM\Column(name="parametros", type="text", nullable=true)
    */
    private $parametros;

    /**
     * @ORM\OneToOne(targetEntity="Etapa", inversedBy="competencia")
     * @ORM\JoinColumn(name="etapa", referencedColumnName="id")
     */
    private $etapa;

    /**
     * @ORM\OneToMany(targetEntity="Plaza", mappedBy="competencia", cascade={"persist"})
     * @ORM\OrderBy({"orden" = "ASC"})
     */
    private $plazas;

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

    public function __construct($user = NULL) {
       $this->createdAt = new \DateTime();
       $this->createdBy = $user;
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
     * @return Competencia
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
     * @return Competencia
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
     * @return Competencia
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
     * @return Competencia
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
     * Set etapa
     *
     * @param \ResultadoBundle\Entity\Etapa $etapa
     * @return Competencia
     */
    public function setEtapa(\ResultadoBundle\Entity\Etapa $etapa = null)
    {
        $this->etapa = $etapa;

        return $this;
    }

    /**
     * Get etapa
     *
     * @return \ResultadoBundle\Entity\Etapa
     */
    public function getEtapa()
    {
        return $this->etapa;
    }

    /**
     * Add plaza
     *
     * @param \ResultadoBundle\Entity\Plaza $plaza
     * @return Competencia
     */
    public function addPlazas(\ResultadoBundle\Entity\Plaza $plaza)
    {
        $this->plazas[] = $plaza;
        $plaza->setCompetencia($this);

        return $this;
    }

    /**
     * Remove plaza
     *
     * @param \ResultadoBundle\Entity\Plaza $plaza
     */
    public function removePlazas(\ResultadoBundle\Entity\Plaza $plaza)
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
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Competencia
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
     * @return Competencia
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
     * Set parametros
     *
     * @param string $parametros
     * @return Disciplina
     */
    public function setParametros($parametros)
    {
        $this->parametros = $parametros;

        return $this;
    }

    /**
     * Get parametros
     *
     * @return string
     */
    public function getParametros()
    {
        $param = $this->getEtapa()->getEvento()->getDisciplina()->getParametros();
        if ($param)
        {
            return $param;
        }
        return "{'PG': 3,'PE': 1,'PP': 0}";
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        //Calculado en las subClases
    }

    /**
     * Get idReload
     *
     * @return integer
     */
    public function getIdReload($plaza)
    {
        return $this->getId();
    }

    /**
     * Get partidos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPartidos()
    {
        return array();
    }

    /**
     * Get folder
     *
     * @return string
     */
    public function getMedalla($plaza)
    {
        return null;
    }

    /**
     * Get performance
     *
     * @return string
     */
    public function getPerformance($plaza)
    {
        return "Etapa ".strtoupper($this->getEtapa()->getTipo());
    }

    /**
     * Add plaza
     *
     * @param \ResultadoBundle\Entity\Plaza $plaza
     *
     * @return Competencia
     */
    public function addPlaza(\ResultadoBundle\Entity\Plaza $plaza)
    {
        $this->plazas[] = $plaza;

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
}
