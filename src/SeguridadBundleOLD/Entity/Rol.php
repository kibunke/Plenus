<?php

namespace SeguridadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * SeguridadBundle\Entity\Rol
 * @ORM\Table(name="Rol")
 * @ORM\Entity(repositoryClass="SeguridadBundle\Entity\Repository\RolRepository")
 */
class Rol implements RoleInterface
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
     * @ORM\Column(name="nombre", type="string", length=255, unique=true)
     */
    private $nombre;

     /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;
    
    /**
     * @var boolean $soloAdmin
     *
     * @ORM\Column(name="soloAdmin", type="boolean", nullable=true)
     */
    protected $soloAdmin;

    /**
     * @ORM\ManyToMany(targetEntity="Perfil", mappedBy="roles")
     * @ORM\JoinTable(name="perfil_rol")
     */  
    private $perfiles;
    
    /**
     * @var smallint $ordenGrupo
     *
     * @ORM\Column(name="ordenGrupo", type="smallint")
     */
    private $ordenGrupo;
    
    /**
     * @var smallint $ordenDentroGrupo
     *
     * @ORM\Column(name="ordenDentroGrupo", type="smallint")
     */
    private $ordenDentroGrupo;
    
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
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumn(name="updatedBy", referencedColumnName="id")
     */   
    private $updatedBy;
    
    public function getRole(){}
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->usuarios = new \Doctrine\Common\Collections\ArrayCollection();
        $this->perfiles = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Rol
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
     * Set soloAdmin
     *
     * @param boolean $soloAdmin
     * @return Rol
     */
    public function setSoloAdmin($soloAdmin)
    {
        $this->soloAdmin = $soloAdmin;

        return $this;
    }

    /**
     * Get soloAdmin
     *
     * @return boolean 
     */
    public function getSoloAdmin()
    {
        return $this->soloAdmin;
    }

    /**
     * Set ordenGrupo
     *
     * @param integer $ordenGrupo
     * @return Rol
     */
    public function setOrdenGrupo($ordenGrupo)
    {
        $this->ordenGrupo = $ordenGrupo;

        return $this;
    }

    /**
     * Get ordenGrupo
     *
     * @return integer 
     */
    public function getOrdenGrupo()
    {
        return $this->ordenGrupo;
    }

    /**
     * Set ordenDentroGrupo
     *
     * @param integer $ordenDentroGrupo
     * @return Rol
     */
    public function setOrdenDentroGrupo($ordenDentroGrupo)
    {
        $this->ordenDentroGrupo = $ordenDentroGrupo;

        return $this;
    }

    /**
     * Get ordenDentroGrupo
     *
     * @return integer 
     */
    public function getOrdenDentroGrupo()
    {
        return $this->ordenDentroGrupo;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Rol
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
     * @return Rol
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
     * Add usuarios
     *
     * @param \SeguridadBundle\Entity\Usuario $usuarios
     * @return Rol
     */
    public function addUsuario(\SeguridadBundle\Entity\Usuario $usuarios)
    {
        $this->usuarios[] = $usuarios;

        return $this;
    }

    /**
     * Remove usuarios
     *
     * @param \SeguridadBundle\Entity\Usuario $usuarios
     */
    public function removeUsuario(\SeguridadBundle\Entity\Usuario $usuarios)
    {
        $this->usuarios->removeElement($usuarios);
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
     * Add perfiles
     *
     * @param \SeguridadBundle\Entity\Perfil $perfiles
     * @return Rol
     */
    public function addPerfile(\SeguridadBundle\Entity\Perfil $perfiles)
    {
        $this->perfiles[] = $perfiles;

        return $this;
    }

    /**
     * Remove perfiles
     *
     * @param \SeguridadBundle\Entity\Perfil $perfiles
     */
    public function removePerfile(\SeguridadBundle\Entity\Perfil $perfiles)
    {
        $this->perfiles->removeElement($perfiles);
    }

    /**
     * Get perfiles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPerfiles()
    {
        return $this->perfiles;
    }

    /**
     * Set usuarioCreo
     *
     * @param \SeguridadBundle\Entity\Usuario $usuarioCreo
     * @return Rol
     */
    public function setUsuarioCreo(\SeguridadBundle\Entity\Usuario $usuarioCreo = null)
    {
        $this->usuarioCreo = $usuarioCreo;

        return $this;
    }

    /**
     * Get usuarioCreo
     *
     * @return \SeguridadBundle\Entity\Usuario 
     */
    public function getUsuarioCreo()
    {
        return $this->usuarioCreo;
    }

    /**
     * Set usuarioModifico
     *
     * @param \SeguridadBundle\Entity\Usuario $usuarioModifico
     * @return Rol
     */
    public function setUsuarioModifico(\SeguridadBundle\Entity\Usuario $usuarioModifico = null)
    {
        $this->usuarioModifico = $usuarioModifico;

        return $this;
    }

    /**
     * Get usuarioModifico
     *
     * @return \SeguridadBundle\Entity\Usuario 
     */
    public function getUsuarioModifico()
    {
        return $this->usuarioModifico;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Rol
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
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Rol
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
     * @return Rol
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
