<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Gui\Communication\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AutosuggestType extends AbstractType
{
    const URL = 'url';
    const PLACEHOLDER = 'placeholder';

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     *
     * @return void
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars[self::URL] = $options[self::URL];
        $view->vars[self::PLACEHOLDER] = $options[self::PLACEHOLDER];
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            self::URL => '',
            self::PLACEHOLDER => 'Select value',
        ]);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'autosuggest';
    }

}