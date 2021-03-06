<?php

class shopFrontendCartAddController extends waJsonController
{
    public function execute()
    {
        $cart_model = new shopCartItemsModel();
        $code = waRequest::cookie('shop_cart');
        if (!$code) {
            $code = md5(uniqid(time(), true));
            wa()->getResponse()->setCookie('shop_cart', $code, time() + 30 * 86400, null, '', false, true);
        }

        $data = waRequest::post();

        if (isset($data['parent_id'])) {
            $parent = $cart_model->getById($data['parent_id']);
            unset($parent['id']);
            $parent['parent_id'] = $data['parent_id'];
            $parent['type'] = 'service';
            $parent['service_id'] = $data['service_id'];
            if (isset($data['service_variant_id'])) {
                $parent['service_variant_id'] = $data['service_variant_id'];
            } else {
                $service_model = new shopServiceModel();
                $service = $service_model->getById($data['service_id']);
                $parent['service_variant_id'] = $service['variant_id'];
            }
            $cart = new shopCart();
            $this->response['id'] = $cart->addItem($parent);
            $this->response['total'] = shop_currency($cart->total(), true);
            $this->response['count'] = shop_currency($cart->count());
            $this->response['discount'] = shop_currency($cart->discount(), true);
            $this->response['item_total'] = shop_currency($cart->getItemTotal($data['parent_id']), true);
            return;
        }

        $sku_model = new shopProductSkusModel();
        $product_model = new shopProductModel();
        if (!isset($data['product_id'])) {
            $sku = $sku_model->getById($data['sku_id']);
            $product = $product_model->getById($sku['product_id']);
        } else {
            $product = $product_model->getById($data['product_id']);
            if (isset($data['sku_id'])) {
                $sku = $sku_model->getById($data['sku_id']);
            } else {
                if (isset($data['features'])) {
                    $product_features_model = new shopProductFeaturesModel();
                    $sku_id = $product_features_model->getSkuByFeatures($product['id'], $data['features']);
                    if ($sku_id) {
                        $sku = $sku_model->getById($sku_id);
                    } else {
                        $sku = null;
                    }
                } else {
                    $sku = $sku_model->getById($product['sku_id']);
                }
            }
        }

        $quantity = waRequest::post('quantity', 1);

        if ($product && $sku) {
            // check quantity
            if (!wa()->getSetting('ignore_stock_count')) {
                $c = $cart_model->countSku($code, $sku['id']);
                if ($sku['count'] !== null && $c >= $sku['count']) {
                    $this->errors = sprintf(_w('Only %d left in stock. Sorry.'), $c);
                    return;
                }
            }
            $services = waRequest::post('services', array());
            $item_id = null;
            if (!$services) {
                $item = $cart_model->getSingleItem($code, $product['id'], $sku['id']);
                if ($item) {
                    $item_id = $item['id'];
                    $cart_model->updateById($item_id, array('quantity' => $item['quantity'] + $quantity));
                }
            }
            if (!$item_id) {
                $data = array(
                    'code' => $code,
                    'contact_id' => $this->getUser()->getId(),
                    'product_id' => $product['id'],
                    'sku_id' => $sku['id'],
                    'create_datetime' => date('Y-m-d H:i:s'),
                    'quantity' => $quantity
                );
                $item_id = $cart_model->insert($data + array('type' => 'product'));
                if ($services) {
                    $variants = waRequest::post('service_variant');
                    $service_model = new shopServiceModel();
                    foreach ($services as $service_id) {
                        $data_service = array('service_id' => $service_id, 'type' => 'service', 'parent_id' => $item_id);
                        if (isset($variants[$service_id])) {
                            $data_service['service_variant_id'] = $variants[$service_id];
                        } else {
                            $service = $service_model->getById($service_id);
                            $data_service['service_variant_id'] = $service['variant_id'];
                        }
                        $cart_model->insert($data + $data_service);
                    }
                }
            }
            // update shop cart session data
            $shop_cart = new shopCart();
            wa()->getStorage()->remove('shop/cart');
            $total = $shop_cart->total();

            if (waRequest::isXMLHttpRequest()) {
                $this->response['item_id'] = $item_id;
                $this->response['total'] = shop_currency($total, true);
                $this->response['count'] = $shop_cart->count();
            } else {
                $this->redirect(waRequest::server('HTTP_REFERER'));
            }
        } else {
            throw new waException('product not found');
        }

    }
}