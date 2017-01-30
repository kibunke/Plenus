<?php
namespace SeguridadBundle\Entity\Repository;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class UsuarioRepository extends EntityRepository implements UserProviderInterface
{
    
    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->where('u.usuario = :username')
            ->setParameter('username', $username)
            ->getQuery()
        ;

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $user = NULL;
        }
 
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
    
    public function getStats()
    {
        $em=$this->getEntityManager();
        $q='SELECT COUNT(DISTINCT(l.usuario)) as cant,DATE_FORMAT(l.fecha,"%d") as dia,DATE_FORMAT(l.fecha,"%d-%m-%Y") as fecha
            FROM Logs l
            WHERE l.actividad = "login" AND DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)<= DATE(fecha)
            GROUP BY dia
            ORDER BY l.fecha ASC';
        return $em->getConnection()->query($q)->fetchAll();
    }
}