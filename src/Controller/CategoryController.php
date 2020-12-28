<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends AbstractController
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }


    public function renderMenuBar()
    {
        $categories = $this->categoryRepository->findAll();
        return $this->render('category/_menu.html.twig', [
            'categories' => $categories
        ]);
    }


    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('homepage');
        }
        return $this->render('category/create.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     * IsGranted("ROLE_ADMIN", message="pas de droit par annotation") 这句也可以放到整个controller的上面来用
     */
    public function edit(Security $security, $id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        /**
         * 假设：所有登入的用户都可以创建category，但是只有admin可以修改
         * 先关闭 config/packages/security.yaml 的 access_control
         * 
         * 注入 security
         */

        // 方法一：
        // $user = $security->getUser();

        // if ($user === null) {
        //     return $this->redirectToRoute('security_login');
        // }

        // if (!in_array('ROLE_ADMIN', $user->getRoles())){
        //     throw new AccessDeniedHttpException('Vous avez pas le droit d\'access');
        // }

        // 方法二：
        // $user = $security->getUser();

        // if ($user === null) {
        //     return $this->redirectToRoute('security_login');
        // }
        // if ($security->isGranted('ROLE_ADMIN') === false) {
        //     throw new AccessDeniedHttpException('Vous avez pas le droit d\'access ici');
        // }

        // 方法三：不需要注入security
        // $user = $this->getUser();
        // if ($user === null) {
        //     return $this->redirectToRoute('security_login');
        // }
        // if ($this->isGranted('ROLE_ADMIN') === false) {
        //     throw new AccessDeniedHttpException('Vous avez pas le droit!!');
        // }

        // 方法四：
        // $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'pad de droit!!');

        // 方法五：annotation

        //=======================================================
        $category = $categoryRepository->find($id);
        if (!$category) {
            throw new NotFoundHttpException('Catégorie n\'existe pas');
        }
        //=======================================================

        /**
         * 需求：用户可以修改自己创建的category，不能修改其它的
         * 先取消IsGranted
         */
        // 方法一：
        // $user = $this->getUser();
        // if (!$user) {
        //     return $this->redirectToRoute('security_login');
        // }
        // if ($user !== $category->getOwner()){
        //     throw new AccessDeniedHttpException('Vous etes pas le owner');
        // }

        // 方法二：make:voter CategoryVoter
        //$security->isGranted('CAN_EDIT', $category);
        // $this->denyAccessUnlessGranted('CAN_EDIT', $category, 'pas le votre');

        // 方法三：同上，使用annotation
        // IsGranted("CAN_EDIT", subject="id", message="vous n'etes pas le proprietaire!")
        // 对应的CategoryVoter，subject就变成了id，需要注入categoryRepository来查找对应的category
        // 再进行判断
        // support 方法可以改为 is_numeric($subject)
        // 注入后获取 category，先判断时候获取成功，如果失败throw exception
        // 如果成功再继续


        //=======================================================

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->flush();
            return $this->redirectToRoute('homepage');
        }
        return $this->render('category/edit.html.twig', [
            'formView' => $form->createView()
        ]);
    }
}
