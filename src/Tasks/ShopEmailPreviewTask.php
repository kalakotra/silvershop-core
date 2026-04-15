<?php

namespace SilverShop\Tasks;

use SilverShop\Checkout\OrderEmailNotifier;
use SilverShop\Model\Order;
use SilverStripe\Dev\BuildTask;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Input\InputInterface;

/**
 * ShopEmailPreviewTask
 *
 * @author     Anselm Christophersen <ac@anselm.dk>
 * @date       September 2016
 * @package    shop
 * @subpackage tasks
 */

/**
 * ShopEmailPreviewTask
 */
class ShopEmailPreviewTask extends BuildTask
{
    protected string $title = 'Preview Shop Emails';

    protected static string $description = 'Previews shop emails';

    protected array $previewableEmails = [
        'Confirmation',
        'Receipt',
        'AdminNotification',
        'CancelNotification',
        'StatusChange'
    ];

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        $email = $input->getOption('email');
        $debug = $input->getOption('debug');

        if (!$email) {
            $output->writeln('Choose email via --email option. Available values:');
            foreach ($this->previewableEmails as $method) {
                $output->writeln('- ' . $method);
            }
            return 0;
        }

        if ($email && in_array($email, $this->previewableEmails)) {
            $order = Order::get()->first();
            if (!$order) {
                $output->writeln('No order found to preview email with.');
                return 1;
            }

            $notifier = OrderEmailNotifier::create($order);

            if ($debug) {
                $notifier->setDebugMode(true);
            }

            $method = "send$email";

            if ($email === 'StatusChange') {
                $output->writeln((string)$notifier->$method('This is a test title', 'This is a test note'));
            } else {
                $output->writeln((string)$notifier->$method());
            }
            return 0;
        }

        $output->writeln('Invalid email type.');
        return 1;
    }
}
