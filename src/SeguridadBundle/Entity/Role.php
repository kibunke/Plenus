<?php

namespace SeguridadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * SeguridadBundle\Entity\Role
 * @ORM\Table(name="Role")
 * @ORM\Entity(repositoryClass="SeguridadBundle\Entity\Repository\RoleRepository")
 */
class Role implements RoleInterface
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

     /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Perfil", mappedBy="roles")
     * @ORM\JoinTable(name="perfil_role")
     */
    private $perfiles;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumn(name="createdBy", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @var boolean $isActive
     *
     * @ORM\Column(name="isActive", type="boolean")
     */
    private $isActive;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updatedAt", type="datetime",nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumn(name="modifiedBy", referencedColumnName="id",nullable=true)
     */
    private $modifiedBy;

    /**
     * implement Interface
     *
     */
    public function getRole()
    {
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->createdAt = new \DateTime();
        $this->name      = 'ROLE_';
    }

    /**
     * __toString
     */
    public function __toString()
    {
        return $this->getName();
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
     * Set name
     *
     * @param string $name
     *
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Role
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Role
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
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     *
     * @return Role
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Add profile
     *
     * @param \SeguridadBundle\Entity\Perfil $perfil
     *
     * @return Role
     */
    public function addProfile(\SeguridadBundle\Entity\Perfil $perfil)
    {
        $this->perfiles[] = $perfil;

        return $this;
    }

    /**
     * Remove profile
     *
     * @param \SeguridadBundle\Entity\Perfil $perfil
     */
    public function removeProfile(\SeguridadBundle\Entity\Perfil $perfil)
    {
        $this->perfiles->removeElement($perfil);
    }

    /**
     * Get profiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPerfiles()
    {
        return $this->perfiles;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     *
     * @return Role
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
     * Set modifiedBy
     *
     * @param \SeguridadBundle\Entity\Usuario $modifiedBy
     *
     * @return Role
     */
    public function setModifiedBy(\SeguridadBundle\Entity\Usuario $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;
        $this->modifiedAt = new \DateTime();

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return \SeguridadBundle\Entity\Usuario
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Role
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Role
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
     * Add perfile
     *
     * @param \SeguridadBundle\Entity\Perfil $perfile
     *
     * @return Role
     */
    public function addPerfile(\SeguridadBundle\Entity\Perfil $perfile)
    {
        $this->perfiles[] = $perfile;

        return $this;
    }

    /**
     * Remove perfile
     *
     * @param \SeguridadBundle\Entity\Perfil $perfile
     */
    public function removePerfile(\SeguridadBundle\Entity\Perfil $perfile)
    {
        $this->perfiles->removeElement($perfile);
    }
}
