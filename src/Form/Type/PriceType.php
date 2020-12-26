<?php

namespace App\Form\Type;

use App\Form\DataTransformer\CentimesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// 模拟 MoneyType 及其 divisor 选项
class PriceType extends AbstractType
{
    // 继承NumberType的所有功能
    public function getParent()
    {
        return NumberType::class;
    }

    // 自定义我们的属性options
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'divide' => true,
        ]);
    }

    // array $options 会合并所有的option在这个数组中（包括configureOptions, numberType, builder->add中的选项）
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['divide']){
            $builder->addModelTransformer(new CentimesTransformer);
        }
    }
}
