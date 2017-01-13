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
        return array();
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
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('SeguridadBundle:Usuario')->datatable($request->request,$user,$this->get('security.authorization_checker')->isGranted('ROLE_USER_LIST_ALL'));

        //$cant = array(
        //              "activos" => $em->getRepository('SeguridadBundle:Usuario')->countActivos(),
        //              "inactivos" => $em->getRepository('SeguridadBundle:Usuario')->countInactivos()
        //            );
        $data=array(
            "draw"=> $request->request->get('draw'),
            "recordsTotal"=> $filter['total'],
            "recordsFiltered"=> $filter['filtered'],
            "data"=> array()
        );
        
        foreach ($filter['rows'] as $user){
            $role = $user->getRoles()[0];
            $persona = $user->getPersona();
            $data['data'][] = array(
                "id" => $user->getId(),
                "ico" => strpos($role, "ADMIN")>-1? true : false,
                "user"=> $user->getUsername(),
                "persona" => array(
                    "name"=> $persona->getNombre(),
                    "lastname"=> $persona->getApellido(),
                    "dni"=> $persona->getDni(),
                    "email" => $persona->getEmail(),
                    "telefono" => $persona->getTelefono(),
                ),
                "active"=> $user->getIsActive(),
                "info"=> array(
                            "created" => $user->getCreatedAt(),
                            "lastop" => $user->getLastActivity()
                        ),
                "pass"=> $user->getChangePassword(),
                "actions"=> '',//$this->generateUrl('usuario_list_logs', array('user' => $user->getId() ))
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
}