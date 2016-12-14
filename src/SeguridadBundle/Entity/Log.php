<?php

namespace SeguridadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SeguridadBundle\Entity\Log
 * 
 * @ORM\Table(name="plenus_admin.Log")
 * @ORM\Entity(repositoryClass="SeguridadBundle\Entity\Repository\LogRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *                          "log"               = "Log"
 *                      })
 */

class Log
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
     * @var string $ip
     *
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string $activity
     *
     * @ORM\Column(name="activity", type="string", length=100)
     *
     * Ej : wrongPassword,wrongUser,login
     */
    private $activity;

    /**
     * @var string $activityGroup
     *
     * @ORM\Column(name="activityGroup", type="string", length=150)
     *
     * Ej : wrongPassword,wrongUser,login
     */
    private $activityGroup;
    
    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="logs")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     **/
    private $usuario;
    
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
     * __construct
     */    
    public function __construct($usuario,$ip,$activityGroup,$activity)
    {
        $this->usuario = $usuario;
        $this->ip = $ip;
        $this->activityGroup = $activityGroup;
        $this->activity = $activity;
        $this->createdAt = new \DateTime();
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
     * Set description
     *
     * @param string $description
     *
     * @return Log
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
     * merge description
     *
     * @return Log
     */
    public function mergeDescription($str)
    {
        if ($this->description){
            $this->description .= " | ".$str;    
        }else{
            $this->description .= $str;
        }
        

        return $this;
    }
    
    /**
     * Set activity
     *
     * @param string $activity
     *
     * @return Log
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity
     *
     * @return string
     */
    public function getActivity()
    {
        return $this->activity;
    }
    
    /**
     * Set activityGroup
     *
     * @param string $activityGroup
     *
     * @return Log
     */
    public function setActivityGroup($activityGroup)
    {
        $this->activityGroup = $activityGroup;

        return $this;
    }

    /**
     * Get activityGroup
     *
     * @return string
     */
    public function getActivityGroup()
    {
        return $this->activityGroup;
    }
    
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Log
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
     * Set createdBy
     *
     * @param User $createdBy
     *
     * @return Log
     */
    public function setCreatedBy(Usuario $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return Usuario
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Log
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

    /**
     * Set user
     *
     * @param \SeguridadBundle\Entity\Usuario $usuario
     *
     * @return Log
     */
    public function setUsuario(\SeguridadBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get user
     *
     * @return \SeguridadBundle\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
}
