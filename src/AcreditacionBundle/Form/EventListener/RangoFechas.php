<?php

namespace AcreditacionBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RangoFechas implements EventSubscriberInterface {

    public static function getSubscribedEvents() {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        );
    }

    public function onPreSetData(FormEvent $event) {
        $datosOpertivos = $event->getData();
        $form = $event->getForm();
        /*$rango = '';
        //Setea el rando de trabajo
        if (($datosOpertivos != null) and ($datosOpertivos->getFechaIniTrabajo()) and ($datosOpertivos->getFechaFinTrabajo())) {
            $rango .= $datosOpertivos->getFechaIniTrabajo()->format("d/m/Y") . ' - ' . $datosOpertivos->getFechaFinTrabajo()->format("d/m/Y");
        } else {
            //$rango .=  date('d/m/Y').' - '.date('d/m/Y') ;
        }
        $form->add('rangoTrabajo', 'text', array(
            'mapped' => false,
            'data' => $rango,
            'label' => 'Rango',
        ));*/
        $rango2 = '';
        //Setea el rango de hospedaje
        if (($datosOpertivos != null) and ( $datosOpertivos->getNecesitaHospedaje()) and ( $datosOpertivos->getFechaIngresoHospedaje() != '')) {
            $rango2 .= $datosOpertivos->getFechaIngresoHospedaje()->format("d/m/Y") . ' - ' . $datosOpertivos->getFechaEgresoHospedaje()->format("d/m/Y");
        } else {
            
        }
        $form->add('rangoHospedaje', 'text', array(
            'mapped' => false,
            'label' => 'Checkin / Checkout',
            'required' => false,
            'data' => $rango2,
        ));
        $rango3 = '';
        //Setea el rando de transporte
        if (($datosOpertivos != null) and ( $datosOpertivos->getNecesitaTransporte()) and ( $datosOpertivos->getFechaIdaTransporte() != '')) {
            $rango3 .= $datosOpertivos->getFechaIdaTransporte()->format("d/m/Y") . ' - ' . $datosOpertivos->getFechaRegresoTransporte()->format("d/m/Y");
        } else {
            
        }
        $form->add('rangoTransporte', 'text', array(
            'mapped' => false,
            'label' => 'Ida / Vuelta',
            'required' => false,
            'data' => $rango3,
        ));
    }

    public function onPreSubmit(FormEvent $event) {
        $datosOpertivos = $event->getData();
        //$datosOpertivos['fechaIniTrabajo'] = substr($datosOpertivos['rangoTrabajo'], 0, 10);
        //$datosOpertivos['fechaFinTrabajo'] = substr($datosOpertivos['rangoTrabajo'], 13, 10);
        if (isset($datosOpertivos['necesitaHospedaje']) && ($datosOpertivos['necesitaHospedaje'])) {
            $datosOpertivos['fechaIngresoHospedaje'] = substr($datosOpertivos['rangoHospedaje'], 0, 10);
            $datosOpertivos['fechaEgresoHospedaje'] = substr($datosOpertivos['rangoHospedaje'], 13, 10);
        }
        if (isset($datosOpertivos['necesitaTransporte']) && ($datosOpertivos['necesitaTransporte'])) {
            $datosOpertivos['fechaIdaTransporte'] = substr($datosOpertivos['rangoTransporte'], 0, 10);
            $datosOpertivos['fechaRegresoTransporte'] = substr($datosOpertivos['rangoTransporte'], 13, 10);
        }
        $event->setData($datosOpertivos);
    }

}
