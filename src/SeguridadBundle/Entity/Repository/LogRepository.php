<?php

namespace SeguridadBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * LogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LogRepository extends EntityRepository
{
    public function getLoginActivitySinceByIp($clientIp,$d1)
    {
        return $this->getEntityManager()
                    ->createQuery(" SELECT l
                                    FROM SeguridadBundle:Log l
                                    WHERE l.ip = ?1 AND l.createdAt > ?2 AND l.activityGroup = 'loginFailure'")
                    ->setParameter(1,$clientIp)
                    ->setParameter(2,$d1)
                    ->getResult();
    }
    //public function getStats()
    //{
    //    return $this->getEntityManager()
    //                ->createQuery(" SELECT COUNT(l) as cant,CONCAT(SUBSTRING(l.createdAt,9,2),SUBSTRING(l.createdAt,5,3)) as day
    //                                FROM SeguridadBundle:Log l
    //                                WHERE DATE_DIFF(l.createdAt, ?1)>=-15 AND l.actividad = 'login'
    //                                GROUP BY day
    //                                ORDER BY l.createdAt")
    //                ->setParameter(1,new \DateTime())
    //                ->getResult();
    //} 
    //
    //public function getStatsUser($user)
    //{
    //    return $this->getEntityManager()
    //                ->createQuery(" SELECT COUNT(l) as cant,CONCAT(SUBSTRING(l.createdAt,9,2),SUBSTRING(l.createdAt,5,3)) as day
    //                                FROM SeguridadBundle:Log l
    //                                WHERE l.usuario = ?2 AND (DATE_DIFF(l.createdAt, ?1)>=-15 AND l.actividad = 'login')
    //                                GROUP BY day
    //                                ORDER BY l.createdAt")
    //                ->setParameter(1,new \DateTime())
    //                ->setParameter(2,$user->getId())
    //                ->getResult();
    //}     
    //public function logsUserDatatable($request,$user)
    //{
    //    $columns = ["id","createdAt","actividad","ip","observacion","actions"];
    //    return array(   "total" =>  $this->getEntityManager()
    //                                    ->createQuery(" SELECT COUNT(l)
    //                                                    FROM SeguridadBundle:Log l
    //                                                    WHERE l.usuario = ?2")
    //                                    ->setParameter(2,$user->getId())
    //                                    ->getSingleScalarResult(),
    //                    "count" =>  $this->getEntityManager()
    //                                    ->createQuery(" SELECT COUNT(l)
    //                                                    FROM SeguridadBundle:Log l
    //                                                    WHERE l.usuario = ?2 AND (l.ip LIKE ?1 OR l.actividad LIKE ?1 OR l.observacion LIKE ?1)")
    //                                    ->setParameter(1,'%'.$request->get('search')['value'].'%')
    //                                    ->setParameter(2,$user->getId())
    //                                    ->getSingleScalarResult(),
    //                    "stats" =>  $this->getEntityManager()
    //                                    ->createQuery(" SELECT l.actividad,COUNT(l)
    //                                                    FROM SeguridadBundle:Log l
    //                                                    WHERE l.usuario = ?1
    //                                                    GROUP BY l.actividad")
    //                                    ->setParameter(1,$user->getId())
    //                                    ->getScalarResult(),                                        
    //                    "logs" =>   $this->getEntityManager()
    //                                    ->createQuery(" SELECT l
    //                                                    FROM SeguridadBundle:Log l
    //                                                    WHERE l.usuario = ?2 AND (l.ip LIKE ?1 OR l.actividad LIKE ?1 OR l.observacion LIKE ?1)
    //                                                    ORDER BY "."l.".$columns[$request->get('order')[0]['column']]." ".$request->get('order')[0]['dir'])
    //                                    ->setParameter(1,'%'.$request->get('search')['value'].'%')
    //                                    ->setParameter(2,$user->getId())
    //                                    ->setMaxResults($request->get('length'))
    //                                    ->setFirstResult($request->get('start'))
    //                                    ->getResult()
    //        );
    //}
    //
    //public function logsDatatable($request)
    //{
    //    $columns = ["icon","usuario","createdAt","actividad","ip","observacion","actions"];
    //    return array(   "total" =>  $this->getEntityManager()
    //                                    ->createQuery(" SELECT COUNT(l)
    //                                                    FROM SeguridadBundle:Log l")
    //                                    ->getSingleScalarResult(),
    //                    "count" =>  $this->getEntityManager()
    //                                    ->createQuery(" SELECT COUNT(l)
    //                                                    FROM SeguridadBundle:Log l
    //                                                    WHERE l.ip LIKE ?1 OR l.actividad LIKE ?1 OR l.observacion LIKE ?1")
    //                                    ->setParameter(1,'%'.$request->get('search')['value'].'%')
    //                                    ->getSingleScalarResult(),
    //                    "stats" =>  $this->getEntityManager()
    //                                    ->createQuery(" SELECT l.actividad,COUNT(l)
    //                                                    FROM SeguridadBundle:Log l
    //                                                    GROUP BY l.actividad")
    //                                    ->getScalarResult(),                                        
    //                    "logs" =>   $this->getEntityManager()
    //                                    ->createQuery(" SELECT l
    //                                                    FROM SeguridadBundle:Log l
    //                                                    WHERE l.ip LIKE ?1 OR l.actividad LIKE ?1 OR l.observacion LIKE ?1
    //                                                    ORDER BY "."l.".$columns[$request->get('order')[0]['column']]." ".$request->get('order')[0]['dir'])
    //                                    ->setParameter(1,'%'.$request->get('search')['value'].'%')
    //                                    ->setMaxResults($request->get('length'))
    //                                    ->setFirstResult($request->get('start'))
    //                                    ->getResult()
    //        );
    //}
    //
    ////public function countUserLogs($user)
    ////{
    ////    return $this->getEntityManager()
    ////                ->createQuery(" SELECT COUNT(l)
    ////                                FROM SeguridadBundle:Log l
    ////                                WHERE l.usuario = ?1")
    ////                ->setParameter(1,$user->getId())
    ////                ->getResult();        
    ////}
    //
    //public function findByLetters($string){
    //    return $this->getEntityManager()->createQuery('SELECT DISTINCT u FROM SeguridadBundle:Usuario u  
    //            WHERE u.usuario LIKE :string OR u.apellido LIKE :string or u.nombre LIKE :string')
    //            ->setParameter('string','%'.$string.'%')
    //            ->getArrayResult();
    //}
}