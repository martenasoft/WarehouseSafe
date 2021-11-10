<?php

namespace MartenaSoft\WarehouseSafe\Form;

use MartenaSoft\WarehouseSafe\Entity\ApplaySafe;
use MartenaSoft\WarehouseSafe\Repository\SafeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplaySafeType extends AbstractType
{
    private SafeRepository $safeRepository;

    public function __construct(SafeRepository $safeRepository)
    {
            $this->safeRepository = $safeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('types', ChoiceType::class, [
                'label' => 'My Money Storages',
                'choices' => $this->safeRepository->getItemsToChoice()
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApplaySafe::class,
        ]);
    }
}
