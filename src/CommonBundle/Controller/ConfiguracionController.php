<?php

namespace CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use GestionBundle\Entity\ConfiguracionGlobal;
use GestionBundle\Form\ConfiguracionGlobalType;

/**
 * @Route("/system/configuracionglobal")
 * @Security("has_role('ROLE_ADMIN')")
 */
class ConfiguracionController extends Controller
{    
    /**
     * @Route("/", name="conf_global_index")
     * @Template("CommonBundle:Configuracion:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('GestionBundle:ConfiguracionGlobal')->findOneById(1);
        return array('entity'=> $entity);
    }
    
    /**
     * @Route("/{conf}/edit", name="conf_global_edit")
     * @Template("CommonBundle::generic.form.html.twig")
     */
    public function editAction(Request $request, ConfiguracionGlobal $conf)
    {
        $form  = $this->createCreateForm($conf);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            $em  = $this->getDoctrine()->getManager();
            $conf->setUpdatedBy($this->getUser());
  
            try{
                $em->flush();
                return new JsonResponse(array('resultado' => 0, 'mensaje' => 'Configuración modificada con éxito'));
            }
            catch(\Exception $e ){
                 return new JsonResponse(array('resultado' => 1, 'mensaje' => 'Error al modificar la Configuración Global'));
            }
        }
        return array(
            'entity' => $conf,
            'form'   => $form->createView(),
        );
    }
    
        /**
     * Creates a form to create a Email entity.
     *
     * @param Role $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ConfiguracionGlobal $entity)
    {
        $form = $this->createForm(ConfiguracionGlobalType::class, $entity);
    
        return $form;
    }
}