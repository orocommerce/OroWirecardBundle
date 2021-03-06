<?php

namespace Oro\Bundle\WirecardBundle\Wirecard\Seamless\Request;

use Oro\Bundle\WirecardBundle\Wirecard\Seamless\Option;

class InitPaymentRequest extends AbstractRequest
{
    const IDENTIFIER = 'init_payment';

    /**
     * {@inheritdoc}
     */
    protected function configureRequestOptions()
    {
        $this
            ->addOption(new Option\StorageId())
            ->addOption(new Option\OrderIdent())
            ->addOption(new Option\PaymentType())
            ->addOption(new Option\Amount())
            ->addOption(new Option\Currency())
            ->addOption(new Option\OrderDescription())
            ->addOption(new Option\SuccessUrl())
            ->addOption(new Option\CancelUrl())
            ->addOption(new Option\FailureUrl())
            ->addOption(new Option\ConfirmUrl())
            ->addOption(new Option\ServiceUrl())
            ->addOption(new Option\ConsumerUserAgent())
            ->addOption(new Option\ConsumerIpAddress())
            ->addOption(new Option\ConsumerShippingAddress());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestIdentifier()
    {
        return self::IDENTIFIER;
    }
}
