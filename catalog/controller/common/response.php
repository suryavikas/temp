<?php

class ControllerCommonResponse extends Controller {

    public function index() {

        $this->load->language('common/EBS');
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['continue'] = HTTP_SERVER . 'index.php?route=common/home';

        $secret_key = $this->config->get('EBS_secret_key'); // Your Secret Key
        if (isset($_GET['DR'])) {
            require('Rc43.php');
            $DR = preg_replace("/\s/", "+", $_GET['DR']);

            $rc4 = new Crypt_RC4($secret_key);
            $QueryString = base64_decode($DR);
            $rc4->decrypt($QueryString);
            $QueryString = explode('&', $QueryString);

            $response = array();
            foreach ($QueryString as $param) {
                $param = explode('=', $param);
                $response[$param[0]] = urldecode($param[1]);
            }
            $this->data['response'] = $response;
                        
            $this->load->model('checkout/order');
            if ($response['ResponseCode'] == '0') {

//$this->model_checkout_order->confirm($response['MerchantRefNo'], $this->config->get('cod_order_status_id'));

                if (isset($this->session->data['order_id'])) {
                    $this->cart->clear();

                    unset($this->session->data['shipping_method']);
                    unset($this->session->data['shipping_methods']);
                    unset($this->session->data['payment_method']);
                    unset($this->session->data['payment_methods']);
                    unset($this->session->data['guest']);
                    unset($this->session->data['comment']);
                    unset($this->session->data['order_id']);
                    unset($this->session->data['coupon']);
                }


//$order_info = $this->model_checkout_order->getOrder($response['MerchantRefNo']);
//print_r($order_info);
                

                $this->model_checkout_order->confirm($response['MerchantRefNo'], '5');

//                $this->data['responseMsg'] = '<h2>Thank you for your Order.</h2><br/><b>Payment Successful<br/> Your Order id - ' . $response['MerchantRefNo'] . '</b>';


               

                $this->load->model('account/guest');

                $order_id = $response['MerchantRefNo'];
//                $email = $response['BillingEmail'];
                $email= preg_replace('/\s/', '+', $response['BillingEmail']);
                $order_info = $this->model_account_guest->getOrder($order_id, $email, '');

                if ($order_info) {

                    $this->document->setTitle($this->language->get('text_order'));
                    $this->language->load('account/guest_history');
                    $this->data['breadcrumbs'] = array();

                    $this->data['breadcrumbs'][] = array(
                        'text' => $this->language->get('text_home'),
                        'href' => $this->url->link('common/home'),
                        'separator' => false
                    );


                    $this->data['heading_title'] = $this->language->get('text_order');

                    $this->data['text_order_detail'] = $this->language->get('text_order_detail');
                    $this->data['text_invoice_no'] = $this->language->get('text_invoice_no');
                    $this->data['text_order_id'] = $this->language->get('text_order_id');
                    $this->data['text_date_added'] = $this->language->get('text_date_added');
                    $this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
                    $this->data['text_shipping_address'] = $this->language->get('text_shipping_address');
                    $this->data['text_payment_method'] = $this->language->get('text_payment_method');
                    $this->data['text_payment_address'] = $this->language->get('text_payment_address');
                    $this->data['text_history'] = $this->language->get('text_history');
                    $this->data['text_comment'] = $this->language->get('text_comment');
                    $this->data['text_action'] = $this->language->get('text_action');
                    $this->data['text_selected'] = $this->language->get('text_selected');
                    $this->data['text_reorder'] = $this->language->get('text_reorder');
                    $this->data['text_return'] = $this->language->get('text_return');

                    $this->data['column_name'] = $this->language->get('column_name');
                    $this->data['column_model'] = $this->language->get('column_model');
                    $this->data['column_quantity'] = $this->language->get('column_quantity');
                    $this->data['column_price'] = $this->language->get('column_price');
                    $this->data['column_total'] = $this->language->get('column_total');
                    $this->data['column_date_added'] = $this->language->get('column_date_added');
                    $this->data['column_status'] = $this->language->get('column_status');
                    $this->data['column_comment'] = $this->language->get('column_comment');

                    $this->data['button_continue'] = $this->language->get('button_continue');

                    $this->data['action'] = $this->url->link('account/order/info', 'order_id=' . $order_id, 'SSL');

                    if ($order_info['invoice_no']) {
                        $this->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
                    } else {
                        $this->data['invoice_no'] = '';
                    }

                    $this->data['order_id'] = $order_id;
                    $this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

                    if ($order_info['shipping_address_format']) {
                        $format = $order_info['shipping_address_format'];
                    } else {
                        $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
                    }

                    $find = array(
                        '{firstname}',
                        '{lastname}',
                        '{company}',
                        '{address_1}',
                        '{address_2}',
                        '{city}',
                        '{postcode}',
                        '{zone}',
                        '{zone_code}',
                        '{country}'
                    );

                    $replace = array(
                        'firstname' => $order_info['shipping_firstname'],
                        'lastname' => $order_info['shipping_lastname'],
                        'company' => $order_info['shipping_company'],
                        'address_1' => $order_info['shipping_address_1'],
                        'address_2' => $order_info['shipping_address_2'],
                        'city' => $order_info['shipping_city'],
                        'postcode' => $order_info['shipping_postcode'],
                        'zone' => $order_info['shipping_zone'],
                        'zone_code' => $order_info['shipping_zone_code'],
                        'country' => $order_info['shipping_country']
                    );

                    $this->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

                    $this->data['shipping_method'] = $order_info['shipping_method'];

                    if ($order_info['payment_address_format']) {
                        $format = $order_info['payment_address_format'];
                    } else {
                        $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
                    }

                    $find = array(
                        '{firstname}',
                        '{lastname}',
                        '{company}',
                        '{address_1}',
                        '{address_2}',
                        '{city}',
                        '{postcode}',
                        '{zone}',
                        '{zone_code}',
                        '{country}'
                    );

                    $replace = array(
                        'firstname' => $order_info['payment_firstname'],
                        'lastname' => $order_info['payment_lastname'],
                        'company' => $order_info['payment_company'],
                        'address_1' => $order_info['payment_address_1'],
                        'address_2' => $order_info['payment_address_2'],
                        'city' => $order_info['payment_city'],
                        'postcode' => $order_info['payment_postcode'],
                        'zone' => $order_info['payment_zone'],
                        'zone_code' => $order_info['payment_zone_code'],
                        'country' => $order_info['payment_country']
                    );

                    $this->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

                    $this->data['payment_method'] = $order_info['payment_method'];

                    $this->data['products'] = array();

                    $products = $this->model_account_guest->getOrderProducts($order_id);

                    foreach ($products as $product) {
                        $option_data = array();

                        $options = $this->model_account_guest->getOrderOptions($order_id, $product['order_product_id']);

                        foreach ($options as $option) {
                            if ($option['type'] != 'file') {
                                $option_data[] = array(
                                    'name' => $option['name'],
                                    'value' => (strlen($option['value']) > 20 ? substr($option['value'], 0, 20) . '..' : $option['value']),
                                );
                            } else {
                                $filename = substr($option['value'], 0, strrpos($option['value'], '.'));

                                $option_data[] = array(
                                    'name' => $option['name'],
                                    'value' => (strlen($filename) > 20 ? substr($filename, 0, 20) . '..' : $filename)
                                );
                            }
                        }

                        $this->data['products'][] = array(
                            'order_product_id' => $product['order_product_id'],
                            'name' => $product['name'],
                            'model' => $product['model'],
                            'option' => $option_data,
                            'quantity' => $product['quantity'],
                            'price' => $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']),
                            'total' => $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value']),
                            'selected' => isset($this->request->post['selected']) && in_array($result['order_product_id'], $this->request->post['selected'])
                        );
                    }

                    $this->data['totals'] = $this->model_account_guest->getOrderTotals($order_id);

                    $this->data['comment'] = $order_info['comment'];

                    $this->data['histories'] = array();

                    $results = $this->model_account_guest->getOrderHistories($order_id);

                    foreach ($results as $result) {
                        $this->data['histories'][] = array(
                            'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                            'status' => $result['status'],
                            'comment' => nl2br($result['comment'])
                        );
                    }
                    
                    $this->data['continue'] = $this->url->link('account/order', '', 'SSL');

                    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/guest_history.tpl')) {
                        $this->template = $this->config->get('config_template') . '/template/account/guest_history.tpl';
                    } else {
                        $this->template = 'default/template/account/guest_history.tpl';
                    }

                    $this->children = array(
                        'common/column_left',
                        'common/column_right',
                        'common/content_top',
                        'common/content_bottom',
                        'common/footer',
                        'common/header'
                    );

                    $this->response->setOutput($this->render());
                } else{
                    echo 'into else';
                }
                } else {


                    $this->model_checkout_order->confirm($response['MerchantRefNo'], '10');
                    $this->data['responseMsg'] = '<h2>Sorry, Try Again !! </h2><br/><b>Payment Failed</b>';
                    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/response.tpl')) {
                        $this->template = $this->config->get('config_template') . '/template/common/response.tpl';
                    } else {
                        $this->template = 'default/template/common/response.tpl';
                    }

                    $this->children = array(
                        'common/header',
                        'common/footer',
                        'common/column_left',
                        'common/column_right'
                    );
                    $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
                    }

                
            }
        }
    }

?>
