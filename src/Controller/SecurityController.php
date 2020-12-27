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
        dump($utils->getLastAuthenticationError());
        $form = $this->createForm(LoginType::class, [
            'email' => $utils->getLastUsername()
        ]);
        return $this->render('security/login.html.twig', [
            'formView' => $form->createView(),
            'error' => $utils->getLastAuthenticationError()
        ]);

        // $utils->getLastAuthenticationError 需要从request或者session中获取错误信息
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(){}
}
