<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * GestionBundle\Entity\ConfiguracionGlobal
 *
 * @ORM\Table(name="ConfiguracionGlobal")
 * @ORM\Entity()
 */
class ConfiguracionGlobal
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
     * @var boolean $isNewAccountActive
     *
     * @ORM\Column(name="isNewAccountActive", type="boolean")
     */
    protected $isNewAccountActive;

    /**
     * @var boolean $isResetPasswordActive
     *
     * @ORM\Column(name="isResetPasswordActive", type="boolean")
     */
    protected $isResetPasswordActive;
    
    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Assert\Date()
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
     * @Assert\Date()
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
     * @var boolean $isActive
     *
     * @ORM\Column(name="isActive", type="boolean")
     */
    protected $isActive;
    
    /**
     * Constructor
     */    
    public function __construct()
    {
        //Esto es para bloquear el new comentar para crear una entidad nueva
        throw new \Exception('Singleton Class.');
        $this->id = 1;
        $this->createdAt = new \DateTime();
        $this->isActive = true;
    }
    
    /**
     * __toString
     */    
    function __toString()
    {
        return "ConfigGlobal";
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
     * Set isNewAccountActive
     *
     * @param boolean $isNewAccountActive
     *
     * @return ConfiguracionGlobal
     */
    public function setIsNewAccountActive($isNewAccountActive)
    {
        $this->isNewAccountActive = $isNewAccountActive;

        return $this;
    }

    /**
     * Get isNewAccountActive
     *
     * @return boolean
     */
    public function isNewAccountActive()
    {
        return $this->isNewAccountActive;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ConfiguracionGlobal
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
     * @return ConfiguracionGlobal
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return ConfiguracionGlobal
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
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     *
     * @return ConfiguracionGlobal
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
     * @return ConfiguracionGlobal
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
     * Get isNewAccountActive
     *
     * @return boolean
     */
    public function getIsNewAccountActive()
    {
        return $this->isNewAccountActive;
    }

    /**
     * Set isResetPasswordActive
     *
     * @param boolean $isResetPasswordActive
     *
     * @return ConfiguracionGlobal
     */
    public function setIsResetPasswordActive($isResetPasswordActive)
    {
        $this->isResetPasswordActive = $isResetPasswordActive;

        return $this;
    }

    /**
     * Get isResetPasswordActive
     *
     * @return boolean
     */
    public function isResetPasswordActive()
    {
        return $this->isResetPasswordActive;
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
}
