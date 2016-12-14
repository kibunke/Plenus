<?php

namespace GestionBundle\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

use GestionBundle\Entity\ConfiguracionGlobal;

/**
 * GestionBundle\Services\PlenusConfig
 *
 */
class PlenusConfig
{    
    /**
     * @var EntityManager 
     */
    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getConfiguracion()
    {
        $gConfig = $this->em->getRepository('GestionBundle:ConfiguracionGlobal')->find(1);
        if (!$gConfig)
            return new ConfiguracionGlobal();
        return $gConfig;       
    }
    
    public function setConfiguracion()
    {
        $gConfig = $this->getConfiguracion();
        $gConfig->setIsNewAccountActive(true);
        $this->em->persist($gConfig);
        $this->em->flush(); 
        return $gConfig;       
    }
    
    public function isNewAccountActive(){
        return $this->getConfiguracion()->isNewAccountActive();
    }
    
    public function isResetPasswordActive(){
        return $this->getConfiguracion()->isResetPasswordActive();
    }    
}
