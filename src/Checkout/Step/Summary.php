<?php

namespace SilverShop\Checkout\Step;

use SilverShop\Cart\ShoppingCart;
use SilverShop\Checkout\CheckoutComponentConfig;
use SilverShop\Checkout\Component\Notes;
use SilverShop\Checkout\Component\Terms;
use SilverShop\Forms\PaymentForm;
use SilverShop\Model\Order;
use SilverStripe\Control\HTTPResponse_Exception;

class Summary extends CheckoutStep
{
    private static array $allowed_actions = [
        'summary',
        'ConfirmationForm',
    ];

    public function summary(): array
    {
        $paymentForm = $this->ConfirmationForm();
        return [
            'OrderForm' => $paymentForm,
        ];
    }

    public function ConfirmationForm(): PaymentForm
    {
        $order = ShoppingCart::curr();
        if (!$order instanceof Order) {
            // Session/cart might expire between checkout steps; redirect instead of hard-failing on null order.
            throw new HTTPResponse_Exception($this->owner->redirect($this->owner->Link()));
        }

        $checkoutComponentConfig = CheckoutComponentConfig::create($order, false);
        $checkoutComponentConfig->addComponent(Notes::create());
        $checkoutComponentConfig->addComponent(Terms::create());
        $this->owner->extend('updateConfirmationComponentConfig', $checkoutComponentConfig);

        $paymentForm = PaymentForm::create($this->owner, 'ConfirmationForm', $checkoutComponentConfig);
        $paymentForm->setFailureLink($this->owner->Link('summary'));
        $this->owner->extend('updateConfirmationForm', $paymentForm);

        return $paymentForm;
    }
}
