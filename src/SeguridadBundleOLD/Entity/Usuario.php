<?php

namespace SeguridadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\Role;

/**
 * SeguridadBundle\Entity\Usuario
 * 
 * @ORM\Table(name="Usuario")
 * @ORM\Entity(repositoryClass="SeguridadBundle\Entity\Repository\UsuarioRepository")
 */

class Usuario implements UserInterface, \Serializable
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
     * @var string $usuario
     *
     * @ORM\Column(name="usuario", type="string", length=50, unique=true)
     */
    protected $usuario;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    protected $nombre;

    /**
     * @var string $apellido
     *
     * @ORM\Column(name="apellido", type="string", length=100)
     */
    protected $apellido;

    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;
    
    /**
     * @var boolean $activo
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    protected $activo;

    /**
     * @ORM\OneToOne(targetEntity="\CommonBundle\Entity\Adjunto")
     * @ORM\JoinColumn(name="adjunto_id", referencedColumnName="id", nullable=true)
     **/
    private $avatar;
    
    /**
     * @ORM\ManyToOne(targetEntity="\CommonBundle\Entity\Localidad")
     * @ORM\JoinColumn(name="localidad_id", referencedColumnName="id")
     **/
    private $localidad;    
    
    /**
     * @var string $telefono
     *
     * @ORM\Column(name="telefono", type="string", length=100, nullable=true)
     */
    private $telefono;
     
    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     */
    private $email;     

    /**
     * @ORM\ManyToOne(targetEntity="\CommonBundle\Entity\Funcion", inversedBy="usuarios")
     * @ORM\JoinColumn(name="funcion", referencedColumnName="id")
     **/
    private $funcion;

    /**
     * @var string $facebook
     *
     * @ORM\Column(name="facebook", type="string", length=30, nullable=true)
     */
    private $facebook;     

    /**
     * @var string $skype
     *
     * @ORM\Column(name="skype", type="string", length=30, nullable=true)
     */
    private $skype;
     
    /**
     * @var string $twitter
     *
     * @ORM\Column(name="twitter", type="string", length=30, nullable=true)
     */
    private $twitter;

    /**
     * @var string $gplus
     *
     * @ORM\Column(name="gplus", type="string", length=30, nullable=true)
     */
    private $gplus;
     
    /**
     * @var string $linkedin
     *
     * @ORM\Column(name="linkedin", type="string", length=30, nullable=true)
     */
    private $linkedin;
     
    /**
     * @var datetime $fechaNacimiento
     *
     * @ORM\Column(name="fechaNacimiento", type="datetime", nullable=true)
     */
    private $fechaNacimiento;
     
    /**
     * @var boolean $logueado
     *
     * @ORM\Column(name="logueado", type="boolean")
     */
    protected $logueado;
        
    /**
     * @var string $ip_login
     *
     * @ORM\Column(name="ip_login", type="string", length=255)
     */
    protected $ip_login;

    /**
     * @var datetime $ultimoLogin
     *
     * @ORM\Column(name="ultimoLogin", type="datetime", nullable=true)
     */
    private $ultimoLogin;

    /**
     * @var datetime $ultimaOperacion
     *
     * @ORM\Column(name="ultimOperacion", type="datetime", nullable=true)
     */
    private $ultimaOperacion;

    /**
     * @ORM\ManyToMany(targetEntity="Rol")
     * @ORM\JoinTable(name="services_juegosba_admin.usuario_rol",
     *      joinColumns={@ORM\JoinColumn(name="usuario_id", referencedColumnName="id", onDelete="RESTRICT")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="rol_id", referencedColumnName="id",onDelete="RESTRICT")}
     *      )
     */
    private $roles;
    
    /**
     * @ORM\ManyToOne(targetEntity="Perfil", inversedBy="usuarios")
     */
    private $perfil;
    
    /**
     * @var string $passwordGenerada
     *
     * @ORM\Column(name="password_generada", type="string", length=255)
     */
    protected $passwordGenerada;

    /**
     * @ORM\ManyToMany(targetEntity="ResultadoBundle\Entity\Evento", mappedBy="coordinadores")
     **/
    private $coordina;
    
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
    
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * __construct
     */    
    public function __construct()
    {
        $this->activo    = true;
        $this->logueado  = false;
        $this->ultimoLogin = new \DateTime();
        $this->salt      = md5(uniqid(null, true));
        $this->ip_login  = '';
        $this->createdAt = new \DateTime();
    }
    
    /**
     * __toString
     */    
    public function __toString()
    {
        return $this->getNombreCompleto() . ' (' . $this->getUsuario() . ')';
    }

    /**
     * Get nombreCompleto
     *
     * @return string 
     */
    public function getNombreCompleto()
    {
        return $this->getApellido().", ".$this->getNombre();
    }

    public function registrarIngreso($ip = null)
    {
        $this->ultimoLogin     = new \DateTime();
        $this->ultimaOperacion = new \DateTime();
        $this->logueado        = true;
        $this->ip_login        = $ip;
    }
    
    /**
     * Get Roles implement Interface
     *
     * @return Array 
     */    
    public function getRoles()
    {
        $roles = array();
        if ($this->getPerfil()){
            foreach($this->getPerfil()->getRoles() as $rol)
            {
               $roles[] = $rol->getNombre(); 
            }
        }
        foreach($this->roles as $rol)
        {
           $roles[] = $rol->getNombre(); 
        }        
        return $roles;
    }
    
    public function isRole($role)
    {
        if (in_array($role,$this->getRoles()))
        {
            return true;
        }
        return false;
    }

    public function hasAccessAtEvento($evento)
    {
        if ($this->isRole('ROLE_ADMIN') || $this->isRole('ROLE_DIRECTOR') || $this->isRole('ROLE_DATAENTRY') || $evento->hasAccess($this)){
            return true;
        }
        return false;
    }   
    
    /**
     * implement Interface
     *
     */    
    public function eraseCredentials()
    {
        
    }
    
    /**
     * implement Interface
     *
     */        
    public function serialize()
    {
        return serialize(array($this->getId(),$this->getUsername(),$this->getActivo()));
    }
    
    /**
     * implement Interface
     *
     */        
    public function unserialize($data)
    {
        list($this->id,$this->usuario,$this->activo)= unserialize($data);
    }
    
    /**
     * implement Interface
     *
     */     
    public function getSalt()
    {
        return null;
    }
    
    /**
     * implement Interface
     *
     */     
    public function getUsername()
    {
       return $this->getUsuario();
    }

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
     * Set usuario
     *
     * @param string $usuario
     * @return Usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Usuario
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
     * Set apellido
     *
     * @param string $apellido
     * @return Usuario
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string 
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Usuario
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return Usuario
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     * @return Usuario
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Usuario
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set funcion
     *
     * @param string $funcion
     * @return Usuario
     */
    public function setFuncion($funcion)
    {
        $this->funcion = $funcion;

        return $this;
    }

    /**
     * Get funcion
     *
     * @return string 
     */
    public function getFuncion()
    {
        return $this->funcion;
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     * @return Usuario
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string 
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set skype
     *
     * @param string $skype
     * @return Usuario
     */
    public function setSkype($skype)
    {
        $this->skype = $skype;

        return $this;
    }

    /**
     * Get skype
     *
     * @return string 
     */
    public function getSkype()
    {
        return $this->skype;
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     * @return Usuario
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return string 
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set gplus
     *
     * @param string $gplus
     * @return Usuario
     */
    public function setGplus($gplus)
    {
        $this->gplus = $gplus;

        return $this;
    }

    /**
     * Get gplus
     *
     * @return string 
     */
    public function getGplus()
    {
        return $this->gplus;
    }

    /**
     * Set linkedin
     *
     * @param string $linkedin
     * @return Usuario
     */
    public function setLinkedin($linkedin)
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    /**
     * Get linkedin
     *
     * @return string 
     */
    public function getLinkedin()
    {
        return $this->linkedin;
    }

    /**
     * Set fechaNacimiento
     *
     * @param \DateTime $fechaNacimiento
     * @return Usuario
     */
    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    /**
     * Get fechaNacimiento
     *
     * @return \DateTime 
     */
    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    /**
     * Set logueado
     *
     * @param boolean $logueado
     * @return Usuario
     */
    public function setLogueado($logueado)
    {
        $this->logueado = $logueado;

        return $this;
    }

    /**
     * Get logueado
     *
     * @return boolean 
     */
    public function getLogueado()
    {
        return $this->logueado;
    }

    /**
     * Set ip_login
     *
     * @param string $ipLogin
     * @return Usuario
     */
    public function setIpLogin($ipLogin)
    {
        $this->ip_login = $ipLogin;

        return $this;
    }

    /**
     * Get ip_login
     *
     * @return string 
     */
    public function getIpLogin()
    {
        return $this->ip_login;
    }

    /**
     * Set ultimoLogin
     *
     * @param \DateTime $ultimoLogin
     * @return Usuario
     */
    public function setUltimoLogin($ultimoLogin)
    {
        $this->ultimoLogin = $ultimoLogin;

        return $this;
    }

    /**
     * Get ultimoLogin
     *
     * @return \DateTime 
     */
    public function getUltimoLogin()
    {
        return $this->ultimoLogin;
    }

    /**
     * Set ultimaOperacion
     *
     * @param \DateTime $ultimaOperacion
     * @return Usuario
     */
    public function setUltimaOperacion($ultimaOperacion)
    {
        $this->ultimaOperacion = $ultimaOperacion;

        return $this;
    }

    /**
     * Get ultimaOperacion
     *
     * @return \DateTime 
     */
    public function getUltimaOperacion()
    {
        return $this->ultimaOperacion;
    }

    /**
     * Set passwordGenerada
     *
     * @param string $passwordGenerada
     * @return Usuario
     */
    public function setPasswordGenerada($passwordGenerada)
    {
        $this->passwordGenerada = $passwordGenerada;

        return $this;
    }

    /**
     * Get passwordGenerada
     *
     * @return string 
     */
    public function getPasswordGenerada()
    {
        return $this->passwordGenerada;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
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
     * Set avatar
     *
     * @param \CommonBundle\Entity\Adjunto $avatar
     * @return Usuario
     */
    public function setAvatar(\CommonBundle\Entity\Adjunto $avatar = null)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return \CommonBundle\Entity\Adjunto 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set localidad
     *
     * @param \CommonBundle\Entity\Localidad $localidad
     * @return Usuario
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
     * Add roles
     *
     * @param \SeguridadBundle\Entity\Rol $roles
     * @return Usuario
     */
    public function addRole(\SeguridadBundle\Entity\Rol $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \SeguridadBundle\Entity\Rol $roles
     */
    public function removeRole(\SeguridadBundle\Entity\Rol $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Set perfil
     *
     * @param \SeguridadBundle\Entity\Perfil $perfil
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
     * Set usuarioCreo
     *
     * @param \SeguridadBundle\Entity\Usuario $usuarioCreo
     * @return Usuario
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
     * @return Usuario
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
     * Add coordina
     *
     * @param \ResultadoBundle\Entity\Evento $coordina
     * @return Usuario
     */
    public function addCoordina(\ResultadoBundle\Entity\Evento $coordina)
    {

        $this->coordina[] = $coordina;

        return $this;
    }

    /**
     * Remove coordina
     *
     * @param \ResultadoBundle\Entity\Evento $coordina
     */
    public function removeCoordina(\ResultadoBundle\Entity\Evento $coordina)
    {
        $this->coordina->removeElement($coordina);
    }

    /**
     * Get coordina
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCoordina()
    {
        return $this->coordina;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
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
     * @return Usuario
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
