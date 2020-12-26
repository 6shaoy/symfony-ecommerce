<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\DataTransformer\CentimesTransformer;
use App\Form\Type\PriceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['placeholder' => 'Tapez le nom du produit']
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => ['placeholder' => 'Tapez la description courte']
            ])
            // ->add('price', MoneyType::class, [
            //     'label' => 'Prix du produit',
            //     'attr' => ['placeholder' => 'Tapez le prix en Euro'],
            //     'divisor' => 100
            // ])
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
            ])
            ->add('price', PriceType::class, [
                'label' => 'Prix',
                'attr' => ['placeholder' => 'test my own type field'],
            ])
        ;


        // 价格的金额转换可以直接在MoneyType中设置divisor，下面是当我们遇到复杂的数据转换时使用的

        // $builder->get('price')->addModelTransformer(new CentimesTransformer);
        // 以下代码可写入类CentimesTransformer中，方便以后复用
        //                     // addViewTransformer 注意查一下区别，干预的时间点不同
        //                     // Model 数据出入到form中时
        //                     // View form将数据展示出时
        // $builder->get('price')->addModelTransformer(new CallbackTransformer(
        //     function($value){ // donnes->form
        //         if ($value === null) return;
        //         return $value / 100;
        //     },
        //     function($value){ // form->donne
        //         if ($value === null) return;
        //         return $value * 100;
        //     }
        // ));

        // 下面可以实现金额的转换但不建议，事件最好应用在form上而不是data上
        // $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
        //     $product = $event->getData();
        //     if ($product->getPrice() !== null){
        //         $product->setPrice($product->getPrice() / 100);
        //     }
        // });

        // $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event){
        //     $product = $event->getData();
        //     if ($product->getPrice() !== null){
        //         $product->setPrice($product->getPrice() * 100);
        //     }
        // });



        // ===================================================================
        // 问题：在create时显示category选项框，在edit时不显示
        // 原理：通过监听事件，获取事件中的数据（create时，还没创建对象，所以值均为null），
        // 进行判断，根据判断结果决定是否添加category选项框
        // 实现还需要修改上面的代码和template中的代码

        // $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
        //     // dd($event);
        //     $form = $event->getForm();
            
        //     // 下面注释告诉编辑器$product是Product这个类的对象
        //     /** @var Product */ 
        //     $product = $event->getData();

        //     if ($product->getId() === null) {
        //         $form->add('category', EntityType::class, [
        //             'label' => 'Catégorie',
        //             'placeholder' => '-- Choisir une catégorie --',
        //             'class' => Category::class,
        //             // 'choice_label' => 'name'
        //             'choice_label' => function(Category $category) {
        //                 return strtoupper($category->getName());
        //             }
        //         ]);
        //     }
        // });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
