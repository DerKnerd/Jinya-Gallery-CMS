<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.12.2017
 * Time: 21:07
 */

namespace Jinya\Components\Form;

use Jinya\Entity\Form\Form;
use Jinya\Entity\Form\FormItem;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Jinya\Services\Slug\SlugServiceInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use function array_key_exists;
use function strpos;

class FormGenerator implements FormGeneratorInterface
{
    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var ConfigurationServiceInterface */
    private $frontendConfigurationService;

    /** @var SlugServiceInterface */
    private $slugService;

    /**
     * FormGenerator constructor.
     * @param FormFactoryInterface $formFactory
     * @param ConfigurationServiceInterface $frontendConfigurationService
     * @param SlugServiceInterface $slugService
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        ConfigurationServiceInterface $frontendConfigurationService,
        SlugServiceInterface $slugService
    ) {
        $this->formFactory = $formFactory;
        $this->frontendConfigurationService = $frontendConfigurationService;
        $this->slugService = $slugService;
    }

    public function generateForm(Form $form): FormInterface
    {
        $formBuilder = $this->formFactory->createBuilder();
        /** @var FormItem $item */
        foreach ($form->getItems() as $item) {
            $originalOptions = $item->getOptions();
            $options = [
                'label' => $item->getLabel(),
                'required' => $originalOptions['required'],
            ];
            if (array_key_exists('choices', $originalOptions)) {
                $choices = [];
                foreach ($originalOptions['choices'] as $choice) {
                    $choices[$choice] = $choice;
                }

                $options['choices'] = $choices;
                $activeTheme = $this->frontendConfigurationService->getConfig()->getCurrentTheme();
                $options['placeholder'] = $activeTheme->getConfiguration()['form']['dropdowns']['placeholder'];
            }

            if (false !== strpos($item->getType(), 'TextareaType')) {
                $options['attr'] = [
                    'rows' => 10,
                ];
            }

            $formBuilder->add($this->slugService->generateSlug($item->getLabel()), $item->getType(), $options);
        }

        return $formBuilder->getForm();
    }
}
