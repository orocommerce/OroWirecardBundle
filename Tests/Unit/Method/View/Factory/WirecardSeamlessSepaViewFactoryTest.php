<?php

namespace Oro\Bundle\WirecardBundle\Tests\Unit\Method\View\Factory;

use Oro\Bundle\WirecardBundle\Method\Config\WirecardSeamlessSepaConfigInterface;
use Oro\Bundle\WirecardBundle\Method\View\Factory\WirecardSeamlessSepaViewFactory;
use Oro\Bundle\WirecardBundle\Method\View\WirecardSeamlessSepaView;
use Symfony\Component\Form\FormFactoryInterface;

class WirecardSeamlessSepaViewFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var FormFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $formFactory;

    /** @var WirecardSeamlessSepaViewFactory */
    protected $factory;

    protected function setUp(): void
    {
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->factory = new WirecardSeamlessSepaViewFactory($this->formFactory);
    }

    public function testCreate()
    {
        $config = $this->createMock(WirecardSeamlessSepaConfigInterface::class);
        $expectedView = new WirecardSeamlessSepaView($this->formFactory, $config);
        $this->assertEquals($expectedView, $this->factory->create($config));
    }
}
