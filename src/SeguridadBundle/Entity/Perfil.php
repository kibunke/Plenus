<?php

namespace SeguridadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SeguridadBundle\Entity\Perfil
 *
 * @ORM\Entity
 * @ORM\Table(name="plenus_admin.Perfil")
 * @ORM\Entity(repositoryClass="SeguridadBundle\Entity\Repository\PerfilRepository")
 * 
 */
class Perfil
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
     * @var string $legend
     *
     * @ORM\Column(name="legend", type="string", length=255)
     */
    private $legend;
    
     /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="perfiles")
     * @ORM\JoinTable(name="plenus_admin.perfil_role")
     */
    private $roles;
    
    /**
     * @ORM\OneToMany(targetEntity="Usuario", mappedBy="perfil")
     */
    private $usuarios;
    
    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Assert\Date()
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario")
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
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumn(name="updatedBy", referencedColumnName="id")
     */
    private $updatedBy;
    
    /**
     * @var boolean $availableForNewUsers
     *
     * @ORM\Column(name="availableForNewUsers", type="boolean")
     */
    private $availableForNewUsers;
    
    /**
     * @var boolean $isActive
     *
     * @ORM\Column(name="isActive", type="boolean")
     */
    private $isActive;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive  = true;
        $this->createdAt = new \DateTime();
        $this->roles     = new ArrayCollection();
        $this->usuarios  = new ArrayCollection();
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
     * @return Perfil
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
     * Set legend
     *
     * @param string $legend
     *
     * @return Perfil
     */
    public function setLegend($legend)
    {
        $this->legend = $legend;

        return $this;
    }

    /**
     * Get legend
     *
     * @return string
     */
    public function getLegend()
    {
        return $this->legend;
    }
    
    /**
     * Set description
     *
     * @param string $description
     *
     * @return Perfil
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
     * @return Perfil
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
     * @return Perfil
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
     * Add role
     *
     * @param \SeguridadBundle\Entity\Role $role
     *
     * @return Perfil
     */
    public function addRole(\SeguridadBundle\Entity\Role $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Remove role
     *
     * @param \SeguridadBundle\Entity\Role $role
     */
    public function removeRole(\SeguridadBundle\Entity\Role $role)
    {
        $this->roles->removeElement($role);
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add usuario
     *
     * @param \SeguridadBundle\Entity\Usuario $user
     *
     * @return Perfil
     */
    public function addUsuario(\SeguridadBundle\Entity\Usuario $usuario)
    {
        $this->usuario[] = $usuario;

        return $this;
    }

    /**
     * Remove usuario
     *
     * @param \SeguridadBundle\Entity\Usuario $usuario
     */
    public function removeUsuario(\SeguridadBundle\Entity\Usuario $usuario)
    {
        $this->usuarios->removeElement($usuario);
    }

    /**
     * Get usuarios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     *
     * @return Perfil
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
     * @return Perfil
     */
    public function setUpdatedBy(\SeguridadBundle\Entity\Usuario $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;
        $this->updatedAt = new \DateTime();
        
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Perfil
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
     * Set availableForNewUsers
     *
     * @param boolean $availableForNewUsers
     *
     * @return Perfil
     */
    public function setAvailableForNewUsers($availableForNewUsers)
    {
        $this->availableForNewUsers = $availableForNewUsers;

        return $this;
    }

    /**
     * Get availableForNewUsers
     *
     * @return boolean
     */
    public function getAvailableForNewUsers()
    {
        return $this->availableForNewUsers;
    }
    
    public function hasRole($role)
    {
        return (in_array($role,$this->getRoles()->toArray()));
    }
}
