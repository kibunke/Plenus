<?php
namespace TesoreriaBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CategoriaPagoRepository
 *
 * @author kibunke
 */
class CategoriaPagoRepository extends EntityRepository
{
    /**
     * Retorna el nombre de una area cuyo id se proporciona como parámetro
     */
    public function getAllOrdered() {
        return $this->createQueryBuilder('categoriaPago')
                        ->orderBy('categoriaPago.nombre', 'ASC')
                        ->getQuery()->getResult();
    }
}