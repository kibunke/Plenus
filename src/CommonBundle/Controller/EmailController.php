<?php

namespace CommonBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use SeguridadBundle\Entity\Usuario;
use SeguridadBundle\Entity\Email;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use SeguridadBundle\Form\EmailType;

/**
 * Email controller.
 *
 * @Route("/email")
 * @Security("has_role('ROLE_EMAIL_LIST')")
 */
class EmailController extends Controller
{
    /**
     * @Route("/list", name="_emails_list")
     * @Template()
     */
    public function listAction()
    {
        //$user = $this->getUser();
        //$error = "";
        //if ($user){
        //    $emails = [];
        //    foreach ($user->getEmails() as $email){
        //        $emails[] = array(
        //                    "id" => $email->getId(),
        //                    "casilla" => $email->getCasilla(),
        //                    "activo" => $email->getActivo(),
        //                );
        //    }
        //    return new JsonResponse(array("hasError" => false, "msj" => "", "emails" => $emails));
        //}else{
        //    $error = "Error al cargar el usuario";
        //}
        return new JsonResponse(array("hasError" => true, "msj" => $error, "emails" => array()));
    }    

    /**
     * @Route("/createOLD", name="email_createOLD")
     * @Security("has_role('ROLE_EMAIL_CREATE')")
     * @Method("POST")
     */
    public function createEmailAction(Request $request){
        //$arrayFinal = array('result'=>0, 'message'=>'0');
        //if ($request->isXmlHttpRequest()) {  
        //    $em = $this->getDoctrine()->getManager();
        //    $emails = $this->getUser()->getEmails()->toArray();
        //    if (sizeof($emails) == $this->container->getParameter('emails_permitidos'))
        //    {
        //        $log = new Log($this->getUser(),$request->getClientIp(),"maxEmails");
        //        $em->persist($log);
        //        $em->flush(); 
        //        
        //        $arrayFinal['result'] = 0;
        //        $arrayFinal['message'] = 'No pueden definirse mas de ' . $this->container->getParameter('emails_permitidos') . ' correos electrónicos';
        //        return new Response(json_encode($arrayFinal));
        //    }
        //    $entity = new Email();
        //    $form  = $this->createForm(new EmailType(), $entity);
        //    $form->bind($request);
        //    if ($this->getUser()->existeEmail($entity->getCasilla()))
        //    {
        //        $log = new Log($this->getUser(),$request->getClientIp(),"existeEmail");
        //        $log->setObservacion($entity->getCasilla());
        //        $em->persist($log);
        //        $em->flush();
        //        $arrayFinal['result'] = 0;
        //        $arrayFinal['message'] = 'El correo electrónico ingresado ya se encuentra declarado';
        //        return new Response(json_encode($arrayFinal));
        //    } 
        //    if($form->isValid()) 
        //    {
        //        $entity->setActivo(true);
        //        $usuario = $em->getRepository('SeguridadBundle:Usuario')->find($this->getUser());
        //        $entity->setUsuario($usuario);
        //        $log = new Log($this->getUser(),$request->getClientIp(),"emailGuardado");
        //        $log->setObservacion($entity->getCasilla());
        //        $em->persist($log);
        //        $em->persist($entity);
        //        try {
        //            $em->flush();
        //            $arrayFinal['result'] = 1;
        //            $this->addFlash('success', 'Se ha guardado correctamente el nuevo e-mail');
        //            return new Response(json_encode($arrayFinal));
        //        }
        //        catch( \PDOException $e )
        //        {
        //            if( $e->getCode() === '23000' )
        //            {
        //                $arrayFinal['result'] = 0;
        //                $arrayFinal['message'] = 'No se ha podido guardar el nuevo e-mail';
        //                return new Response(json_encode($arrayFinal));
        //            }
        //            else throw $e;
        //        }
        //    }
        //    else
        //    {
        //        $log = new Log($this->getUser(),$request->getClientIp(),"wrongEmail");
        //        $em->persist($log);
        //        $em->flush();
        //        $arrayFinal['result'] = 0;
        //        $arrayFinal['message'] = 'Verifique los datos ingresados';
        //        return new Response(json_encode($arrayFinal));
        //
        //    }
        //}
        //return new Response(json_encode($arrayFinal));
    }
    
    /**
     * Creates a new Email entity.
     *
     * @Route("/create", name="email_create", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_EMAIL_CREATE')")
     * @Template("SeguridadBundle:Email:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $user = $this->getUser();
        $entity = new Email();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        
        $em = $this->getDoctrine()->getManager();
        $emails = $user->getEmails()->toArray();
        if (sizeof($emails) == $this->container->getParameter('emails_permitidos'))
        {
            $log = new Log($this->getUser(),$request->getClientIp(),"maxEmails");
            $log->setObservacion($entity->getCasilla());
            $em->persist($log);
            $em->flush(); 
            return new JsonResponse(array('success' => false, 'msj' => 'No pueden definirse mas de ' . $this->container->getParameter('emails_permitidos') . ' correos electrónicos'));
        }
        //Chequea que no exista el mail en la misma persona.
        if ($entity->exist($emails)) {
            return new JsonResponse(array('success' => false, 'msj' => 'Ya tiene activo ese Email.<br>'.$entity->getCasilla()));
        }
        if ($form->isValid())
        {
            $entity->setActivo(true);
            $entity->setUsuario($user);
            $log = new Log($this->getUser(),$request->getClientIp(),"emailGuardado");
            $log->setObservacion($entity->getCasilla());
            $em->persist($log);
            $em->persist($entity);
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue guardada correctamente', 'data' => array("id" => $entity->getId(), "casilla" => $entity->getCasilla())));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.', 'debug'=> $e->getMessage()));
            }
        }
        return new JsonResponse(array('success' => false, 'msj' => 'Imposible guardar el Email. La información no es válida.'));
    }
    
    /**
     * Creates a form to create a Email entity.
     *
     * @param Email $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Email $entity)
    {
        $form = $this->createForm(new EmailType(), $entity, array(
            'action' => $this->generateUrl('email_create'),
            'method' => 'POST'
        ));
    
        //$form->add('submit', 'submit', array('label' => 'Guardar'));
    
        return $form;
    }
    
    /**
     * Displays a form to create a new Email entity.
     *
     * @Route("/new", name="email_new", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     * @Security("has_role('ROLE_EMAIL_NEW')")
     * @Template("SeguridadBundle:Email:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Email();
        
        $emails = $this->getUser()->getEmails()->toArray();
        if (sizeof($emails) == $this->container->getParameter('emails_permitidos'))
        {
            return new JsonResponse(array('success' => false, 'msj' => 'No pueden definirse mas de ' . $this->container->getParameter('emails_permitidos') . ' correos electrónicos'));
        }
        
        $form   = $this->createCreateForm($entity);
    
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Deletes a Email entity.
     *
     * @Route("/{id}/remove", name="email_remove", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_EMAIL_REMOVE')")
     * @Method("DELETE")
     */
    public function removeAction(Request $request, Email $entity)
    {
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $emails = $this->getUser()->getEmails()->toArray();
            
            $em = $this->getDoctrine()->getManager();
            //Chequea que exista el mail y que sea de la persona.
            if (!$entity || !in_array($entity, $emails, true)) {
                return new JsonResponse(array('success' => false, 'msj' => 'No existe el Email.'));
            }
            //Vuelve a chequear que quede 1 mail por lo menos.
            if (sizeof($emails) < 2){
                return new JsonResponse(array('success' => false, 'msj' => 'Imposible eliminar el Email. Debe tener por lo menos un correo electrónico declarado.'));
            }
            $log = new Log($this->getUser(),$request->getClientIp(),"deleteEmail");
            $log->setObservacion($entity->getCasilla());
            $em->persist($log);            
            $em->remove($entity);
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue eliminada correctamente'));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser eliminada. Intentelo en unos instantes.'));
            }
        }
        
        return new JsonResponse(array('success' => false, 'msj' => 'Imposible eliminar el Email. La información no es válida.'));
    }
    
    /**
     * Creates a form to delete a Email entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Email $entity)
    {
        return $this->createFormBuilder(null, array(
                'action' => $this->generateUrl('email_remove', array('id' => $entity->getId())),
                'method' => 'DELETE',
                )
            )
            ->getForm()
        ;
    }
    
    /**
     * Delete a Email entity.
     *
     * @Route("/{id}/delete", name="email_delete", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_EMAIL_DELETE')")
     * @Method("GET")
     * @Template()
     */
    public function deleteAction(Request $request, Email $entity = NULL)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            return new JsonResponse(array('success' => false, 'msj' => 'No existe el Email.'));
        }
        $emails = $this->getUser()->getEmails()->toArray();
        if (sizeof($emails) < 2){
            return new JsonResponse(array('success' => false, 'msj' => 'Imposible eliminar el Email. Debe tener por lo menos un correo electrónico declarado.'));
        }
        //Chequea que exista el mail y que sea de la persona.
        if (!$entity || !in_array($entity, $emails, true)) {
            return new JsonResponse(array('success' => false, 'msj' => 'No existe el Email.'));
        }    
        $form = $this->createDeleteForm($entity);
        
        return array(
                'entity' => $entity,
                'form' => $form->createView()  
            );
    }
}
