<?php

class shopOrderTotalController extends waJsonController
{
    public function execute()
    {
        $items = waRequest::post('items');
        $product_ids = array();
        foreach ($items as $i) {
            $product_ids[] = $i['product_id'];
        }
        $product_ids = array_unique($product_ids);
        $feature_model = new shopFeatureModel();
        $f = $feature_model->getByCode('weight');
        if (!$f) {
            $values = array();
        } else {
            $values_model = $feature_model->getValuesModel($f['type']);
            $values = $values_model->getProductValues($product_ids, $f['id']);
        }

        $shipping_address = $this->getAddress();

        $shipping_items = array();
        foreach ($items as $i) {
            $shipping_items[] = array(
                'name' => '',
                'price' => $i['price'],
                'quantity' => $i['quantity'],
                'weight' => isset($values[$i['product_id']]) ? $values[$i['product_id']] : 0
            );
        }

        $this->response['shipping_methods'] = shopHelper::getShippingMethods($shipping_address, $shipping_items);
    }

    protected function getAddress()
    {
        $customer = waRequest::post('customer');
        if ($customer) {
            $c = new waContact($customer);
            $address = $c->getFirst('address.shipping');
            if ($address) {
                return $address['data'];
            }
        }
        return array();
    }
}