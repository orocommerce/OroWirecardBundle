<?php

namespace Oro\Bundle\WirecardBundle\Tests\Unit\DependencyInjection\EventListener\Callback;

use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\Provider\PaymentMethodProviderInterface;
use Oro\Bundle\WirecardBundle\EventListener\Callback\WirecardIPCheckListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Oro\Bundle\PaymentBundle\Event\CallbackNotifyEvent;
use Oro\Bundle\WirecardBundle\Wirecard\Seamless\Option;

class WirecardIPCheckListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @var PaymentMethodProviderInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $paymentMethodProvider;


    protected function setUp()
    {
        $this->paymentMethodProvider = $this->createMock(PaymentMethodProviderInterface::class);
    }

    /**
     * @return array[]
     */
    public function returnAllowedIPs()
    {
        return [
            'Wirecard\'s IP address 1 should be allowed' => ['195.93.244.97'],
            'Wirecard\'s IP address 2 should be allowed' => ['185.60.56.35'],
            'Wirecard\'s IP address 3 should be allowed' => ['185.60.56.36'],
        ];
    }

    /**
     * @return array[]
     */
    public function returnNotAllowedIPs()
    {
        return [
            'Google\'s IP address 4 should not be allowed' => ['216.58.214.206'],
            'Facebook\'s IP address 5 should not be allowed' => ['173.252.120.68'],
        ];
    }

    /**
     * @dataProvider returnAllowedIPs
     * @param string $remoteAddress
     */
    public function testOnNotifyAllowed($remoteAddress)
    {
        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction
            ->setAction('action')
            ->setPaymentMethod('payment_method')
            ->setResponse(['existing' => 'response']);

        $masterRequest = $this->createMock(Request::class);
        $masterRequest->method('getClientIp')->will($this->returnValue($remoteAddress));

        /** @var RequestStack|\PHPUnit_Framework_MockObject_MockObject $requestStack */
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getMasterRequest')->will($this->returnValue($masterRequest));

        /** @var CallbackNotifyEvent|\PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->createMock(CallbackNotifyEvent::class);
        $event
            ->expects($this->never())
            ->method('markFailed');

        $event
            ->expects($this->once())
            ->method('getPaymentTransaction')
            ->willReturn($paymentTransaction);

        $this->paymentMethodProvider
            ->expects(static::once())
            ->method('hasPaymentMethod')
            ->with('payment_method')
            ->willReturn(true);

        $listener = new WirecardIPCheckListener($this->paymentMethodProvider, $requestStack);
        $listener->onNotify($event);
    }

    /**
     * @dataProvider returnNotAllowedIPs
     * @param string $remoteAddress
     */
    public function testOnNotifyNotAllowed($remoteAddress)
    {
        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction
            ->setAction('action')
            ->setPaymentMethod('payment_method')
            ->setResponse(['existing' => 'response']);

        $masterRequest = $this->createMock(Request::class);
        $masterRequest->method('getClientIp')->will($this->returnValue($remoteAddress));

        /** @var RequestStack|\PHPUnit_Framework_MockObject_MockObject $requestStack */
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getMasterRequest')->will($this->returnValue($masterRequest));

        /** @var CallbackNotifyEvent|\PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->createMock(CallbackNotifyEvent::class);
        $event
            ->expects($this->once())
            ->method('markFailed');

        $event
            ->expects($this->once())
            ->method('getPaymentTransaction')
            ->willReturn($paymentTransaction);

        $this->paymentMethodProvider
            ->expects(static::once())
            ->method('hasPaymentMethod')
            ->with('payment_method')
            ->willReturn(true);

        $listener = new WirecardIPCheckListener($this->paymentMethodProvider, $requestStack);
        $listener->onNotify($event);
    }

    /**
     * @dataProvider returnNotAllowedIPs
     * @param string $remoteAddress
     */
    public function testOnNotifyAllowIfTestMode($remoteAddress)
    {
        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction
            ->setAction('action')
            ->setPaymentMethod('payment_method')
            ->setRequest(
                [
                    Option\TestMode::TESTMODE => true
                ]
            )
            ->setResponse(['existing' => 'response']);

        $masterRequest = $this->createMock(Request::class);
        $masterRequest->method('getClientIp')->will($this->returnValue($remoteAddress));

        /** @var RequestStack|\PHPUnit_Framework_MockObject_MockObject $requestStack */
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getMasterRequest')->will($this->returnValue($masterRequest));

        /** @var CallbackNotifyEvent|\PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->createMock(CallbackNotifyEvent::class);
        $event
            ->expects($this->never())
            ->method('markFailed');

        $event
            ->expects($this->once())
            ->method('getPaymentTransaction')
            ->willReturn($paymentTransaction);

        $this->paymentMethodProvider
            ->expects(static::once())
            ->method('hasPaymentMethod')
            ->with('payment_method')
            ->willReturn(true);

        $listener = new WirecardIPCheckListener($this->paymentMethodProvider, $requestStack);
        $listener->onNotify($event);
    }

    public function testOnNotifyDontAllowIfMasterRequestEmpty()
    {
        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction
            ->setAction('action')
            ->setPaymentMethod('payment_method')
            ->setResponse(['existing' => 'response']);

        $masterRequest = null;

        /** @var RequestStack|\PHPUnit_Framework_MockObject_MockObject $requestStack */
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getMasterRequest')->will($this->returnValue($masterRequest));

        /** @var CallbackNotifyEvent|\PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->createMock(CallbackNotifyEvent::class);
        $event
            ->expects($this->once())
            ->method('markFailed');

        $event
            ->expects($this->once())
            ->method('getPaymentTransaction')
            ->willReturn($paymentTransaction);

        $this->paymentMethodProvider
            ->expects(static::once())
            ->method('hasPaymentMethod')
            ->with('payment_method')
            ->willReturn(true);

        $listener = new WirecardIPCheckListener($this->paymentMethodProvider, $requestStack);
        $listener->onNotify($event);
    }
}
