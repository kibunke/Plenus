<?php

namespace SeguridadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\Role;

/**
 * SeguridadBundle\Entity\Logs
 * 
 * @ORM\Table(name="Logs")
 * @ORM\Entity()
 */

class Logs
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
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumn(name="usuario", referencedColumnName="id")
     **/
    protected $usuario;
    
    /**
     * @var datetime $fecha
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;    
    
    /**
     * @var string $actividad
     *
     * @ORM\Column(name="actividad", type="string", length=50)
     */
    protected $actividad;
    
    /**
     * @var string $ip
     *
     * @ORM\Column(name="ip", type="string", length=50)
     */
    protected $ip;    
    
    /**
     * __construct
     */    
    public function __construct($user,$ip,$actividad='login')
    {
        $this->fecha = new \DateTime();
        $this->usuario = $user;
        $this->actividad = $actividad;
        $this->ip = $ip;   
    }
    
    /**
     * __toString
     */    
    public function __toString()
    {
        return 'logs';
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
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Logs
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set usuario
     *
     * @param \SeguridadBundle\Entity\Usuario $usuario
     * @return Logs
     */
    public function setUsuario(\SeguridadBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \SeguridadBundle\Entity\Usuario 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set actividad
     *
     * @param string $actividad
     * @return Logs
     */
    public function setActividad($actividad)
    {
        $this->actividad = $actividad;

        return $this;
    }

    /**
     * Get actividad
     *
     * @return string 
     */
    public function getActividad()
    {
        return $this->actividad;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Logs
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }
}
