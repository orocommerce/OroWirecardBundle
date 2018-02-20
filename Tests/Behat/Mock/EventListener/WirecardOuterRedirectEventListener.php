<?php

namespace Oro\Bundle\WirecardBundle\Tests\Behat\Mock\EventListener;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\WirecardBundle\Tests\Behat\Mock\Client\WirecardHttpClientMock;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class WirecardOuterRedirectEventListener
{
    const SUCCESS_TRANSACTION_AMOUNT = '13';

    const SUCCESS_REDIRECT_ROUTE = 'oro_payment_callback_return';
    const FAILURE_REDIRECT_ROUTE = 'oro_payment_callback_error';
    const SILENT_POST_NOTIFY_URL = 'oro_payment_callback_notify';

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * @var HttpKernelInterface
     */
    private $httpKernel;

    /**
     * @param RouterInterface $router
     * @param DoctrineHelper $doctrineHelper
     * @param HttpKernelInterface $httpKernel
     */
    public function __construct(
        RouterInterface $router,
        DoctrineHelper $doctrineHelper,
        HttpKernelInterface $httpKernel
    ) {
        $this->router = $router;
        $this->doctrineHelper = $doctrineHelper;
        $this->httpKernel = $httpKernel;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (strpos($request->getUri(), WirecardHttpClientMock::WIRECARD_OUTER_REDIRECT_MOCK_LINK) !== false) {
            /** @var PaymentTransaction $paymentTransaction */
            $paymentTransaction = $this->doctrineHelper
                ->getEntityRepositoryForClass(PaymentTransaction::class)->findOneBy([], ['id' => 'DESC']);

            if (!$paymentTransaction) {
                throw new \InvalidArgumentException('Required PaymentTransaction not found');
            }

            $accessIdentifier = $paymentTransaction->getAccessIdentifier();
            $accessToken = $paymentTransaction->getAccessToken();

            $transactionResult = $this->isTransactionSuccessful($paymentTransaction->getAmount());

            $this->sendNotifySubRequest(
                ['accessIdentifier' => $accessIdentifier, 'accessToken' => $accessToken],
                $this->getNotifyParams($transactionResult)
            );
            $response = new RedirectResponse(
                $this->generateUrl(
                    $this->getRedirectRoute($transactionResult),
                    ['accessIdentifier' => $accessIdentifier]
                )
            );

            $event->setResponse($response);
        }
    }

    /**
     * @param array $connectParams
     * @param array $queryParams
     */
    protected function sendNotifySubRequest($connectParams, $queryParams)
    {
        $request = Request::create($this->generateUrl(
            self::SILENT_POST_NOTIFY_URL,
            $connectParams
        ), 'POST');

        foreach ($queryParams as $key => $value) {
            $request->request->set($key, $value);
        }

        $this->httpKernel->handle(
            $request,
            HttpKernelInterface::SUB_REQUEST
        );
    }

    /**
     * @param string $amount
     * @return bool
     */
    protected function isTransactionSuccessful($amount)
    {
        return $amount === self::SUCCESS_TRANSACTION_AMOUNT;
    }

    /**
     * @param bool $transactionResult
     * @return string
     */
    protected function getRedirectRoute($transactionResult)
    {
        return $transactionResult ? self::SUCCESS_REDIRECT_ROUTE : self::FAILURE_REDIRECT_ROUTE;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function generateUrl($route, $params)
    {
        return $this->router->generate(
            $route,
            $params,
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @param bool $transactionResult
     * @return array
     */
    protected function getNotifyParams($transactionResult)
    {
        return $transactionResult ?['orderNumber' => '1', 'paymentState' => 'SUCCESS'] : ['paymentState' => 'FAIL'];
    }
}
