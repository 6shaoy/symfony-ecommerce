<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Zend\Code\Generator\DocBlock\Tag\AuthorTag;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $form = $this->createForm(LoginType::class, [
            'email' => $utils->getLastUsername()
        ]);

        $data = ['formView' => $form->createView(), 'error' => null];
        $error = $utils->getLastAuthenticationError();
        if ($error) {
            $data['error'] = $error->getMessage();
        }

        return $this->render('security/login.html.twig', $data);

        // $utils->getLastAuthenticationError 需要从request或者session中获取错误信息
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
    }
}
