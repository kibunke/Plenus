<?php

namespace InscripcionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use InscripcionBundle\Entity\Inscripto;
use InscripcionBundle\Form\InscriptoType;
use ResultadoBundle\Entity\Evento;
use InscripcionBundle\Entity\Municipio;
use InscripcionBundle\Entity\Escuela;
use InscripcionBundle\Entity\Otro;
/**
 * Inscripto controller.
 *
 * @Route("/inscripcion/inscripto")
 * @Security("has_role('ROLE_INSCRIPCION')")
 */
class InscriptoController extends Controller
{
    /**
     * Lists all Inscripto entities.
     *
     * @Route("/{id}", name="inscripto")
     * @Method("GET")
     * @Security("has_role('ROLE_INSCRIPCION_LIST')")
     * @Template()
     */
    public function indexAction(Evento $evento)
    {

        $em = $this->getDoctrine()->getManager();
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($evento)){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return $this->redirectToRoute('homepage');
        }
        
        $inscriptos = $em->getRepository('InscripcionBundle:Inscripto')->findAllByEvento($evento);
        $instituciones = $em->getRepository('InscripcionBundle:Origen')->findAllOrderedByMunicipio();
        $arrInst=[];
        foreach($instituciones as $i){
            //asi si filtro tambien por tipo
            $arrInst[$i->getMunicipio()->getId()][$i->getClass()][]=$i->getNombre();
            //$arrInst[$i->getMunicipio()->getId()][]=$i->getNombre();
        }
        return array(
            'evento' => $evento,
            'inscriptos' => $inscriptos,
            'instituciones' => json_encode($arrInst)
        );        
    }
    
    /**
     * Creates a new Inscripto entity.
     *
     * @Route("/", name="inscripto_create")
     * @Method("POST")
     * @Security("has_role('ROLE_INSCRIPCION_NEW')")
     * @Template("InscripcionBundle:Inscripto:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Inscripto();
        //var_dump($request->request->get('juegosba_inscripcionbundle_inscripto')['origen']['origen']);die();

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        if (!$entity->getOrigen()->getId()){
            $entity->getOrigen()->setCreatedBy($this->getUser());
        }

        if ($form->isValid()) {
            $request->getSession()->set('ultMunicipio', $entity->getMunicipio()->getId());
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedBy($this->getUser());
            $em->persist($entity);
            try{
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente. Número de planilla : '.$entity->getId());
                return new JsonResponse(array('success' => true, 'id' => $entity->getId()));
            } catch (Exception $e) {
                $this->addFlash('error', 'La información no pudo ser guardada correctamente.');
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Inscripto entity.
     *
     * @param Inscripto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Inscripto $entity)
    {
        $form = $this->createForm(new InscriptoType(), $entity, array(
            'action' => $this->generateUrl('inscripto_create'),
            'method' => 'POST',
            'em' => $this->getDoctrine()->getManager(),
            'security' => $this->get('security.context'),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Inscripto entity.
     *
     * @Route("/{id}/new", name="inscripto_new", defaults={"id" = 0})
     * @Method("GET")
     * @Security("has_role('ROLE_INSCRIPCION_NEW')")
     * @Template()
     */
    public function newAction(Request $request, Evento $evento=null)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirectToRoute('inscripcion');
        }
        $em = $this->getDoctrine()->getManager();
        $entity = new Inscripto();
        
        $entity->setEvento($evento);
        if ($this->get('session')->get('ultMunicipio')){
            $mun=$em->getReference('CommonBundle:Partido',$this->get('session')->get('ultMunicipio'));
            $entity->setMunicipio($mun);
        }
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Inscripto entity.
     *
     * @Route("/{id}", name="inscripto_show")
     * @Method("GET")
     * @Security("has_role('ROLE_INSCRIPCION_SHOW')")
     * @Template()
     */
    public function showAction(Request $request, Inscripto $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirectToRoute('inscripcion');
        }
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inscripto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Inscripto entity.
     *
     * @Route("/{id}/edit", name="inscripto_edit")
     * @Method("GET")
     * @Security("has_role('ROLE_INSCRIPCION_EDIT')")
     * @Template("InscripcionBundle:Inscripto:new.html.twig")
     */
    public function editAction(Request $request, Inscripto $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirectToRoute('inscripcion');
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inscripto entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Inscripto entity.
    *
    * @param Inscripto $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Inscripto $entity)
    {
        $form = $this->createForm(new InscriptoType(), $entity, array(
            'action' => $this->generateUrl('inscripto_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'em' => $this->getDoctrine()->getManager(),
            'security' => $this->get('security.context'),
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Inscripto entity.
     *
     * @Route("/{id}", name="inscripto_update")
     * @Method("POST")
     * @Security("has_role('ROLE_INSCRIPCION_EDIT')")
     * @Template("InscripcionBundle:Inscripto:new.html.twig")
     */
    public function updateAction(Request $request, Inscripto $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inscripto entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if (!$entity->getOrigen()->getId()){
            $entity->getOrigen()->setCreatedBy($this->getUser());
        }
        
        if ($editForm->isValid()) {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());
            try{
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente. Número de planilla : '.$entity->getId());
                return new JsonResponse(array('success' => true, 'id' => $entity->getId()));
            } catch (Exception $e) {
                $this->addFlash('error', 'La información no pudo ser guardada correctamente.');
            }
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }
    
    /**
     * Deletes a Inscripto entity.
     *
     * @Route("/{id}/remove", name="inscripto_delete_flush")
     * @Security("has_role('ROLE_INSCRIPCION_DELETE')")
     * @Method("DELETE")
     */
    public function deleteFlushAction(Request $request, Inscripto $entity)
    {
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Inscripto entity.');
            }

            $em->remove($entity);
            try{
                $em->flush();
                $this->addFlash('exito', 'La planilla fue eliminada con exito ');
            } catch (Exception $e) {
                $this->addFlash('error', 'La planilla no pudo ser eliminada.');
            }
        }
        return $this->redirect($this->generateUrl('inscripto', array('id' => $entity->getEvento()->getId())));
    }

    /**
     * Creates a form to delete a Inscripto entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Inscripto $entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inscripto_delete_flush', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar'))
            ->getForm()
        ;
    }
    
    /**
     * Deletes a Actividad entity.
     *
     * @Route("/{id}/remove", name="inscripto_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_INSCRIPCION_DELETE')")
     * @Template("InscripcionBundle:Inscripto:delete.html.twig")
     */
    public function deleteAction(Request $request, Inscripto $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirectToRoute('inscripcion');
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('No existe la Entidad.');
        }
        $form = $this->createDeleteForm($entity);
        //return new JsonResponse(array('success' => true, 'id' => $entity->getId(),'form' => $form->createView()));
        return array(
                'entity' => $entity,
                'form' => $form->createView()  
            );
    }
}
