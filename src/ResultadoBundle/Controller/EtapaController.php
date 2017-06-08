<?php

namespace ResultadoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Etapa;
use ResultadoBundle\Entity\Evento;
use ResultadoBundle\Entity\EtapaClasificacion;
use ResultadoBundle\Entity\EtapaFinal;
use ResultadoBundle\Entity\EtapaMedallero;

/**
 * Etapa controller.
 *
 * @Route("/etapa")
 * @Security("has_role('ROLE_RESULTADO_ETAPA')")
 */
class EtapaController extends Controller
{
    /**
     * Creates a new EtapaClasificacion + EtapaFInal entities by evento.
     *
     * @Route("/{id}/clasificacion/new", name="resultado_etapaClasificacion_create")
     * @Method("POST")
     * @Security("has_role('ROLE_RESULTADO_ETAPA_NEW')")
     * @Template()
     */
    public function createClasificacionAction(Request $request, Evento $evento)
    {

        $form =  $this->createEtapaClasificacionForm($evento);
        $form->handleRequest($request);

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($evento)){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE EL EVENTO NO TENGA ETAPAS CREADAS */
        if(count($evento->getEtapas())){
            $this->addFlash('primary', 'Este evento ya posee etapas de competencia. Antes de crear nuevas, elimine las actuales');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        if ($form->isValid()) {
            $clasificacion = new EtapaClasificacion($this->getUser());
            $clasificacion->setEvento($evento);
            $final = new EtapaFinal($this->getUser());
            $final->setEvento($evento);
            $medallero = new EtapaMedallero($this->getUser());
            $medallero->setEvento($evento);

            $em = $this->getDoctrine()->getManager();

            $em->persist($clasificacion);
            $em->persist($final);
            $em->persist($medallero);
            try{
                $em->flush();
                $this->addFlash('exito', 'Las etapa de clasificación + final se crearon correctamente.');
                return $this->redirect($this->getRequest()->headers->get('referer'));
            } catch (Exception $e) {
                $this->addFlash('error', 'Las etapa de clasificación + final NO pudieron crearse.');
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new EtapaFInal entity by evento.
     *
     * @Route("/{id}/final/new", name="resultado_etapaFinal_create")
     * @Method("POST")
     * @Security("has_role('ROLE_RESULTADO_ETAPA_NEW')")
     * @Template()
     */
    public function createFinalAction(Request $request, Evento $evento)
    {
        $form =  $this->createEtapaFinalForm($evento);
        $form->handleRequest($request);

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($evento)){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE EL EVENTO NO TENGA ETAPAS CREADAS */
        if(count($evento->getEtapas())){
            $this->addFlash('primary', 'Este evento ya posee etapas de competencia. Antes de crear nuevas, elimine las actuales');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        if ($form->isValid()) {
            $final = new EtapaFinal($this->getUser());
            $final->setEvento($evento);
            $medallero = new EtapaMedallero($this->getUser());
            $medallero->setEvento($evento);
            $em = $this->getDoctrine()->getManager();

            $em->persist($final);
            $em->persist($medallero);
            try{
                $em->flush();
                $this->addFlash('exito', 'Las etapa de clasificación + final se crearon correctamente.');
                return $this->redirect($this->getRequest()->headers->get('referer'));
            } catch (Exception $e) {
                $this->addFlash('error', 'Las etapa de clasificación + final NO pudieron crearse.');
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a EtapaClasificacion entity.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEtapaClasificacionForm(Evento $evento)
    {
        return $this->createFormBuilder(array())
                            ->setAction($this->generateUrl('resultado_etapaClasificacion_create', array('id' => $evento->getId())))
                            ->setMethod('POST')
                            ->getForm();
        ;
    }

    /**
     * Creates a form to create a EtapaFinal entity.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEtapaFinalForm(Evento $evento)
    {
        return $this->createFormBuilder(array())
                            ->setAction($this->generateUrl('resultado_etapaFinal_create', array('id' => $evento->getId())))
                            ->setMethod('POST')
                            ->getForm();
        ;
    }

    /**
     * Displays a view to create a new Etapa entity.
     *
     * @Route("/{id}/new", name="resultado_etapa_new")
     * @Method("GET")
     * @Security("has_role('ROLE_RESULTADO_ETAPA_NEW')")
     * @Template()
     */
    public function newAction(Request $request, Evento $evento)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        $formFinal =  $this->createEtapaFinalForm($evento);
        $formClasificacion =  $this->createEtapaClasificacionForm($evento);

        return array(
            'evento' => $evento,
            'formEtapaFinal'=>$formFinal->createView(),
            'formEtapaClasificacion'=>$formClasificacion->createView(),
        );
    }

    /**
     * Creates a form to delete a Etapa entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Etapa $entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('resultado_etapa_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar'))
            ->getForm()
        ;
    }

    /**
     * Delete a Etapa entity.
     *
     * @Route("/{id}/remove", name="resultado_etapa_reset")
     * @Method("GET")
     * @Security("has_role('ROLE_COMPETENCIA_DELETE')")
     * @Template("ResultadoBundle:Etapa:delete.html.twig")
     */
    public function resetAction(Request $request,Etapa $etapa)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        $form =  $this->createDeleteForm($etapa);

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Deletes a Etapa entity.
     *
     * @Route("/{id}/delete", name="resultado_etapa_delete")
     * @Security("has_role('ROLE_COMPETENCIA_DELETE')")
     * @Method("DELETE")
     */
    public function resetFlushAction(Request $request, Etapa $etapa)
    {
        $form = $this->createDeleteForm($etapa);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$etapa) {
                throw $this->createNotFoundException('No existe la etapa.');
            }

            $em->remove($etapa);
            try{
                $em->flush();
                $this->addFlash('exito', 'La etapa fue reseteada con exito ');
            } catch (Exception $e) {
                $this->addFlash('error', 'La etapa no pudo ser reseteada.');
            }
        }
        return new JsonResponse(array('success' => true, 'reload' =>true));
    }

    /**
     * Get state etapa.
     *
     * @Route("/{id}/stats/avance", name="resultado_etapa_stats_avance")
     * @Method("GET")
     */
    public function statsAvanceAction(Etapa $etapa)
    {
        return new JsonResponse($etapa->getStateBadgeRaw());
    }


    /**
     * Get Etapas Disponibles.
     *
     * @Route("/etapas/disponibles", name="resultado_etapa_disponibles")
     * @Method("GET")
     */
    public function etapasDisponiblesAction(Request $request)
    {
        return new JsonResponse(Etapa::getEtapasDisponiblesAsArray());
    }

}
