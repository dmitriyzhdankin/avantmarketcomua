<?php

class shopCheckoutPayment extends shopCheckout
{
    protected $step_id = 'payment';

    public function display()
    {
        $plugin_model = new shopPluginModel();

        if (waRequest::param('payment_id') && is_array(waRequest::param('payment_id'))) {
            $methods = $plugin_model->getById(waRequest::param('payment_id'));
        } else {
            $methods = $plugin_model->listPlugins('payment');
        }

        $shipping = $this->getSessionData('shipping');
        if ($shipping) {
            $disabled = shopHelper::getDisabledMethods('payment', $shipping['id']);
        } else {
            $disabled = array();
        }

        $currencies = wa('shop')->getConfig()->getCurrencies();
        foreach ($methods as $method_id => $m) {
            if (in_array($method_id, $disabled)) {
                unset($methods[$method_id]);
                continue;
            }
            $plugin = shopPayment::getPlugin($m['plugin'], $m['id']);
            $plugin_info = $plugin->info($m['plugin']);
            $methods[$method_id]['icon'] = $plugin_info['icon'];
            $custom_fields = $this->getCustomFields($method_id, $plugin);
            $custom_html = '';
            foreach ($custom_fields as $c) {
                $custom_html .= '<div class="wa-field">'.$c.'</div>';
            }
            $methods[$method_id]['custom_html'] = $custom_html;
            $allowed_currencies = $plugin->allowedCurrency();
            if ($allowed_currencies !== true) {
                $allowed_currencies = (array) $allowed_currencies;
                if (!array_intersect($allowed_currencies, array_keys($currencies))) {
                    $methods[$method_id]['error'] = sprintf(_w('Payment procedure cannot be processed because required currency %s is not defined in your store settings.'), implode(', ', $allowed_currencies));
                }
            }
        }

        $view = wa()->getView();
        $view->assign('checkout_payment_methods', $methods);
        $m = reset($methods);
        $view->assign('payment_id', $this->getSessionData('payment', $m ? $m['id'] : null));
    }

    protected function getCustomFields($id, waPayment $plugin)
    {
        $contact = $this->getContact();
        $order_params = $this->getSessionData('params', array());
        $payment_params = isset($order_params['payment']) ? $order_params['payment'] : array();
        foreach ($payment_params as $k => $v) {
            $order_params['payment_params_'.$k] = $v;
        }
        $order = new waOrder(array('contact' => $contact,
            'contact_id' => $contact ? $contact->getId() : null,
            'params' => $order_params
        ));
        $custom_fields = $plugin->customFields($order);
        if (!$custom_fields) {
            return $custom_fields;
        }

        $params = array();
        $params['namespace'] = 'payment_'.$id;
        $params['title_wrapper'] = '%s';
        $params['description_wrapper'] = '<br><span class="hint">%s</span>';
        $params['control_wrapper'] = '<div class="wa-name">%s</div><div class="wa-value">%s %s</div>';

        $selected = ($id == $this->getSessionData('payment'));

        $controls = array();
        foreach ($custom_fields as $name => $row) {
            $row = array_merge($row, $params);
            if ($selected && isset($payment_params[$name])) {
                $row['value'] = $payment_params[$name];
            }
            $controls[$name] = waHtmlControl::getControl($row['control_type'], $name, $row);
        }

        return $controls;
    }


    public function validate()
    {

    }

    public function execute()
    {
        if ($payment_id = waRequest::post('payment_id')) {
            $this->setSessionData('payment', $payment_id);
            if ($comment = waRequest::post('comment')) {
                $this->setSessionData('comment', $comment);
            }
            if ($payment_params = waRequest::post('payment_'.$payment_id)) {
                $params = $this->getSessionData('params', array());
                $params['payment'] = $payment_params;
                $this->setSessionData('params', $params);
            }
            return true;
        } else {
            return false;
        }

    }
    
}
