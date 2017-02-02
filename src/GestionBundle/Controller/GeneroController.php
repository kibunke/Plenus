<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Genero;
use GestionBundle\Form\GeneroType;
/**
 * Genero controller.
 *
 * @Route("/gestion/genero")
 * @Security("has_role('ROLE_ADMIN')")
 */
class GeneroController extends Controller
{
    /**
     * Lists all Genero entities.
     *
     * @Route("/genero", name="genero")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('dimension');
    }
    
    /**
     * @Route("/list/datatable", name="genero_list_datatable", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function listDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('ResultadoBundle:Genero')->datatable($request->request);
        
        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );
        
        foreach ($filter['rows'] as $genero){
            $data['data'][] = array(
                "genero"  => array(
                                    "id" => $genero->getId(),
                                    "nombre" => $genero->getNombre(),
                                    "descripcion"  => $genero->getDescripcion(),
                                    "eventos"   => count($genero->getEventos())
                            ),
                "actions"   => $this->renderView('GestionBundle:Genero:actions.html.twig', array('entity' => $genero)),
            );
        }
        return new JsonResponse($data);
    }    
    
    /**
     * Creates a new Genero entity.
     *
     * @Route("/new", name="genero_new", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Genero:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Genero();
        $form = $this->createForm(GeneroType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setCreatedBy($this->getUser());
            try{
                $em->persist($entity);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se agregó el género.'));
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
     * Finds and displays a Genero entity.
     *
     * @Route("/{id}", name="genero_show", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Genero $id)
    {
    }

    /**
     * Displays a form to edit an existing Genero entity.
     *
     * @Route("/{id}/edit", name="genero_edit", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Genero:edit.html.twig")
     */
    public function editAction(Request $request, Genero $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(GeneroType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());       
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'El género fue modificado.'));
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
