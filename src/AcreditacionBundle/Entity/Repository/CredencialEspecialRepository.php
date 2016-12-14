<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AcreditacionBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of CredencialEspecialRepository
 *
 * @author kibunke
 */
class CredencialEspecialRepository extends EntityRepository {

    /**
     * Retorna las credenciales de la BD de las cuales es responsable de Área el usuario que se recibe como parámetro
     */
    public function getCredenciales($idResponsable) {
        return $this->createQueryBuilder('credencial')
                        ->innerJoin('credencial.area', 'area')
                        ->innerJoin('area.usuariosResponsables', 'responsable')
                        ->where('responsable.id = ?1')
                        ->setParameter(1, $idResponsable)->getQuery()->getResult();
    }

    /**
     * Retorna Todas las credenciales de la BD
     */
    public function getAll() {
        return $this->createQueryBuilder('credencial')
                        ->innerJoin('credencial.area', 'area')
                        ->innerJoin('area.usuariosResponsables', 'responsable')
                        ->getQuery()->getResult();
    }

}
