<?php

namespace App\Controller;

use App\Entity\Product;
use Twig\Environment;
use App\Taxes\Detector;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\Validator\Constraints\Collection;
// use Symfony\Component\Validator\Constraints\GreaterThan;
// use Symfony\Component\Validator\Constraints\Length;
// use Symfony\Component\Validator\Constraints\LessThanOrEqual;
// use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HelloController
{
    /**
     * @Route("/hello/{prenom<\w+>?world}", name="hello")
     */
    public function hello($prenom, Detector $detector, Environment $twig)
    {
        // dump($detector->detect(101));
        // dump($detector->detect(10));


        $html = $twig->render('hello.html.twig', [
            'prenom' => $prenom,
            'ages' => [
                12,
                18,
                29,
                8
            ]
        ]);

        return new Response($html);
    }

    /**
     * @Route("/validator", name="test_validator")
     */
    public function validator(ValidatorInterface $validator){
        
        // ================= 1/5 validation de donnees simple (scalaires) ================
        // $age = -120;
        // $res = $validator->validate($age, [
        //     new Assert\LessThanOrEqual(120),
        //     new Assert\GreaterThan([
        //         'value' => 0,
        //         'message' => 'l\'age doit etre superieur a {{ compared_value }}, mais vous avez {{ value }}, c\'impossible!!'
        //     ])
        // ]);

        // if ($res->count() > 0){
        //     dd($res);
        // } else {
        //     dd('Tout est OK');
        // }

        // =======================================================

        // ================= 2/5 Validation de donnes complexes (tableaux) ================
        // $client = [
        //     'nom' => 'Lior',
        //     'pwd' => 'he',
        //     'voiture' => [
        //         'marque' => 'renault',
        //         'couleur' => 'noire'
        //     ]
        // ];

        // $collection = new Collection([
        //     'nom' => new NotBlank(['message' => 'le nom est obligatoire']),
        //     'pwd' => [
        //         new Assert\NotBlank(),
        //         new Assert\Length(['min'=>6])
        //     ],
        //     'voiture' => new Collection([
        //         'marque' => new Assert\NotBlank(),
        //         'couleur' => new Assert\NotBlank()
        //     ]),
        // ]);

        // $res = $validator->validate($client, $collection);
        // if ($res->count() == 0) dd('tout est ok!');
        // dd ($res);

        // =======================================================

        // ================= 3/5 Validation de donnes Objet =====================
        // 验证条件定义在 config/validator/product.yaml 文件中，product.yaml 命名不是必须用product

        // $product = new Product;
        // $product->setName('hello');
        // $product->setPrice(20);
        //
        // $res = $validator->validate($product);
        // dd($res);

        // =======================================================

        // ================= 4/5 Validation de donnes Objet =====================

        // 直接在类中定义函数 一个公共静态方法 , 例：Product
        // public static function loadValidatorMetadata(ClassMetadata $metadata)

        // $product = new Product;
        // // $product->setName('hello');
        // // $product->setPrice(20);
        
        // $res = $validator->validate($product);
        // dd($res);

        // =======================================================

        // ================= 5/5 Validation de donnes Objet =====================
        // 在 product 类中，annotation里直接定义，这是现在symfony的推荐方法
        $product = new Product;
        $product->setName('he');
        // $product->setPrice(20);
        
        $res = $validator->validate($product);
        dd($res);


        // validation peut faire dans le formulaire avec 
        // $builder->add('name', TextType::class, [
        //     ...
        //     'contraints' => new NotBlank(),
        //     ...
        // ])


        /**
         * validation groups
         * 可以将验证进行分组，在验证时选择只验证某个分组或某几个分组或全部分组
         * 没有指定分组的验证会被放入一个默认的分组，叫 default
         * 可以为同一个属性设置不同验证要求的分组，在不同的时候调用不同的分组来验证
         */
        // 举例对于 name 定义分组 with-name
        // @Assert\NotBlank(message="le prix est obligatoire", groups={"with-name"})

        // $res = $validator->validate($product);                                  // 只验证default分组，即没有设置分组的
        // $res = $validator->validate($product, null, ["with-name"]);             // 只验证分组为 with-name 的
        // $res = $validator->validate($product, null, ["default", "with-name"]);  // 验证所有的，因为只设置了一个分组

        // 对于表单操作如下
        // $form = $this->createForm(ProductType::class, $product, [
        //     'validation_groups' => "with-name"
        // ]);
        // $form = $this->createForm(ProductType::class, $product, [
        //     'validation_groups' => ["default", "with-name"]
        // ]);


        /**
         * 针对 objet 验证方法有
         *  - yaml
         *  - php classique
         *  - annotation
         *  - form
         */
    }
}