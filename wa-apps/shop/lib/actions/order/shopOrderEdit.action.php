<?php

class shopOrderEditAction extends waViewAction
{
    private $crop_size = null;
    /**
     * @var shopOrderModel
     */
    private $order_model;

    public function __construct($params = null) {
        $this->order_model = new shopOrderModel();
        return parent::__construct($params);
    }

    public function execute()
    {
        $order_id = waRequest::get('id', null, waRequest::TYPE_INT);

        $form = null;
        $order = array();
        $shipping_address = array();

        // Existing order?
        if ($order_id) {
            $order = $this->getOrder($order_id);
            $currency = $order['currency'];
            if ($order['contact_id']) {
                $has_contacts_rights = shopHelper::getContactRights($order['contact_id']);
                $shipping_address = shopHelper::getOrderAddress($order['params'], 'shipping');
                if (!empty($order['contact_id'])) {
                    try {
                        $form = shopHelper::getCustomerForm($order['contact_id']);
                    } catch (waException $e) {
                        // Contact does not exist; ignore. When $form is null, customer data saved in order is shown.
                    }
                }
            } else {
                $has_contacts_rights = shopHelper::getContactRights();
            }
        }
        // New order?
        else {
            $currency = $this->getConfig()->getCurrency();
            $has_contacts_rights = shopHelper::getContactRights();
            $form = shopHelper::getCustomerForm();
        }

        $stock_model = new shopStockModel();
        $stocks = $stock_model->getAll('id');

        $tax_model = new shopTaxModel();
        $taxes_count = $tax_model->countAll();

        $count_new = $this->order_model->getStateCounters('new');


        $this->view->assign(array(
            'form'     => $form,
            'order'    => $order,
            'stocks'   => $stocks,
            'currency' => $currency,
            'count_new' => $count_new,
            'taxes_count' => $taxes_count,
            'shipping_address' => $shipping_address,
            'has_contacts_rights' => $has_contacts_rights,
        ));
    }

    private function getOrder($order_id)
    {
        $order = $this->order_model->getOrder($order_id, true, true);
        if (!$order) {
            throw new waException("Unknow order", 404);
        }
        $order['shipping_id'] = ifset($order['params']['shipping_id'], '').'.'.ifset($order['params']['shipping_rate_id'], '');

        $sku_ids = array();
        foreach ($order['items'] as $item) {
            foreach ($item['skus'] as $sku) {
                if (empty($sku['fake'])) {
                    $sku_ids[] = $sku['id'];
                }
            }
        }
        $sku_stocks = $this->getSkuStocks(array_unique($sku_ids));

        foreach ($order['items'] as &$item) {
            $this->workupItems($item, $sku_stocks);
        }
        return $order;
    }

    private function getSkuStocks($sku_ids)
    {
        if (!$sku_ids) {
            return array();
        }
        $product_stocks_model = new shopProductStocksModel();
        return $product_stocks_model->getBySkuId($sku_ids);
    }

    private function getCropSize()
    {
        if ($this->crop_size === null) {
            $this->crop_size = $this->getConfig()->getImageSize('crop_small');
        }
        return $this->crop_size;
    }

    private function workupItems(&$item, $sku_stocks)
    {
        $size = $this->getCropSize();
        if (empty($item['image_id'])) {
            $item['url_crop_small'] = null;
        } else {
            $item['url_crop_small'] = shopImage::getUrl(
                array(
                    'id' => $item['image_id'],
                    'product_id' => $item['id'],
                    'ext' => $item['ext']
                ),
                $size
            );
        }
        if (empty($item['fake'])) {
            $item['icon'] = shopHelper::getStockCountIcon($item['count'], null, true);
        }
        foreach ($item['skus'] as &$sku) {
            if (empty($sku['fake'])) {

                if (empty($sku_stocks[$sku['id']])) {
                    $sku['icon'] = shopHelper::getStockCountIcon($sku['count'], null, true);
                } else {
                    $icons = array();
                    foreach ($sku_stocks[$sku['id']] as $stock_id => $stock) {
                        $icon  = &$icons[];
                        $icon  = shopHelper::getStockCountIcon($stock['count'], $stock_id)." ";
                        $icon .= $stock['count']." ";
                        $icon .= "@".htmlspecialchars($stock['name']);
                        unset($icon);
                    }
                    $sku['icon'] = implode(', ', $icons);
                }
            }
        }
        unset($sku);
    }
}

