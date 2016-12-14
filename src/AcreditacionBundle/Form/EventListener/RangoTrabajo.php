<?php

namespace AcreditacionBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RangoTrabajo implements EventSubscriberInterface {

    public static function getSubscribedEvents() {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        );
    }

    public function onPreSetData(FormEvent $event) {
        $entity = $event->getData();
        $form = $event->getForm();
        $rango = '';
        //Setea el rando de trabajo
        if (($entity != null) and ($entity->getFechaIniTrabajo()) and ($entity->getFechaFinTrabajo())) {
            $rango .= $entity->getFechaIniTrabajo()->format("d/m/Y") . ' - ' . $entity->getFechaFinTrabajo()->format("d/m/Y");
        } else {
            $rango .=  date('d/m/Y').' - '.date('d/m/Y') ;
        }
        $form->add('rangoFechaTrabajo', 'text', array(
            'mapped' => false,
            'data' => $rango,
            'label' => 'Dias de trabajo',
        ));
    }

    public function onPreSubmit(FormEvent $event) {
        $entity = $event->getData();
        $entity['fechaIniTrabajo'] = substr($entity['rangoFechaTrabajo'], 0, 10);
        $entity['fechaFinTrabajo'] = substr($entity['rangoFechaTrabajo'], 13, 10);
        $event->setData($entity);
    }

}