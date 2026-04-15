<?php

namespace SilverShop\Extension;

use SilverShop\Checkout\OrderProcessor;
use SilverShop\Model\Order;
use SilverStripe\Core\Extension;

/**
 * Customisations to the payment model specifically for the shop module.
 * @property int $OrderID
 * @method Order Order()
 */
class PaymentExtension extends Extension
{
    private static array $has_one = [
        'Order' => Order::class,
    ];

    public function onAwaitingAuthorized($response): void
    {
        $this->placeOrder();
    }

    public function onAwaitingCaptured($response): void
    {
        $this->placeOrder();
    }

    public function onAuthorized($response): void
    {
        $this->placeOrder();
    }

    public function onCaptured($response): void
    {
        // ensure order is being reloaded from DB, to prevent dealing with stale data!
        /**
         * @var Order $order
         */
        $order = Order::get()->byID($this->owner->OrderID);
        if ($order && $order->exists()) {
            OrderProcessor::create($order)->completePayment();
        }
    }

    protected function placeOrder(): void
    {
        // ensure order is being reloaded from DB, to prevent dealing with stale data!
        /**
         * @var Order $order
         */
        $order = Order::get()->byID($this->owner->OrderID);
        if ($order && $order->exists()) {
            OrderProcessor::create($order)->placeOrder();
        }
    }
}
