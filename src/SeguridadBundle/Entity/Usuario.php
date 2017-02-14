<?php

namespace SeguridadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Validator\Constraints as Assert;
//use Symfony\Component\Security\Core\Role\Role;

/**
 * SeguridadBundle\Entity\Usuario
 * 
 * @ORM\Table(name="Usuario", uniqueConstraints={
 *          @ORM\UniqueConstraint(name="unique_username", columns={"username"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="SeguridadBundle\Entity\Repository\UsuarioRepository")
 */
class Usuario implements AdvancedUserInterface, \Serializable
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
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=50, unique=true)
     */
    protected $username;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;
    
    /**
     * @var string $passwordHistory
     *
     * @ORM\Column(name="passwordHistory", type="json_array")
     */
    private $passwordHistory;
    
    /**
     * @ORM\OneToMany(targetEntity="Log", mappedBy="usuario")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $logs;

    /**
     * @var datetime $lastActivity
     *
     * @ORM\Column(name="lastActivity", type="datetime", nullable=true)
     */
    private $lastActivity;

    /**
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="usuario_role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="RESTRICT")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id",onDelete="RESTRICT")}
     *      )
     */
    private $roles;
    
    /**
     * @ORM\ManyToMany(targetEntity="InscripcionBundle\Entity\Segmento", mappedBy="coordinadores")
     **/
    private $coordina;
    
    /**
     * @ORM\ManyToOne(targetEntity="Perfil", inversedBy="usuarios")
     */
    private $perfil;
    
    /**
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\Persona", inversedBy="usuario", cascade={"all"})
     * @ORM\JoinColumn(name="persona_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $persona;
    
    /**
     * @var boolean $changePassword
     *
     * @ORM\Column(name="changePassword", type="boolean")
     */
    protected $changePassword;

    /**
     * @var boolean $checkData
     *
     * @ORM\Column(name="checkData", type="boolean")
     */
    protected $checkData;
    
    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Assert\Date()
     */
    private $createdAt;
    
    /**
     * @var User $createdBy
     * 
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
     * @var User $updatedBy
     * 
     * @ORM\ManyToOne(targetEntity="Usuario")
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
        $this->createdAt      = new \DateTime();
        $this->isActive       = false;
        $this->checkData      = true;
        $this->changePassword = true;
        $this->salt           = md5(uniqid(null, true));
        $this->logs           = new ArrayCollection();
        $this->roles          = new ArrayCollection();
    }
    
    /**
     * __toString
     */    
    public function __toString()
    {
        return $this->username;
    }
    
    /**
     * Get Roles implement Interface
     *
     * @return Array 
     */    
    public function getRoles()
    {
        $roles = array();
        //Agrega los roles del perfil
        if ($this->getPerfil())
        {
            foreach($this->getPerfil()->getRoles() as $rol)
            {
                if ($rol->getIsActive())
                    $roles[] = $rol->getName(); 
            }
        }
        
        //Agrega los roles asignados especificamente al usuario
        foreach($this->roles as $rol)
        {
           $roles[] = $rol->getName(); 
        }        
        
        return $roles;
    }
    
    /**
     * implement Interface
     */    
    public function eraseCredentials()
    {
    }
    
    /**
     * implement Interface
     *  
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            //$this->isActive
            // see section on salt below
            // $this->salt,
        ));
    }
    
    /**
     * implement Interface
     *     
     * @see \Serializable::unserialize()
    */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            //$this->isActive
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }
    
    /**
     * implement Interface
     */    
    public function isAccountNonExpired()
    {
        return true;
    }
    
    /**
     * implement Interface
     */     
    public function isAccountNonLocked()
    {
        return true;
    }
    
    /**
     * implement Interface
     */     
    public function isCredentialsNonExpired()
    {
        return true;
    }
    
    /**
     * implement Interface
     */     
    public function isEnabled()
    {
        return true;//$this->isActive;
    }    

    /**
     * implement Interface
     *
     */     
    public function getUsername()
    {
       return $this->username;
    }
    
    /**
     * implement Interface
     */     
    public function getSalt()
    {
        return $this->salt;
    }
    
    /**
     * implement Interface
     *
     */
    public function getPassword()
    {
        return $this->password;
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
     * Set username
     *
     * @param string $username
     *
     * @return Usuario
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return Usuario
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set passwordHistory
     *
     * @param array $passwordHistory
     *
     * @return Usuario
     */
    public function setPasswordHistory($passwordHistory)
    {
        $this->passwordHistory = $passwordHistory;

        return $this;
    }

    public function addPasswordHistory($datos = array())
    {
        $actual = $this->getPasswordHistory();
        $actual[] = $datos;
        $this->passwordHistory = $actual;
        
        return $this;
    }
    
    /**
     * Get passwordHistory
     *
     * @return array
     */
    public function getPasswordHistory()
    {
        return $this->passwordHistory;
    }

    /**
     * Set lastActivity
     *
     * @param \DateTime $lastActivity
     *
     * @return Usuario
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;

        return $this;
    }

    /**
     * Get lastActivity
     *
     * @return \DateTime
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * Set changePassword
     *
     * @param boolean $changePassword
     *
     * @return Usuario
     */
    public function setChangePassword($changePassword)
    {
        $this->changePassword = $changePassword;

        return $this;
    }

    /**
     * Get changePassword
     *
     * @return boolean
     */
    public function getChangePassword()
    {
        return $this->changePassword;
    }

    /**
     * Set checkData
     *
     * @param boolean $checkData
     *
     * @return Usuario
     */
    public function setCheckData($checkData)
    {
        $this->checkData = $checkData;

        return $this;
    }

    /**
     * Get checkData
     *
     * @return boolean
     */
    public function getCheckData()
    {
        return $this->checkData;
    }    
    
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Usuario
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
     * @return Usuario
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
     * Add log
     *
     * @param \SeguridadBundle\Entity\Log $log
     *
     * @return Usuario
     */
    public function addLog(\SeguridadBundle\Entity\Log $log)
    {
        $this->logs[] = $log;

        return $this;
    }

    /**
     * Remove log
     *
     * @param \SeguridadBundle\Entity\Log $log
     */
    public function removeLog(\SeguridadBundle\Entity\Log $log)
    {
        $this->logs->removeElement($log);
    }

    /**
     * Get logs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Add role
     *
     * @param \SeguridadBundle\Entity\Role $role
     *
     * @return Usuario
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
     * Set perfil
     *
     * @param \SeguridadBundle\Entity\Perfil $perfil
     *
     * @return Usuario
     */
    public function setPerfil(\SeguridadBundle\Entity\Perfil $perfil = null)
    {
        $this->perfil = $perfil;

        return $this;
    }

    /**
     * Get perfil
     *
     * @return \SeguridadBundle\Entity\Perfil
     */
    public function getPerfil()
    {
        return $this->perfil;
    }

    /**
     * Set persona
     *
     * @param \CommonBundle\Entity\Persona $persona
     *
     * @return Usuario
     */
    public function setPersona(\CommonBundle\Entity\Persona $persona = null)
    {
        $this->persona = $persona;

        return $this;
    }

    /**
     * Get persona
     *
     * @return \CommonBundle\Entity\Persona
     */
    public function getPersona()
    {
        return $this->persona;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     *
     * @return Usuario
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
     * @return Usuario
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
     * get Email
     *
     * @return boolean
     */
    public function getEmail()
    {
        return $this->getPersona()->getEmail();
    }
    
    /**
     * is ValidEmail
     *
     * @return boolean
     */
    public function isValidEmail($email)
    {
        if ($this->getEmail() == $email){
            return true;
        }
        return false;
    }
    
    /**
     * get Avatar
     *
     * @return boolean
     */
    public function getAvatar()
    {
        return $this->getPersona()->getAvatar();
    }
    
    /**
     * get NombreCompleto
     *
     */     
    public function getNombreCompleto()
    {
       return $this->persona->getNombreCompleto();
    }
    
    public function clearPersona()
    {
        $this->persona->clearUsuario();
        $this->persona = NULL;
        
        return $this;
    }
    
    public function hasRole($role = '')
    {
        return in_array($role,$this->getRoles());
    }
    
    public function mismoMunicipio(Usuario $user)
    {
        return $this->getPersona()->mismoMunicipio($user->getPersona());
    }
}
