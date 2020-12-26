<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category")
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$category) {
            throw $this->createNotFoundException('Category demandé n\'exist pas');
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     */
    public function show($slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$product) {
            throw $this->createNotFoundException('Produit demandé n\'exist pas');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/create_old", name="product_create")
     */
    public function create_old(FormFactoryInterface $formFactory, Request $request, SluggerInterface $slug, EntityManagerInterface $em)
    {
        $builder = $formFactory->createBuilder(FormType::class, null, [
            'data_class' => Product::class
        ]);

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['placeholder' => 'Tapez le nom du produit']
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => ['placeholder' => 'Tapez la description courte']
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit',
                'attr' => ['placeholder' => 'Tapez le prix en Euro']
            ])
            ->add('mainPicture', UrlType::class, [
                'label' => 'Image du Produit',
                'attr' => ['placeholder' => 'Tapez une URL d\'image']
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'placeholder' => '-- Choisir une catégorie --',
                'class' => Category::class,
                // 'choice_label' => 'name'
                'choice_label' => function(Category $category) {
                    return strtoupper($category->getName());
                }
            ]);

        $form = $builder->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted()){
            $product = $form->getData();
            $product->setSlug(strtolower($slug->slug($product->getName())));
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('homepage');
        }

        return $this->render('product/create.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em){
        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $em->flush();
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $request, SluggerInterface $slug, EntityManagerInterface $em)
    {
        $form = $this->createForm(ProductType::class);

        $form->handleRequest($request);
        if($form->isSubmitted()){
            $product = $form->getData();
            $product->setSlug(strtolower($slug->slug($product->getName())));
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        return $this->render('product/create.html.twig', [
            'formView' => $form->createView()
        ]);
    }
}
