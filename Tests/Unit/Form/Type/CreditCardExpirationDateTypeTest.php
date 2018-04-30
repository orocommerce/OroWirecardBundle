<?php

namespace Oro\Bundle\WirecardBundle\Tests\Unit\Form\Type;

use Oro\Bundle\WirecardBundle\Form\Type\CreditCardExpirationDateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\Test\FormIntegrationTestCase;

class CreditCardExpirationDateTypeTest extends FormIntegrationTestCase
{
    /**
     * @dataProvider formConfigurationProvider
     *
     * @param array $formFields
     * @param array $formOptions
     */
    public function testFormConfiguration(array $formFields, array $formOptions)
    {
        $form = $this->factory->create(CreditCardExpirationDateType::class);
        $this->assertFormOptions($form->getConfig(), $formOptions);
        foreach ($formFields as $fieldname => $fieldData) {
            $this->assertTrue($form->has($fieldname));
            $field = $form->get($fieldname);
            $this->assertInstanceOf($fieldData['type'], $field->getConfig()->getType()->getInnerType());
            $this->assertFormOptions($field->getConfig(), $fieldData['options']);
        }
    }

    /**
     * @return array
     */
    public function formConfigurationProvider()
    {
        return [
            [
                [
                    'month' => [
                        'type' => ChoiceType::class,
                        'options' => [
                            'required' => true,
                        ],
                    ],
                    'year' => [
                        'type' => ChoiceType::class,
                        'options' => [
                            'required' => true,
                        ],
                    ],
                ],
                [
                    'model_timezone' => 'UTC',
                    'view_timezone' => 'UTC',
                    'format' => 'dMy',
                    'input' => 'array',
                    'years' => range(date('Y'), date('Y') + CreditCardExpirationDateType::YEAR_PERIOD),
                    'months' => ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'],
                ],
            ],
        ];
    }

    /**
     * @param FormConfigInterface $formConfig
     * @param array $formOptions
     */
    public function assertFormOptions(FormConfigInterface $formConfig, $formOptions)
    {
        $options = $formConfig->getOptions();
        foreach ($formOptions as $formOptionName => $formOptionData) {
            $this->assertTrue($formConfig->hasOption($formOptionName));
            $this->assertEquals($formOptionData, $options[$formOptionName]);
        }
    }
}
