<?php

namespace Oro\Bundle\WirecardBundle\Tests\Unit\Wirecard\Seamless\Option;

use Oro\Bundle\WirecardBundle\Wirecard\Seamless\Option;

use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class ConsumerIpAddressTest extends AbstractOptionTest
{
    /** {@inheritdoc} */
    protected function getOptions()
    {
        return [new Option\ConsumerIpAddress()];
    }

    /** {@inheritdoc} */
    public function configureOptionDataProvider()
    {
        return [
            'empty' => [
                [],
                [],
                [
                    MissingOptionsException::class,
                    'The required option "consumerIpAddress" is missing.',
                ],
            ],
            'invalid type given' => [
                ['consumerIpAddress' => 10],
                [],
                [
                    InvalidOptionsException::class,
                    'The option "consumerIpAddress" with value 10 is expected to be of type "string",' .
                    ' but is of type "integer".'
                ],
            ],
            'valid string value' => [
                ['consumerIpAddress' => 'test string'],
                ['consumerIpAddress' => 'test string'],
            ],
        ];
    }
}