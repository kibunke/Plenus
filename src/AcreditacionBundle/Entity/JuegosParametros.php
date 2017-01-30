<?php

namespace AcreditacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AcreditacionBundle\Entity\JuegosParametros
 * @ORM\Table(name="JuegosParametros")
 * @ORM\Entity(repositoryClass="AcreditacionBundle\Entity\Repository\JuegosParametrosRepository")
 */
class JuegosParametros {
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var date $fechaIniTrabajo
     * 
     * @ORM\Column(name="fechaIniTrabajo", type="date")
     */
    private $fechaIniTrabajo;
    
    /**
     * @var date $fechaFinTrabajo
     * 
     * @ORM\Column(name="fechaFinTrabajo", type="date")
     */
    private $fechaFinTrabajo;
    
    /**
     * @var date $fechaLimiteAcreditacion
     * 
     * @ORM\Column(name="fechaLimiteAcreditacion", type="date")
     */
    private $fechaLimiteAcreditacion;
    
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fechaIniTrabajo
     *
     * @param \DateTime $fechaIniTrabajo
     * @return JuegosParametros
     */
    public function setFechaIniTrabajo($fechaIniTrabajo)
    {
        $this->fechaIniTrabajo = $fechaIniTrabajo;

        return $this;
    }

    /**
     * Get fechaIniTrabajo
     *
     * @return \DateTime 
     */
    public function getFechaIniTrabajo()
    {
        return $this->fechaIniTrabajo;
    }

    /**
     * Set fechaFinTrabajo
     *
     * @param \DateTime $fechaFinTrabajo
     * @return JuegosParametros
     */
    public function setFechaFinTrabajo($fechaFinTrabajo)
    {
        $this->fechaFinTrabajo = $fechaFinTrabajo;

        return $this;
    }

    /**
     * Get fechaFinTrabajo
     *
     * @return \DateTime 
     */
    public function getFechaFinTrabajo()
    {
        return $this->fechaFinTrabajo;
    }

    /**
     * Set fechaLimiteAcreditacion
     *
     * @param \DateTime $fechaLimiteAcreditacion
     * @return JuegosParametros
     */
    public function setFechaLimiteAcreditacion($fechaLimiteAcreditacion)
    {
        $this->fechaLimiteAcreditacion = $fechaLimiteAcreditacion;

        return $this;
    }

    /**
     * Get fechaLimiteAcreditacion
     *
     * @return \DateTime 
     */
    public function getFechaLimiteAcreditacion()
    {
        return $this->fechaLimiteAcreditacion;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return JuegosParametros
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
     * @return JuegosParametros
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
     * @return JuegosParametros
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
     * @return JuegosParametros
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
