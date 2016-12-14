<?php

namespace ResultadoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Evento;
use ResultadoBundle\Entity\EtapaMedallero;
use GestionBundle\Form\EventoType;
/**
 * Evento controller.
 *
 * @Route("/resultados")
 * @Security("has_role('ROLE_EVENTO')")
 */
class EventoController extends Controller
{
    /**
     * Edit Evento entity.
     *
     * @Route("/{id}/evento", name="resultado_evento_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Evento $evento)
    {
        return array(
            'evento' => $evento
        );
    }
    
    /**
     * Creates a form to delete a Etapa entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Evento $entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('resultado_evento_etapas_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar'))
            ->getForm()
        ;
    }
    
    /**
     * Delete a Etapa entity.
     *
     * @Route("/{id}/remove", name="resultado_evento_etapas_remove")
     * @Method("GET")
     * @Security("has_role('ROLE_ETAPA_DELETE')")
     * @Template()
     */
    public function deleteAction(Request $request,Evento $evento)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        
        $form =  $this->createDeleteForm($evento);                    
        
        return array(
            'form' => $form->createView(),
        );
    }
    
    /**
     * Deletes a Etapa entity.
     *
     * @Route("/{id}/delete", name="resultado_evento_etapas_delete")
     * @Security("has_role('ROLE_ETAPA_DELETE')")
     * @Method("DELETE")
     */
    public function deleteFlushAction(Request $request, Evento $evento)
    {
        $form = $this->createDeleteForm($evento);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$evento) {
                throw $this->createNotFoundException('Unable to find Inscripto entity.');
            }
            foreach ($evento->getEtapas() as $etapa)
            {
                $em->remove($etapa);
            }
            try{
                $em->flush();
                $this->addFlash('exito', 'Las etapas del evento fueron eliminadas con exito ');
            } catch (\Exception $e) {
                $this->addFlash('error', 'La etapas del evento no pudieron ser eliminadas.');
            }
        }
        return $this->redirect($this->generateUrl('resultados'));
    }
    
    /**
     * Edit Evento entity.
     *
     * @Route("/{id}/evento/stats/ganadores", name="resultado_evento_stats_ganadores")
     * @Method("GET")
     */
    public function statsGanadoresAction(Evento $evento)
    {
        return new JsonResponse($evento->getStatsEquipos());
    }
    
    /**
     * Edit Evento entity.
     *
     * @Route("/{id}/evento/stats/plazas", name="resultado_evento_stats_plazas")
     * @Method("GET")
     */
    public function statsPlazasAction(Evento $evento)
    {
        return new JsonResponse($evento->getStatsPlazas());
    }
    
    /**
     * Edit Evento entity.
     *
     * @Route("/{id}/evento/stats/avance", name="resultado_evento_stats_avance")
     * @Method("GET")
     */
    public function statsAvanceAction(Evento $evento)
    {
        return new JsonResponse($evento->getState());
    }        
}
