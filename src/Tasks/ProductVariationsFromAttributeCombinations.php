<?php

namespace SilverShop\Tasks;

use SilverShop\Page\Product;
use SilverStripe\Dev\BuildTask;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Input\InputInterface;

/**
 *
 * @subpackage tasks
 */
class ProductVariationsFromAttributeCombinations extends BuildTask
{
    protected string $title = 'Generate Product Variations From Attributes';

    protected static string $description = 'Generates product variations from existing attribute combinations.';

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        $products = Product::get();
        if (!$products->count()) {
            $output->writeln('No products found.');
            return 0;
        }

        foreach ($products as $product) {
            $product->generateVariationsFromAttributes();
        }

        $output->writeln('Product variations generated.');

        return 0;
    }
}
