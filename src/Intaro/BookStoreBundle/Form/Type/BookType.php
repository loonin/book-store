<?php

namespace Intaro\BookStoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array('label' => 'Название'));
        $builder->add('author', null, array('label' => 'Автор'));
        $builder->add('cover_src', null, array('label' => 'Обложка'));
        $builder->add('file_src', null, array('label' => 'Файл книги'));
        $builder->add('reading_date', 'date',
            array(
                'label' => 'Дата прочтения',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy'
            ));
        $builder->add('download', 'checkbox',
            array(
                'label' => 'Разрешить скачивание',
                'required' => false,
            ));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Intaro\BookStoreBundle\Entity\Book',
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'book';
    }
}