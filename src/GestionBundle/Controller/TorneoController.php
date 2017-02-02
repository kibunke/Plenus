<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Torneo;
use GestionBundle\Form\TorneoType;

/**
 * Torneo controller.
 *
 * @Route("/gestion/torneo")
 * @Security("has_role('ROLE_ADMIN')")
 */
class TorneoController extends Controller
{
    /**
     * Lists all Torneo entities.
     *
     * @Route("/", name="torneo")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('dimension');
    }
    
    /**
     * @Route("/list/datatable", name="torneo_list_datatable", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function listDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('ResultadoBundle:Torneo')->datatable($request->request);
        
        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );
        
        foreach ($filter['rows'] as $torneo){
            $data['data'][] = array(
                "torneo"  => array(
                                    "id" => $torneo->getId(),
                                    "nombre" => $torneo->getNombre(),
                                    "descripcion"  => $torneo->getDescripcion(),
                                    "eventos"   => count($torneo->getEventos())
                            ),
                "actions"   => $this->renderView('GestionBundle:Torneo:actions.html.twig', array('entity' => $torneo)),
            );
        }
        return new JsonResponse($data);
    }    
    
    /**
     * Creates a new Torneo entity.
     *
     * @Route("/new", name="torneo_new", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Torneo:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Torneo();
        //$form = $this->createForm(TorneoType::class, $entity, array('action' => $this->generateUrl('torneo_new')));
        $form = $this->createForm(TorneoType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setCreatedBy($this->getUser());
            try{
                $em->persist($entity);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se agregó un torneo.'));
            } catch (Exception $e) {
                $error = $e->getMessage();
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos.', 'debug' => $error));
            }
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    } 
    
    /**
     * Finds and displays a Torneo entity.
     *
     * @Route("/{id}", name="torneo_show", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Torneo $id)
    {
    }

    /**
     * Displays a form to edit an existing Torneo entity.
     *
     * @Route("/{id}/edit", name="torneo_edit", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Torneo:edit.html.twig")
     */
    public function editAction(Request $request, Torneo $entity)
    {
        $em = $this->getDoctrine()->getManager();
        //$form = $this->createForm(TorneoType::class, $entity, array('action' => $this->generateUrl('torneo_edit', array('id'=> $entity->getId()))));
        $form = $this->createForm(TorneoType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());       
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'El torneo fue modificado.'));
            } catch (Exception $e) {
                $error = $e->getMessage();
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos.', 'debug' => $error));
            }
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
}
