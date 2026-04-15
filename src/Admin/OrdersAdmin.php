<?php

namespace SilverShop\Admin;

use SilverShop\Forms\GridField\OrderGridFieldDetailForm_ItemRequest;
use SilverShop\Model\Order;
use SilverShop\Model\OrderStatusLog;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;
use SilverStripe\ORM\DataList;

/**
 * Order administration interface, based on ModelAdmin
 *
 * @package SilverShop\Admin
 */
class OrdersAdmin extends ModelAdmin
{
    private static string $url_segment = 'orders';

    private static string $menu_title = 'Orders';

    private static int $menu_priority = 1;

    private static string $menu_icon_class = 'silvershop-icon-cart';

    private static array $managed_models = [
        Order::class,
        OrderStatusLog::class
    ];

    private static array $model_importers = [];

    /**
     * Restrict list to non-hidden statuses
     */
    public function getList(): DataList
    {
        $list = parent::getList();

        if ($this->getModelClass() == Order::class) {
            // Exclude hidden statuses
            $list = $list->exclude('Status', Order::config()->hidden_status);
            $this->extend('updateList', $list);
        }

        return $list;
    }

    /**
     * Replace gridfield detail form to include print functionality
     */
    public function getEditForm($id = null, $fields = null): Form
    {
        $form = parent::getEditForm($id, $fields);
        if ($this->getModelClass() == Order::class) {
            /** @var GridFieldConfig $config */
            $config = $form
                ->Fields()
                ->fieldByName($this->sanitiseClassName($this->getModelClass()))
                ->getConfig();

            $config
                ->getComponentByType(GridFieldSortableHeader::class)
                ->setFieldSorting([ 'StatusI18N' => 'Status' ]);

            $config
                ->getComponentByType(GridFieldDetailForm::class)
                ->setItemRequestClass(OrderGridFieldDetailForm_ItemRequest::class); //see below
        }

        if ($this->getModelClass() == OrderStatusLog::class) {
            /** @var GridFieldConfig $config */
            $config = $form
                ->Fields()
                ->fieldByName($this->sanitiseClassName($this->getModelClass()))
                ->getConfig();

            // Remove add new button
            $config->removeComponentsByType($config->getComponentByType(GridFieldAddNewButton::class));
        }

        return $form;
    }
}
