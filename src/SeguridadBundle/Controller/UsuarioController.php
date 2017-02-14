<?php

namespace SeguridadBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use SeguridadBundle\Form\UsuarioType;
use SeguridadBundle\Form\UsuarioAdminType;
use SeguridadBundle\Form\UsuarioEditType;
use SeguridadBundle\Form\UsuarioCheckDataType;
use SeguridadBundle\Entity\Usuario;

/**
 * Usuario controller.
 *
 * @Route("/system/user")
 */
class UsuarioController extends Controller
{
    /**
     * @Route("/check/userData", name="_checkUserData")
     * @Template()
     * @Security("has_role('ROLE_USER')")
     */
    public function checkUserDataAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UsuarioCheckDataType::class, $user);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            $em  = $this->getDoctrine()->getManager();
            $user->setCheckData(false)
                 ->setUpdatedBy($user)
                 ;
            
            try{
                $em->flush();
                $this->addFlash('success','Usuario completado con éxito');
                return $this->redirectToRoute('homepage');
            }
            catch(\Exception $e ){
                 $this->addFlash('error','Error al completar los datos de usuario');
            }
        }
        
        return array(
            'entity' => $user,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/list", name="user_list")
     * @Template()
     * @Security("has_role('ROLE_USER_LIST')")
     */
    public function listAction(Request $request)
    {
        return array();
    }
    
    /**
     * @Route("/list/datatable", name="user_list_datatable")
     * @Security("has_role('ROLE_USER_LIST') and has_role('ROLE_ORGANIZADOR')")
     */
    public function listDataTableAction(Request $request)
    {
        $user   = $this->getUser();
        $em     = $this->getDoctrine()->getManager();
        
        $filter = $em->getRepository('SeguridadBundle:Usuario')->datatable($request->request,$user,$this->get('security.authorization_checker'));
        $sessions = $em->getRepository('SeguridadBundle:Sessions')->findAll();

        $data=array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "sessions"        => count($sessions),
                    "data"            => array()
        );
        
        foreach ($filter['rows'] as $user){
            $persona = $user->getPersona();
            $data['data'][] = array(
                "id"      => $user->getId(),
                "ico"     => false,
                "user"    => $user->getUsername(),
                //"session" => $user->getUsername(),
                "persona" => array(
                                    "name"     => $persona->getApellido() . ', ' . $persona->getNombre(),
                                    "dni"      => $persona->getDni(),
                                    "email"    => $persona->getEmail(),
                                    "telefono" => $persona->getTelefono(),
                ),
                "perfil" => $user->getPerfil()->getName(),
                "active" => $user->getIsActive(),
                "info"   => array(
                                    "created" => $user->getCreatedAt(),
                                    "lastop"  => $user->getLastActivity()
                                 ),
                "pass"    => $user->getChangePassword(),
                "actions" => $this->renderView('SeguridadBundle:Usuario:actions.html.twig', array('entity' => $user)),
            );
        }
        return new JsonResponse($data);
    }
    
    /**
     * @Route("/change/password", name="_changePassword")
     * @Template()
     * @Security("has_role('ROLE_USER')")
     */
    public function changePasswordAction(Request $request)
    {
        $user = $this->getUser();
        $error = null;
        if($request->getMethod() == 'POST'){
            $em = $this->getDoctrine()->getManager();

            $encoder = $this->container->get('security.password_encoder');
            $current = $encoder->encodePassword($user, $request->get('currentPassword'));
            $passwordValid = $encoder->isPasswordValid($user, $request->get('currentPassword'));
            
            if (!$passwordValid){
                $error = 'La contraseña actual no es correcta.';   
            }
            
            if($request->get('newPassword') != $request->get('confirmNewPassword') && !$error){
                $error = 'La contraseña nueva no coincide con su verificación.';
            }
            
            $re = '/
                   # Match password with 6-15 chars with letters and digits
                      ^                 # Anchor to start of string.
                      (?=.*?[A-Z])      # Assert there is at least one mayus letter, AND
                      (?=.*?[a-z])      # Assert there is at least one minus letter, AND
                      (?=.*?[0-9])      # Assert there is at least one digit, AND
                      (?=.{6,15}\z)     # Assert the length is from 6 to 15 chars.
                      /x';
        
            if(!preg_match($re, $request->get('newPassword')) && !$error){
                $error = 'La nueva contraseña no cumple con los requisitos de seguridad.';
                $this->addFlash('warning', 'La contraseña debe poseer entre 6 y 15 caracteres, al menos una mayúscula, al menos una minuscula, al menos un número y sólo admite caracteres alfanuméricos.');
            }
            
            if (!$error){
                $new = $encoder->encodePassword($user, $request->get('newPassword'));
                $user->setPassword($new);
                $user->setChangePassword(false);
                //agrega al historial de claves la nueva
                $passHistory = $user->getPasswordHistory();
                $passHistory[] = array(
                            "fecha" => new \DateTime(),
                            "pass" => "",
                            "passHash" => $new,
                            "observacion" => "Generada por el usuario."
                        );
                $user->setPasswordHistory($passHistory);                
                try {
                    $em->flush();
                    $this->addFlash('success', 'Recuerde utilizar la nueva contraseña la próxima vez que inicie sesión.');
                    return $this->redirectToRoute('homepage');
                }
                catch(\Exception $e ){
                     $error = 'Ocurrio un error al persistir lo datos.';
                }
            }
            $this->addFlash('error', $error);
        }else{
            $this->addFlash('warning', 'La clave debe poseer entre 6 y 15 caracteres, al menos una mayúscula, al menos una minuscula, al menos un número y sólo admite caracteres alfanuméricos.');
        }
        return array(
            'user' => $user
        );
    }
    
    /**
     * @Route("/{user}/edit", name="user_edit", defaults={"user":"__00__"})
     * @Security("has_role('ROLE_USER_EDIT')")
     * @Template("SeguridadBundle:Usuario:edit.html.twig")
     */
    public function editAction(Request $request,Usuario $user)
    {
        if(!$this->validarEdicionUsuario($user))
        {
            $this->addFlash('error', "El usuario no puede ser modificado");
            return $this->redirectToRoute('user_list');
        }
        
        $form = $this->createForm(UsuarioAdminType::class, $user,
                                  array(
                                        'action' => $this->generateUrl('user_edit', array('user' => $user->getId())),
                                        'method' => 'POST'
                                       )
                                 );
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            $em  = $this->getDoctrine()->getManager();
            
            if($request->get('password'))
            {
                if($request->get('password') != $request->get('password_confirm'))
                {
                    $this->addFlash('error', 'No coinciden las contraseñas');
                    return array('entity' => $user,'form'   => $form->createView());
                }
                
                $encoder = $this->container->get('security.password_encoder');
                $nuevaPass = $encoder->encodePassword($user, $request->get('password'));
                
                $user->setPassword($nuevaPass);
                $user->setChangePassword(true);
                $passHistory[] = array(
                                         "fecha"       => new \DateTime(),
                                         "pass"        => $request->get('password'),
                                         "passHash"    => '',
                                         "observacion" => "Modificada por el usuario " . $this->getUser()->getUsername()
                                     );
                $user->addPasswordHistory($passHistory);
            }
            
            $user->setUpdatedBy($this->getUser());
  
            try{
                $em->flush();
                $this->addFlash('success', "Usuario ". $user->getUsername() . " modificado con éxito");
            }
            catch(\Exception $e ){
                $this->addFlash('error', "El usuario ". $user->getUsername() . " no pudo ser modificado");
            }
        }
        return array(
            'entity' => $user,
            'form'   => $form->createView(),
        );
    }
   
    /**
     * @Route("/{user}/activar", name="user_activar", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_USER_ACTIVATE')")
     * @Method({"POST"})
     * 
     */
    public function activarAction(Request $request,Usuario $user)
    {
        if(!$this->validarEdicionUsuario($user))
        {
            return new JsonResponse(array('resultado' => 1, 'mensaje' => 'Error al activar el usuario'));
        }
        
        $em  = $this->getDoctrine()->getManager();
           
        $user->setIsActive(true);    
        $user->setUpdatedBy($this->getUser());
        $message = \Swift_Message::newInstance()
                   ->setSubject("PLENUS - Cuenta activada del usuario: " . $user->getUsername())
                   ->setFrom($this->container->getParameter('mail_from_no_reply'))
                   ->setTo($user->getEmail())
                   ->setBody($this->renderView('SeguridadBundle:Usuario:cuenta.activada.email.html.twig', array()),'text/html')
                   ;
        try{
            $em->flush();
            $this->get('mailer')->send($message);
            return new JsonResponse(array('resultado' => 0, 'mensaje' => 'Usuario activado con éxito'));
        }
        catch(\Exception $e ){
             return new JsonResponse(array('resultado' => 1, 'mensaje' => 'Error al activar el usuario'));
        }
    }
    
    /**
     * Creates a form to create a User entity.
     *
     * @param Usuario $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Usuario $entity)
    {
        $form = $this->createForm(UsuarioAdminType::class, $entity,
                                  array(
                                        'action' => $this->generateUrl('user_edit', array('user' => $entity->getId())),
                                        'method' => 'POST'
                                       )
                                 );
    
        return $form;
    }
    
    /**
     * @Route("/{user}/auto/edit", name="user_auto_edit")
     * @Security("has_role('ROLE_USER')")
     * @Template("SeguridadBundle:Usuario:auto.edit.html.twig")
     */
    public function autoEditAction(Request $request,Usuario $user)
    {
        $form = $this->createForm(UsuarioEditType::class, $user,
                                  array(
                                        'action' => $this->generateUrl('user_auto_edit', array('user' => $user->getId())),
                                        'method' => 'POST'
                                       )
                                 );
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            $em  = $this->getDoctrine()->getManager();
            $user->setUpdatedBy($this->getUser());
  
            try{
                $em->flush();
                $this->addFlash('success', "Usuario ". $user->getUsername() . " modificado con éxito");
            }
            catch(\Exception $e ){
                
                if(strpos($e->getMessage(), 'unique_dni') !== false)
                {
                    $error = 'Ya existe un usuario con ese tipo y número de documento.';
                }else{
                    if(strpos($e->getMessage(), 'unique_email') !== false)
                    {
                        $error = 'Ya existe un usuario con ese email registrado.';   
                    }else{
                        $error = 'Error al actualizar los datos';
                    }
                }
                $this->addFlash('error', $error);
            }
        }
        return array(
            'entity' => $user,
            'form'   => $form->createView(),
        );
    }
    
     /**
     * @Route("/{user}/eliminar", name="user_eliminar", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_USER_DELETE')")
     * @Method({"POST"})
     * 
     */
    public function eliminarAction(Request $request,Usuario $user)
    {
        if($user->getLastActivity())
        {
            return new JsonResponse(array('resultado' => 1, 'mensaje' => 'Error al eliminar el usuario, el mismo registra actividad'));
        }
        
        if(!$this->validarEdicionUsuario($user))
        {
            return new JsonResponse(array('resultado' => 1, 'mensaje' => 'Error al eliminar el usuario'));
        }
        
        $em  = $this->getDoctrine()->getManager();

        foreach($user->getLogs() as $log)
        {
            $em->remove($log);
        }
        $em->flush();
        
        $em->remove($user);
        
        try{
            $em->flush();
            return new JsonResponse(array('resultado' => 0, 'mensaje' => 'Usuario eliminado con éxito'));
        }
        catch(\Exception $e ){
             return new JsonResponse(array('resultado' => 1, 'mensaje' => 'Error al eliminar el usuario' . $e->getMessage()));
        }
    }
    
    private function validarEdicionUsuario($usuario)
    {
        if($this->isGranted('ROLE_ADMIN')){
            return true;
        }
        
        if(!$this->getUser()->mismoMunicipio($usuario)){
            return false;
        }
        
        if(!$usuario->hasRole('ROLE_INSCRIPTOR') || $usuario->hasRole('ROLE_ADMIN')){
            return false;            
        }
        
        return true;
    }
}