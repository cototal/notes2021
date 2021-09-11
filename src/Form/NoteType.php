<?php

namespace App\Form;

use App\Entity\Note;
use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("title")
            ->add("content")
            ->add("category")
            ->add("sequence")
            ->add("tags", TextType::class, [
                "required" => false,
            ])
        ;
        $builder->get("tags")
            ->addModelTransformer(new CallbackTransformer(function($tagsAsArray) {
                /** @var Tag[] $tagsAsArray */
                return implode(",", array_map(function($tag) {
                    return $tag->getName();
                }, $tagsAsArray->toArray()));
            }, function ($tagsAsString) {
                return array_map(function($name) {
                    return (new Tag)->setName(trim($name));
                }, explode(",", $tagsAsString));
            }));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Note::class,
        ]);
    }
}
