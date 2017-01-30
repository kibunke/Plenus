<?php

namespace InscripcionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use InscripcionBundle\Entity\Segmento;
use InscripcionBundle\Form\SegmentoType;

/**
 * Segmento controller.
 *
 * @Route("/segmento")
 * @Security("has_role('ROLE_INSCRIPCION') and has_role('ROLE_SEGMENTO')")
 */
class SegmentoController extends Controller
{
    /**
     * Lists all Segmento entities.
     *
     * @Route("/list", name="segmento_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction()
    {
        return array();        
    }
    
    /**
     * @Route("/list/datatable", name="segmento_list_datatable")
     * @Method("POST")
     */
    public function listDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('InscripcionBundle:Segmento')->datatable($request->request);

        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );
        
        foreach ($filter['rows'] as $segmento){
            $data['data'][] = array(
                "id"        => $segmento->getId(),
                "segmento"  => $segmento->getNombre(),
                "eventos"   => count($segmento->getEventos()),
                "inscriptos"=> 0,//$user->getUsername(),
                "actions"   => $this->renderView('IncripcionBundle:Segmento:actions.html.twig', array('entity' => $segmento)),
            );
        }
        return new JsonResponse($data);
    }
    
    /**
     * @Route("/new", name="segmento_new")
     * @Method({"GET", "POST"})
     * @Template("InscripcionBundle:Segmento:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $segmento = new Segmento();
        $form = $this->createForm(SegmentoType::class, $segmento);
        //$form = $this->createNewAccountForm($user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {            
            try {
                //$em->persist($segmento);
                //$em->persist($log);
                //$em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se creo el Segmento'));
            }
            catch(\Exception $e ){
                return new JsonResponse(array('success' => false, 'message' => 'Ocurrio un error al intentar guardar los datos!', 'debug' => $e->getMessage()));
            }
        }
        return array(
                'form' => $form->createView(),
            );
    }
}