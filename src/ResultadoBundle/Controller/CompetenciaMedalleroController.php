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
use ResultadoBundle\Entity\Partido;
use ResultadoBundle\Entity\Etapa;
use ResultadoBundle\Entity\Competencia;
use ResultadoBundle\Entity\CompetenciaMedallero;

/**
 * CompetenciaOrden controller.
 *
 * @Route("/resultados/competencia/medallero")
 * @Security("has_role('ROLE_COMPETENCIA')")
 */
class CompetenciaMedalleroController extends CompetenciaController
{
    /**
     * Finds and displays a table of Plazas.
     *
     * @Route("/{id}/reload", name="competenciaMedallero_reload")
     * @Method("GET")
     */
    public function competenciaMedalleroReloadAction(Request $request, Competencia $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Competencia entity.');
        }

        return $this->render(
            'ResultadoBundle:Competencia:Medallero/edit.html.twig',
            array('competencia' => $entity)
        );
    }
    
    /**
     * Creates a form to delete a Etapa entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Competencia $entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('competenciaMedallero_delete_flush', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar'))
            ->getForm()
        ;
    }
    
    /**
     * Delete a Etapa entity.
     *
     * @Route("/{id}/remove", name="competenciaMedallero_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_COMPETENCIA_DELETE')")
     * @Template("ResultadoBundle:Etapa:delete.html.twig")
     */
    public function resetAction(Request $request,Competencia $competencia)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        
        $form =  $this->createDeleteForm($competencia);                    
        
        return array(
            'form' => $form->createView(),
        );
    }
    
    /**
     * Deletes a Etapa entity.
     *
     * @Route("/{id}/delete", name="competenciaMedallero_delete_flush")
     * @Security("has_role('ROLE_COMPETENCIA_DELETE')")
     * @Method("DELETE")
     */
    public function resetFlushAction(Request $request, Competencia $competencia)
    {
        $form = $this->createDeleteForm($competencia);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            if (!$competencia) {
                throw $this->createNotFoundException('No existe la competencia.');
            }
            //$em->remove($competencia);
            foreach($competencia->getPlazas() as $item){
                $em->remove($item);
            }
            try{
                $em->flush();
                $em->remove($competencia);
                $em->flush();
                $this->addFlash('exito', 'La etapa fue reseteada con exito ');
            } catch (Exception $e) {
                $this->addFlash('error', 'La etapa no pudo ser reseteada.');
            }
        }
        return new JsonResponse(array('success' => true, 'reload' =>true));
    }
    
    /**
     * View Detalle medallero.
     *
     * @Route("/{id}/detalle", name="competenciaMedallero_detalle")
     * @Method("GET")
     * @Template("ResultadoBundle:Competencia/Medallero:detalle.html.twig")
     */
    public function detalleAction(Request $request, Partido $municipio)
    {
        $em = $this->getDoctrine()->getManager();
        $plazas = $em->getRepository('ResultadoBundle:PlazaMedallero')->getAllByMunicipio($municipio);        

        $plazasMedallero = $em->getRepository('ResultadoBundle:PlazaMedallero')->findAll();
        $listos = [];
        $municipios = [];
        $oro = []; $plata = []; $bronce = [];
        foreach($plazasMedallero as $plazaMedallero){
            if ($plazaMedallero->getEquipo()){
                $mun = $plazaMedallero->getEquipo()->getMunicipio();
                if (!in_array($mun->getId(), $listos)) {
                    $listos[]=$mun->getId();
                    $municipios[$mun->getId()]["region"] = "<small>".str_replace($mun->getRegionDeportiva(), "</small><strong>".$mun->getRegionDeportiva()."</strong><small>",  $mun->getCruceRegional())."</small>";
                    $municipios[$mun->getId()]["nombre"] = $mun->getNombre();
                    $municipios[$mun->getId()]["id"] = $mun->getId();
                    $municipios[$mun->getId()]["medallero"] = [0,0,0];
                    $oro[$mun->getId()] = 0;
                    $plata[$mun->getId()] = 0;
                    $bronce[$mun->getId()] = 0;
                }
                switch ($plazaMedallero->getOrden()){
                    case 1 : $oro[$mun->getId()] = $oro[$mun->getId()] + 1; break;
                    case 2 : $plata[$mun->getId()] = $plata[$mun->getId()] + 1; break;
                    case 3 : $bronce[$mun->getId()] = $bronce[$mun->getId()] + 1; break;
                }
                $municipios[$mun->getId()]["medallero"] = [$oro[$mun->getId()],$plata[$mun->getId()],$bronce[$mun->getId()]];
            }
        }

        $arr = array_multisort(
                    $oro, SORT_DESC, SORT_NUMERIC,
                    $plata, SORT_DESC, SORT_NUMERIC,
                    $bronce, SORT_DESC, SORT_NUMERIC,
                    $municipios
                );
        $pos = 0;
        $it = 0;
        foreach($municipios as $item){
            if ($item['id'] == $municipio->getId())
                $pos = $it;
            $it++;
        }
        
        return array('plazas' => $plazas, 'pos' => $pos, 'municipios' => $municipios);
    }    
}
