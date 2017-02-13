<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Evento;
use GestionBundle\Form\EventoType;
use InscripcionBundle\Entity\Segmento;
/**
 * Evento controller.
 *
 * @Route("/gestion/evento")
 * @Security("has_role('ROLE_ADMIN')")
 */
class EventoController extends Controller
{
    /**
     * Lists all Evento entities.
     *
     * @Route("/", name="evento")
     * @Method("GET")
     * @Template("GestionBundle:Evento:index.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        return array();
    }
    
    /**
     * @Route("/list/datatable", name="evento_list_datatable", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function listDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('ResultadoBundle:Evento')->datatable($request->request);
        
        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );
        
        foreach ($filter['rows'] as $evento){
            $data['data'][] = array(
                "evento"  => array(
                                    "id" => $evento->getId(),
                                    "nombre" => $evento->getNombreCompletoRaw(),
                                    "orden" => $evento->getOrden(),
                                    "descripcion"  => $evento->getDescripcion()
                            ),
                "actions"   => $this->renderView('GestionBundle:Evento:actions.html.twig', array('entity' => $evento)),
            );
        }
        return new JsonResponse($data);
    }    
    
    /**
     * Creates a new Evento entity.
     *
     * @Route("/new", name="evento_new", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Evento:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Evento();
        $form = $this->createForm(EventoType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setCreatedBy($this->getUser());
            try{
                $em->persist($entity);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se agregó el evento.'));
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
     * Creates a new Evento entity.
     *
     * @Route("/new/from/segmento/{id}", name="evento_new_from_segmento", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Evento:new.html.twig")
     */
    public function newFromSegmentoAction(Request $request, Segmento $segmento)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Evento();
        $form = $this->createForm(EventoType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setCreatedBy($this->getUser());
            try{
                $em->persist($entity);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se agregó el evento.'));
            } catch (Exception $e) {
                $error = $e->getMessage();
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos.', 'debug' => $error));
            }
        }elseif(!$form->isSubmitted()){
            $entity->setDimensionesFromSegmento($segmento);
            $form = $this->createForm(EventoType::class, $entity);
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }    
    /**
     * Finds and displays a Evento entity.
     *
     * @Route("/{id}", name="evento_show", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Evento $id)
    {
    }

    /**
     * Displays a form to edit an existing Evento entity.
     *
     * @Route("/{id}/edit", name="evento_edit", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Evento:edit.html.twig")
     */
    public function editAction(Request $request, Evento $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(EventoType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());       
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'El evento fue modificado.'));
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
