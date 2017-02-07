<?php

namespace SeguridadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sessions
 *
 * @ORM\Table(name="Sessions")
 * @ORM\Entity
 */
class Sessions
{
    /**
     * #var binary
     *
     * #ORM\Column(name="sess_id", type="binary")
     * #ORM\Id
     * #ORM\GeneratedValue(strategy="IDENTITY")
     */
    //private $sessId;
    
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="sess_id", type="string", length=128, nullable=false)
     */
    private $sessId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="sess_data", type="blob", length=65535, nullable=false)
     */
    private $sessData;

    /**
     * @var integer
     *
     * @ORM\Column(name="sess_time", type="integer", nullable=false)
     */
    private $sessTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="sess_lifetime", type="integer", nullable=false)
     */
    private $sessLifetime;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="userId", type="integer", nullable=true)
     */
    private $userId;

    /**
     * Set sessData
     *
     * @param string $sessData
     *
     * @return Sessions
     */
    public function setSessData($sessData)
    {
        $this->sessData = $sessData;

        return $this;
    }

    /**
     * Get sessData
     *
     * @return string
     */
    public function getSessData()
    {
        return $this->sessData;
    }

    /**
     * Set sessTime
     *
     * @param integer $sessTime
     *
     * @return Sessions
     */
    public function setSessTime($sessTime)
    {
        $this->sessTime = $sessTime;

        return $this;
    }

    /**
     * Get sessTime
     *
     * @return integer
     */
    public function getSessTime()
    {
        return $this->sessTime;
    }

    /**
     * Set sessLifetime
     *
     * @param integer $sessLifetime
     *
     * @return Sessions
     */
    public function setSessLifetime($sessLifetime)
    {
        $this->sessLifetime = $sessLifetime;

        return $this;
    }

    /**
     * Get sessLifetime
     *
     * @return integer
     */
    public function getSessLifetime()
    {
        return $this->sessLifetime;
    }

    /**
     * Get sessId
     *
     * @return binary
     */
    public function getSessId()
    {
        return $this->sessId;
    }
    
    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     *
     * @return Email
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
}
