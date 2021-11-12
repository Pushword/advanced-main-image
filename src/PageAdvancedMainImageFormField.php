<?php

namespace Pushword\AdvancedMainImage;

use Pushword\Admin\FormField\PageMainImageField;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PageAdvancedMainImageFormField extends PageMainImageField
{
    public function formField(FormMapper $form): FormMapper
    {
        parent::formField($form);

        $subject = $this->admin->getSubject();

        $form->add('mainImageFormat', ChoiceType::class, [
            'required' => false,
            'mapped' => false,
            'label' => 'admin.page.mainImageFormat.label',
            'choices' => [
                'admin.page.mainImageFormat.none' => 1,
                'admin.page.mainImageFormat.normal' => 0,
                'admin.page.mainImageFormat.13fullscreen' => 2,
                'admin.page.mainImageFormat.34fullscreen' => 3,
                //'admin.page.mainImageFormat.fullscreen' => 4,
            ],
            'data' => (int) ($subject->getCustomProperty('mainImageFormat')),
        ]);

        return $form;
    }

    public static function formatToRatio($format): string
    {
        switch ($format) {
            case 2: return 'screen-1/3';
            case 3: return 'screen-3/4';
            case 4: return 'screen';
        }

        return 'screen-3/4';
    }
}
