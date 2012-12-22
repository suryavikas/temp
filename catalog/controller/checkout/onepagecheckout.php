<?php

class ControllerCheckoutOnePageCheckout extends Controller {

    private $countryCode = 99;

    public function pincode() {
        $json = array();

        $this->load->model('localisation/pincodes');

        $pincode = $this->model_localisation_pincodes->getPincodeDetails($this->request->get['pincode']);

        if ($pincode and sizeof($pincode) > 0) {
//            $this->load->model('localisation/zone');
            //city_id, city_name, location, Z.zone_id, Z.name as state, C.country_id, C.name as country, courier_provider
            $json = array(
                'country_id' => $pincode['country_id'],
                'country' => $pincode['country'],
                'state' => $pincode['state'],
                'state_id' => $pincode['state_id'],
                'location' => $pincode['location'],
                'city_name' => $pincode['city_name'],
                'city_id' => $pincode['city_id'],
                'courier_provider' => $pincode['courier_provider'],
                'pin_code' => $pincode['pin_code']
            );
            $this->session->data['pincode_based_details'] = $json;
        } else {
            $this->language->load('checkout/onepagecheckout');
            $json = array(
                'error' => $this->language->get('error_no_shipping_to_this_pincode')
            );
        }

        $this->response->setOutput(json_encode($json));
    }

    public function index() {
        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $this->redirect($this->url->link('checkout/cart'));
        }

        //Populating states list
        $this->load->model('localisation/zone');

        $zones = $this->model_localisation_zone->getZonesByCountryId($this->countryCode);
//        print_r($zones);
//        die;

        $this->data['zones'] = $zones;
        // Validate minimum quantity requirments.
        $products = $this->cart->getProducts();
        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $this->redirect($this->url->link('checkout/cart'));
            }
        }
        //Setting default shipping
        $quote_data = array();



        $this->language->load('checkout/onepagecheckout');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_cart'),
            'href' => $this->url->link('checkout/cart'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('checkout/checkout', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_checkout_option'] = $this->language->get('text_checkout_option');
        $this->data['text_checkout_account'] = $this->language->get('text_checkout_account');
        $this->data['text_checkout_payment_address'] = $this->language->get('text_checkout_payment_address');
        $this->data['text_checkout_shipping_address'] = $this->language->get('text_checkout_shipping_address');
        $this->data['text_checkout_shipping_method'] = $this->language->get('text_checkout_shipping_method');
        $this->data['text_checkout_payment_method'] = $this->language->get('text_checkout_payment_method');
        $this->data['text_checkout_confirm'] = $this->language->get('text_checkout_confirm');
        $this->data['text_modify'] = $this->language->get('text_modify');
        $this->data['error_no_shipping_to_this_pincode'] = $this->language->get('error_no_shipping_to_this_pincode');
        $this->data['text_modal_title'] = $this->language->get('text_modal_title');
        $this->data['text_button'] = $this->language->get('text_button');


        $this->data['logged'] = $this->customer->isLogged();
        $this->data['shipping_required'] = $this->cart->hasShipping();

        ////////////////////////////////////////////////////////FIRST PANE ///////////////////////////////////////////////////////////////
        $this->data['text_new_customer'] = $this->language->get('text_new_customer');
        $this->data['text_returning_customer'] = $this->language->get('text_returning_customer');
        $this->data['text_checkout'] = $this->language->get('text_checkout');
        $this->data['text_register'] = $this->language->get('text_register');
        $this->data['text_guest'] = $this->language->get('text_guest');
        $this->data['text_i_am_returning_customer'] = $this->language->get('text_i_am_returning_customer');
        $this->data['text_register_account'] = $this->language->get('text_register_account');
        $this->data['text_forgotten'] = $this->language->get('text_forgotten');

        $this->data['entry_email'] = $this->language->get('entry_email');
        $this->data['entry_password'] = $this->language->get('entry_password');
        $this->data['error_email'] = $this->language->get('error_email');
        $this->data['error_password_empty'] = $this->language->get('error_password_empty');
        $this->data['text_login_success'] = sprintf($this->language->get('text_login_success'), $this->url->link('account/logout'));
        $this->data['text_welcome'] = sprintf($this->language->get('text_welcome'), $this->url->link('account/register', '', 'SSL'), $this->url->link('account/forgotten', '', 'SSL'));
        $this->data['text_option'] = $this->language->get('text_option');
        ////////////////////////////////////////////////////////SECOND PANE//////////////////////////////////////////////////////////////
        $this->data['text_select'] = $this->language->get('text_select');
        $this->data['text_none'] = $this->language->get('text_none');
        $this->data['text_your_details'] = $this->language->get('text_your_details');
        $this->data['text_your_account'] = $this->language->get('text_your_account');
        $this->data['text_your_address'] = $this->language->get('text_your_address');

        $this->data['entry_firstname'] = $this->language->get('entry_firstname');
        $this->data['entry_lastname'] = $this->language->get('entry_lastname');
        $this->data['entry_email'] = $this->language->get('entry_email');
        $this->data['entry_telephone'] = $this->language->get('entry_telephone');
        $this->data['entry_fax'] = $this->language->get('entry_fax');
        $this->data['entry_company'] = $this->language->get('entry_company');
        $this->data['entry_customer_group'] = $this->language->get('entry_customer_group');
        $this->data['entry_company_id'] = $this->language->get('entry_company_id');
        $this->data['entry_tax_id'] = $this->language->get('entry_tax_id');
        $this->data['entry_address_1'] = $this->language->get('entry_address_1');
        $this->data['entry_address_2'] = $this->language->get('entry_address_2');
        $this->data['entry_postcode'] = $this->language->get('entry_postcode');
        $this->data['entry_city'] = $this->language->get('entry_city');
        $this->data['entry_country'] = $this->language->get('entry_country');
        $this->data['entry_zone'] = $this->language->get('entry_zone');
        $this->data['entry_shipping'] = $this->language->get('entry_shipping');

        $this->data['button_continue'] = $this->language->get('button_continue');

        if (isset($this->session->data['guest']['firstname'])) {
            $this->data['firstname'] = $this->session->data['guest']['firstname'];
        } else {
            $this->data['firstname'] = '';
        }

        if (isset($this->session->data['guest']['lastname'])) {
            $this->data['lastname'] = $this->session->data['guest']['lastname'];
        } else {
            $this->data['lastname'] = '';
        }

        if (isset($this->session->data['guest']['email'])) {
            $this->data['email'] = $this->session->data['guest']['email'];
        } else {
            $this->data['email'] = '';
        }

        if (isset($this->session->data['guest']['telephone'])) {
            $this->data['telephone'] = $this->session->data['guest']['telephone'];
        } else {
            $this->data['telephone'] = '';
        }

        if (isset($this->session->data['guest']['fax'])) {
            $this->data['fax'] = $this->session->data['guest']['fax'];
        } else {
            $this->data['fax'] = '';
        }

        if (isset($this->session->data['guest']['payment']['company'])) {
            $this->data['company'] = $this->session->data['guest']['payment']['company'];
        } else {
            $this->data['company'] = '';
        }


        $this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['button_login'] = $this->language->get('button_login');

        $this->data['guest_checkout'] = ($this->config->get('config_guest_checkout') && !$this->config->get('config_customer_price') && !$this->cart->hasDownload());

        if (isset($this->session->data['account'])) {
            $this->data['account'] = $this->session->data['account'];
        } else {
            $this->data['account'] = 'register';
        }

        //Second pane guest checkout
        $this->load->model('account/customer_group');
        $this->data['customer_groups'] = array();

        if (is_array($this->config->get('config_customer_group_display'))) {
            $customer_groups = $this->model_account_customer_group->getCustomerGroups();

            foreach ($customer_groups as $customer_group) {
                if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
                    $this->data['customer_groups'][] = $customer_group;
                }
            }
        }

        if (isset($this->session->data['guest']['customer_group_id'])) {
            $this->data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
        } else {
            $this->data['customer_group_id'] = $this->config->get('config_customer_group_id');
        }

        // Company ID
        if (isset($this->session->data['guest']['payment']['company_id'])) {
            $this->data['company_id'] = $this->session->data['guest']['payment']['company_id'];
        } else {
            $this->data['company_id'] = '';
        }

        // Tax ID
        if (isset($this->session->data['guest']['payment']['tax_id'])) {
            $this->data['tax_id'] = $this->session->data['guest']['payment']['tax_id'];
        } else {
            $this->data['tax_id'] = '';
        }

        if (isset($this->session->data['guest']['payment']['address_1'])) {
            $this->data['address_1'] = $this->session->data['guest']['payment']['address_1'];
        } else {
            $this->data['address_1'] = '';
        }

        if (isset($this->session->data['guest']['payment']['address_2'])) {
            $this->data['address_2'] = $this->session->data['guest']['payment']['address_2'];
        } else {
            $this->data['address_2'] = '';
        }

        if (isset($this->session->data['guest']['payment']['postcode'])) {
            $this->data['postcode'] = $this->session->data['guest']['payment']['postcode'];
        } elseif (isset($this->session->data['shipping_postcode'])) {
            $this->data['postcode'] = $this->session->data['shipping_postcode'];
        } else {
            $this->data['postcode'] = '';
        }

        if (isset($this->session->data['guest']['payment']['city'])) {
            $this->data['city'] = $this->session->data['guest']['payment']['city'];
        } else {
            $this->data['city'] = '';
        }

        if (isset($this->session->data['guest']['payment']['country_id'])) {
            $this->data['country_id'] = $this->session->data['guest']['payment']['country_id'];
        } elseif (isset($this->session->data['shipping_country_id'])) {
            $this->data['country_id'] = $this->session->data['shipping_country_id'];
        } else {
            $this->data['country_id'] = $this->config->get('config_country_id');
        }

        if (isset($this->session->data['guest']['payment']['zone_id'])) {
            $this->data['zone_id'] = $this->session->data['guest']['payment']['zone_id'];
        } elseif (isset($this->session->data['shipping_zone_id'])) {
            $this->data['zone_id'] = $this->session->data['shipping_zone_id'];
        } else {
            $this->data['zone_id'] = '';
        }

        $this->load->model('localisation/country');

        $this->data['countries'] = $this->model_localisation_country->getCountries();

        $this->data['shipping_required'] = $this->cart->hasShipping();

        if (isset($this->session->data['guest']['shipping_address'])) {
            $this->data['shipping_address'] = $this->session->data['guest']['shipping_address'];
        } else {
            $this->data['shipping_address'] = true;
        }

        $this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
        ////////////////////////////////////////////////////////FIRST PANE ///////////////////////////////////////////////////////////////
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/onepagecheckout.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/checkout/onepagecheckout.tpl';
        } else {
            $this->template = 'default/template/checkout/onepagecheckout.tpl';
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
    }

    private function set_default_shipping() {
        $this->load->model('setting/extension');

        $results = $this->model_setting_extension->getExtensions('shipping');

//        $shipping_address = $this->session->data['guest']['shipping'];
        if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
            $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
        } elseif (isset($this->session->data['guest'])) {
            $shipping_address = $this->session->data['guest']['shipping'];
        }

        $quote_data = array();
        foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                $this->load->model('shipping/' . $result['code']);

                $quote = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address);

                if ($quote) {
                    $quote_data[$result['code']] = array(
                        'title' => $quote['title'],
                        'quote' => $quote['quote'],
                        'sort_order' => $quote['sort_order'],
                        'error' => $quote['error']
                    );
                }
            }
        }

        $sort_order = array();

//Always choose the first one and then leave rest
        foreach ($quote_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
            $this->session->data['shipping_method'] = $quote_data[$key]['quote'][$key];
            break;
        }

//        array_multisort($sort_order, SORT_ASC, $quote_data);
        $this->session->data['shipping_methods'] = $quote_data;

        $json = array();
        if (sizeof($quote_data) <= 0) {
            $this->language->load('checkout/onepagecheckout');
            $json['error'] = $this->language->get('error_shipping');
        }


        $this->response->setOutput(json_encode($json));
    }

    private function validate_shipping() {
        $this->language->load('checkout/onepagecheckout');

        $json = array();

        // Validate if customer is logged in.
        if ($this->customer->isLogged()) {
            $json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart');
        }

        // Check if guest checkout is avaliable.
        if (!$this->config->get('config_guest_checkout') || $this->config->get('config_customer_price') || $this->cart->hasDownload()) {
            $json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
        }
        if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
            $customer_group_id = $this->request->post['customer_group_id'];
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }
        // Customer Group
        $this->load->model('account/customer_group');
        $customer_group = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

        if ($customer_group) {
            // Company ID
            if ($customer_group['company_id_display'] && $customer_group['company_id_required'] && empty($this->request->post['company_id'])) {
                $json['error']['company_id'] = $this->language->get('error_company_id');
            }

            // Tax ID
            if ($customer_group['tax_id_display'] && $customer_group['tax_id_required'] && empty($this->request->post['tax_id'])) {
                $json['error']['tax_id'] = $this->language->get('error_tax_id');
            }
        }

        if (!$json) {
            $this->session->data['guest']['customer_group_id'] = $customer_group_id;
            $this->session->data['guest']['firstname'] = $this->request->post['firstname'];
            $this->session->data['guest']['lastname'] = $this->request->post['lastname'];
            $this->session->data['guest']['email'] = $this->request->post['email'];
            $this->session->data['guest']['telephone'] = $this->request->post['telephone'];
            $this->session->data['guest']['fax'] = $this->request->post['fax'];

            $this->session->data['guest']['payment']['firstname'] = $this->request->post['firstname'];
            $this->session->data['guest']['payment']['lastname'] = $this->request->post['lastname'];
            $this->session->data['guest']['payment']['company'] = $this->request->post['company'];
            $this->session->data['guest']['payment']['company_id'] = $this->request->post['company_id'];
            $this->session->data['guest']['payment']['tax_id'] = $this->request->post['tax_id'];
            $this->session->data['guest']['payment']['address_1'] = $this->request->post['address_1'];
            $this->session->data['guest']['payment']['address_2'] = $this->request->post['address_2'];
            $this->session->data['guest']['payment']['postcode'] = $this->request->post['postcode'];
            $this->session->data['guest']['payment']['city'] = $this->request->post['city'];
            //Added guard rail to get details from server side code only
            $this->request->post['country_id'] = $this->request->post['country_id'] ? '' : $this->session->data['pincode_based_details']['country_id'];
            $this->session->data['guest']['payment']['country_id'] = $this->request->post['country_id'];
            $this->session->data['guest']['payment']['zone_id'] = $this->request->post['zone_id'];

            $this->load->model('localisation/country');

            $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

            if ($country_info) {
                $this->session->data['guest']['payment']['country'] = $country_info['name'];
                $this->session->data['guest']['payment']['iso_code_2'] = $country_info['iso_code_2'];
                $this->session->data['guest']['payment']['iso_code_3'] = $country_info['iso_code_3'];
                $this->session->data['guest']['payment']['address_format'] = $country_info['address_format'];
            } else {
                $this->session->data['guest']['payment']['country'] = '';
                $this->session->data['guest']['payment']['iso_code_2'] = '';
                $this->session->data['guest']['payment']['iso_code_3'] = '';
                $this->session->data['guest']['payment']['address_format'] = '';
            }

            $this->load->model('localisation/zone');

            $zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

            if ($zone_info) {
                $this->session->data['guest']['payment']['zone'] = $zone_info['name'];
                $this->session->data['guest']['payment']['zone_code'] = $zone_info['code'];
            } else {
                $this->session->data['guest']['payment']['zone'] = '';
                $this->session->data['guest']['payment']['zone_code'] = '';
            }

            if (!empty($this->request->post['shipping_address_guest'])) {
                $this->session->data['guest']['shipping_address'] = true;
            } else {
                $this->session->data['guest']['shipping_address'] = false;
            }

            // Default Payment Address
            $this->session->data['payment_country_id'] = $this->request->post['country_id'];
            $this->session->data['payment_zone_id'] = $this->request->post['zone_id'];

            if ($this->session->data['guest']['shipping_address']) {
                $this->session->data['guest']['shipping']['firstname'] = $this->request->post['firstname'];
                $this->session->data['guest']['shipping']['lastname'] = $this->request->post['lastname'];
                $this->session->data['guest']['shipping']['company'] = $this->request->post['company'];
                $this->session->data['guest']['shipping']['address_1'] = $this->request->post['address_1'];
                $this->session->data['guest']['shipping']['address_2'] = $this->request->post['address_2'];
                $this->session->data['guest']['shipping']['postcode'] = $this->request->post['postcode'];
                $this->session->data['guest']['shipping']['city'] = $this->request->post['city'];
                $this->session->data['guest']['shipping']['country_id'] = $this->request->post['country_id'];
                $this->session->data['guest']['shipping']['zone_id'] = $this->request->post['zone_id'];

                if ($country_info) {
                    $this->session->data['guest']['shipping']['country'] = $country_info['name'];
                    $this->session->data['guest']['shipping']['iso_code_2'] = $country_info['iso_code_2'];
                    $this->session->data['guest']['shipping']['iso_code_3'] = $country_info['iso_code_3'];
                    $this->session->data['guest']['shipping']['address_format'] = $country_info['address_format'];
                } else {
                    $this->session->data['guest']['shipping']['country'] = '';
                    $this->session->data['guest']['shipping']['iso_code_2'] = '';
                    $this->session->data['guest']['shipping']['iso_code_3'] = '';
                    $this->session->data['guest']['shipping']['address_format'] = '';
                }

                if ($zone_info) {
                    $this->session->data['guest']['shipping']['zone'] = $zone_info['name'];
                    $this->session->data['guest']['shipping']['zone_code'] = $zone_info['code'];
                } else {
                    $this->session->data['guest']['shipping']['zone'] = '';
                    $this->session->data['guest']['shipping']['zone_code'] = '';
                }

                // Default Shipping Address
                $this->session->data['shipping_country_id'] = $this->request->post['country_id'];
                $this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
                $this->session->data['shipping_postcode'] = $this->request->post['postcode'];
            }

            $this->session->data['account'] = 'guest';
        }
        return $json;
//        $this->response->setOutput(json_encode($json));
    }

    public function set_payment_shipping() {

        ////////////////////////////////////////////Validate Form Input///////////////////////////////////////////////////////////////
        $json = $this->guest_checkout_form_validate();

        if (!empty($json)) {
            $this->response->setOutput(json_encode($json));
            return;
        }
        ////////////////////////////////////////////Validate Form input///////////////////////////////////////////////////////////////
        ////////////////////////////////////////////Validate SHIPPING///////////////////////////////////////////////////////////////
        $json = $this->validate_shipping();
        if (!empty($json)) {
            $this->response->setOutput(json_encode($json));
            return;
        }
        ////////////////////////////////////////////Validate SHIPPING///////////////////////////////////////////////////////////////
        ////////////////////////////////////////////SET DEFAULT SHIPPING///////////////////////////////////////////////////////////////
        $this->set_default_shipping();
        ////////////////////////////////////////////SET DEFAULT SHIPPING///////////////////////////////////////////////////////////////


        $this->language->load('checkout/onepagecheckout');

        $this->load->model('account/address');

        if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
            $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
        } elseif (isset($this->session->data['guest'])) {
            $payment_address = $this->session->data['guest']['payment'];
        }

        if (!empty($payment_address)) {
            // Totals
            $total_data = array();
            $total = 0;
            $taxes = $this->cart->getTaxes();

            $this->load->model('setting/extension');

            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('total/' . $result['code']);

                    $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
                }
            }

            // Payment Methods
            $method_data = array();

            $this->load->model('setting/extension');

            $results = $this->model_setting_extension->getExtensions('payment');

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('payment/' . $result['code']);

                    $method = $this->{'model_payment_' . $result['code']}->getMethod($payment_address, $total);

                    if ($method) {
                        $method_data[$result['code']] = $method;
                    }
                }
            }

            $sort_order = array();

            foreach ($method_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $method_data);

            $this->session->data['payment_methods'] = $method_data;
        }

        $this->data['text_payment_method'] = $this->language->get('text_payment_method');
        $this->data['text_comments'] = $this->language->get('text_comments');

        $this->data['button_continue'] = $this->language->get('button_continue');

        if (empty($this->session->data['payment_methods'])) {
            $this->data['error_warning'] = sprintf($this->language->get('error_no_payment'), $this->url->link('information/contact'));
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['payment_methods'])) {
            $this->data['payment_methods'] = $this->session->data['payment_methods'];
        } else {
            $this->data['payment_methods'] = array();
        }

        if (isset($this->session->data['payment_method']['code'])) {
            $this->data['code'] = $this->session->data['payment_method']['code'];
        } else {
            $this->data['code'] = '';
        }

        if (isset($this->session->data['comment'])) {
            $this->data['comment'] = $this->session->data['comment'];
        } else {
            $this->data['comment'] = '';
        }

        if ($this->config->get('config_checkout_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

            if ($information_info) {
                $this->data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);
            } else {
                $this->data['text_agree'] = '';
            }
        } else {
            $this->data['text_agree'] = '';
        }

        if (isset($this->session->data['agree'])) {
            $this->data['agree'] = $this->session->data['agree'];
        } else {
            $this->data['agree'] = '';
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/one_page_payment.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/checkout/one_page_payment.tpl';
        } else {
            $this->template = 'default/template/checkout/payment_method.tpl';
        }

        $this->response->setOutput($this->render());
    }

    public function validate_payment() {

        $this->language->load('checkout/onepagecheckout');

        $json = array();

        // Validate if payment address has been set.
        $this->load->model('account/address');

        if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
            $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
        } elseif (isset($this->session->data['guest'])) {
            $payment_address = $this->session->data['guest']['payment'];
        }

        if (empty($payment_address)) {
            $json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {

            $json['redirect'] = $this->url->link('checkout/cart');
        }

        // Validate minimum quantity requirments.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $json['redirect'] = $this->url->link('checkout/cart');

                break;
            }
        }

        if (!$json) {
            if (!isset($this->request->post['payment_method'])) {
                $json['error']['warning'] = $this->language->get('error_payment');
            } else {
                if (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
                    $json['error']['warning'] = $this->language->get('error_payment');
                }
            }

            if ($this->config->get('config_checkout_id')) {
                $this->load->model('catalog/information');

                $information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

//				if ($information_info && !isset($this->request->post['agree'])) {
//					$json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
//				}
            }

            if (!$json) {
                $this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];

                $this->session->data['comment'] = strip_tags($this->request->post['comment']);
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function do_payment() {
        $redirect = '';

        if ($this->cart->hasShipping()) {
            // Validate if shipping address has been set.
            $this->load->model('account/address');

            if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
                $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
            } elseif (isset($this->session->data['guest'])) {
                $shipping_address = $this->session->data['guest']['shipping'];
            }

            if (empty($shipping_address)) {
                $redirect = $this->url->link('checkout/checkout', '', 'SSL');
            }

            // Validate if shipping method has been set.
            if (!isset($this->session->data['shipping_method'])) {
                echo"NO SHIPPING METHOD";
                $redirect = $this->url->link('checkout/checkout', '', 'SSL');
            }
        } else {
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
        }

        // Validate if payment address has been set.
        $this->load->model('account/address');

        if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
            $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
        } elseif (isset($this->session->data['guest'])) {
            $payment_address = $this->session->data['guest']['payment'];
        }

        if (empty($payment_address)) {
            $redirect = $this->url->link('checkout/checkout', '', 'SSL');
        }

        // Validate if payment method has been set.
        if (!isset($this->session->data['payment_method'])) {
            $redirect = $this->url->link('checkout/checkout', '', 'SSL');
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $redirect = $this->url->link('checkout/cart');
        }

        // Validate minimum quantity requirments.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $redirect = $this->url->link('checkout/cart');

                break;
            }
        }

        if (!$redirect) {
            $total_data = array();
            $total = 0;
            $taxes = $this->cart->getTaxes();

            $this->load->model('setting/extension');

            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('total/' . $result['code']);

                    $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
                }
            }

            $sort_order = array();

            foreach ($total_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $total_data);

            $this->language->load('checkout/onepagecheckout');

            $data = array();

            $data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
            $data['store_id'] = $this->config->get('config_store_id');
            $data['store_name'] = $this->config->get('config_name');

            if ($data['store_id']) {
                $data['store_url'] = $this->config->get('config_url');
            } else {
                $data['store_url'] = HTTP_SERVER;
            }

            if ($this->customer->isLogged()) {
                $data['customer_id'] = $this->customer->getId();
                $data['customer_group_id'] = $this->customer->getCustomerGroupId();
                $data['firstname'] = $this->customer->getFirstName();
                $data['lastname'] = $this->customer->getLastName();
                $data['email'] = $this->customer->getEmail();
                $data['telephone'] = $this->customer->getTelephone();
                $data['fax'] = $this->customer->getFax();

                $this->load->model('account/address');

                $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
            } elseif (isset($this->session->data['guest'])) {
                $data['customer_id'] = 0;
                $data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
                $data['firstname'] = $this->session->data['guest']['firstname'];
                $data['lastname'] = $this->session->data['guest']['lastname'];
                $data['email'] = $this->session->data['guest']['email'];
                $data['telephone'] = $this->session->data['guest']['telephone'];
                $data['fax'] = $this->session->data['guest']['fax'];

                $payment_address = $this->session->data['guest']['payment'];
            }

            $data['payment_firstname'] = $payment_address['firstname'];
            $data['payment_lastname'] = $payment_address['lastname'];
            $data['payment_company'] = $payment_address['company'];
            $data['payment_company_id'] = $payment_address['company_id'];
            $data['payment_tax_id'] = $payment_address['tax_id'];
            $data['payment_address_1'] = $payment_address['address_1'];
            $data['payment_address_2'] = $payment_address['address_2'];
            $data['payment_city'] = $payment_address['city'];
            $data['payment_postcode'] = $payment_address['postcode'];
            $data['payment_zone'] = $payment_address['zone'];
            $data['payment_zone_id'] = $payment_address['zone_id'];
            $data['payment_country'] = $payment_address['country'];
            $data['payment_country_id'] = $payment_address['country_id'];
            $data['payment_address_format'] = $payment_address['address_format'];

            if (isset($this->session->data['payment_method']['title'])) {
                $data['payment_method'] = $this->session->data['payment_method']['title'];
            } else {
                $data['payment_method'] = '';
            }

            if (isset($this->session->data['payment_method']['code'])) {
                $data['payment_code'] = $this->session->data['payment_method']['code'];
            } else {
                $data['payment_code'] = '';
            }

            if ($this->cart->hasShipping()) {
                if ($this->customer->isLogged()) {
                    $this->load->model('account/address');

                    $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
                } elseif (isset($this->session->data['guest'])) {
                    $shipping_address = $this->session->data['guest']['shipping'];
                }

                $data['shipping_firstname'] = $shipping_address['firstname'];
                $data['shipping_lastname'] = $shipping_address['lastname'];
                $data['shipping_company'] = $shipping_address['company'];
                $data['shipping_address_1'] = $shipping_address['address_1'];
                $data['shipping_address_2'] = $shipping_address['address_2'];
                $data['shipping_city'] = $shipping_address['city'];
                $data['shipping_postcode'] = $shipping_address['postcode'];
                $data['shipping_zone'] = $shipping_address['zone'];
                $data['shipping_zone_id'] = $shipping_address['zone_id'];
                $data['shipping_country'] = $shipping_address['country'];
                $data['shipping_country_id'] = $shipping_address['country_id'];
                $data['shipping_address_format'] = $shipping_address['address_format'];

                if (isset($this->session->data['shipping_method']['title'])) {
                    $data['shipping_method'] = $this->session->data['shipping_method']['title'];
                } else {
                    $data['shipping_method'] = '';
                }

                if (isset($this->session->data['shipping_method']['code'])) {
                    $data['shipping_code'] = $this->session->data['shipping_method']['code'];
                } else {
                    $data['shipping_code'] = '';
                }
            } else {
                $data['shipping_firstname'] = '';
                $data['shipping_lastname'] = '';
                $data['shipping_company'] = '';
                $data['shipping_address_1'] = '';
                $data['shipping_address_2'] = '';
                $data['shipping_city'] = '';
                $data['shipping_postcode'] = '';
                $data['shipping_zone'] = '';
                $data['shipping_zone_id'] = '';
                $data['shipping_country'] = '';
                $data['shipping_country_id'] = '';
                $data['shipping_address_format'] = '';
                $data['shipping_method'] = '';
                $data['shipping_code'] = '';
            }

            $product_data = array();

            foreach ($this->cart->getProducts() as $product) {
                $option_data = array();

                foreach ($product['option'] as $option) {
                    if ($option['type'] != 'file') {
                        $value = $option['option_value'];
                    } else {
                        $value = $this->encryption->decrypt($option['option_value']);
                    }

                    $option_data[] = array(
                        'product_option_id' => $option['product_option_id'],
                        'product_option_value_id' => $option['product_option_value_id'],
                        'option_id' => $option['option_id'],
                        'option_value_id' => $option['option_value_id'],
                        'name' => $option['name'],
                        'value' => $value,
                        'type' => $option['type']
                    );
                }

                $product_data[] = array(
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $option_data,
                    'download' => $product['download'],
                    'quantity' => $product['quantity'],
                    'subtract' => $product['subtract'],
                    'price' => $product['price'],
                    'total' => $product['total'],
                    'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
                    'reward' => $product['reward']
                );
            }

            // Gift Voucher
            $voucher_data = array();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $voucher) {
                    $voucher_data[] = array(
                        'description' => $voucher['description'],
                        'code' => substr(md5(mt_rand()), 0, 10),
                        'to_name' => $voucher['to_name'],
                        'to_email' => $voucher['to_email'],
                        'from_name' => $voucher['from_name'],
                        'from_email' => $voucher['from_email'],
                        'voucher_theme_id' => $voucher['voucher_theme_id'],
                        'message' => $voucher['message'],
                        'amount' => $voucher['amount']
                    );
                }
            }

            $data['products'] = $product_data;
            $data['vouchers'] = $voucher_data;
            $data['totals'] = $total_data;
            $data['comment'] = $this->session->data['comment'];
            $data['total'] = $total;

            if (isset($this->request->cookie['tracking'])) {
                $this->load->model('affiliate/affiliate');

                $affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);
                $subtotal = $this->cart->getSubTotal();

                if ($affiliate_info) {
                    $data['affiliate_id'] = $affiliate_info['affiliate_id'];
                    $data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
                } else {
                    $data['affiliate_id'] = 0;
                    $data['commission'] = 0;
                }
            } else {
                $data['affiliate_id'] = 0;
                $data['commission'] = 0;
            }

            $data['language_id'] = $this->config->get('config_language_id');
            $data['currency_id'] = $this->currency->getId();
            $data['currency_code'] = $this->currency->getCode();
            $data['currency_value'] = $this->currency->getValue($this->currency->getCode());
            $data['ip'] = $this->request->server['REMOTE_ADDR'];

            if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
                $data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
            } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
                $data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
            } else {
                $data['forwarded_ip'] = '';
            }

            if (isset($this->request->server['HTTP_USER_AGENT'])) {
                $data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
            } else {
                $data['user_agent'] = '';
            }

            if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
                $data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
            } else {
                $data['accept_language'] = '';
            }

            $this->load->model('checkout/order');

            $this->session->data['order_id'] = $this->model_checkout_order->addOrder($data);

            $this->data['column_name'] = $this->language->get('column_name');
            $this->data['column_model'] = $this->language->get('column_model');
            $this->data['column_quantity'] = $this->language->get('column_quantity');
            $this->data['column_price'] = $this->language->get('column_price');
            $this->data['column_total'] = $this->language->get('column_total');

            $this->data['products'] = array();

            foreach ($this->cart->getProducts() as $product) {
                $option_data = array();

                foreach ($product['option'] as $option) {
                    if ($option['type'] != 'file') {
                        $value = $option['option_value'];
                    } else {
                        $filename = $this->encryption->decrypt($option['option_value']);

                        $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                    }

                    $option_data[] = array(
                        'name' => $option['name'],
                        'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                    );
                }

                $this->data['products'][] = array(
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $option_data,
                    'quantity' => $product['quantity'],
                    'subtract' => $product['subtract'],
                    'price' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
                    'total' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']),
                    'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'])
                );
            }

            // Gift Voucher
            $this->data['vouchers'] = array();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $voucher) {
                    $this->data['vouchers'][] = array(
                        'description' => $voucher['description'],
                        'amount' => $this->currency->format($voucher['amount'])
                    );
                }
            }

            $this->data['totals'] = $total_data;

            $this->data['payment'] = $this->getChild('payment/' . $this->session->data['payment_method']['code']);
        } else {
            $this->data['redirect'] = $redirect;
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/do_payment.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/checkout/do_payment.tpl';
        } else {
            $this->template = 'default/template/checkout/confirm.tpl';
        }
//
        $this->response->setOutput($this->render());
    }

    public function loggedin_user_shipping_address() {
        $this->language->load('checkout/onepagecheckout');

        $this->data['text_address_existing'] = $this->language->get('text_address_existing');
        $this->data['text_address_new'] = $this->language->get('text_address_new');
        $this->data['text_select'] = $this->language->get('text_select');
        $this->data['text_none'] = $this->language->get('text_none');

        $this->data['entry_firstname'] = $this->language->get('entry_firstname');
        $this->data['entry_lastname'] = $this->language->get('entry_lastname');
        $this->data['entry_company'] = $this->language->get('entry_company');
        $this->data['entry_address_1'] = $this->language->get('entry_address_1');
        $this->data['entry_address_2'] = $this->language->get('entry_address_2');
        $this->data['entry_postcode'] = $this->language->get('entry_postcode');
        $this->data['entry_city'] = $this->language->get('entry_city');
        $this->data['entry_country'] = $this->language->get('entry_country');
        $this->data['entry_zone'] = $this->language->get('entry_zone');
        $this->data['entry_telephone'] = $this->language->get('entry_telephone');
        $this->load->model('account/customer');
        $this->data['text_welcome_shipping'] = sprintf($this->language->get('text_welcome_shipping'), $this->customer->getFirstName());

        $this->data['entry_email'] = $this->language->get('entry_email');
        $this->data['entry_password'] = $this->language->get('entry_password');
        $this->data['error_email'] = $this->language->get('error_email');
        $this->data['error_password_empty'] = $this->language->get('error_password_empty');
        $this->data['text_login_success'] = sprintf($this->language->get('text_login_success'), $this->url->link('account/logout'));
        ////////////////////////////////////////////////////////SECOND PANE//////////////////////////////////////////////////////////////
        $this->data['text_select'] = $this->language->get('text_select');
        $this->data['text_none'] = $this->language->get('text_none');
        $this->data['text_your_details'] = $this->language->get('text_your_details');
        $this->data['text_your_account'] = $this->language->get('text_your_account');
        $this->data['text_your_address'] = $this->language->get('text_your_address');

        $this->load->model('account/customer_group');
        $this->data['customer_groups'] = array();

        if (is_array($this->config->get('config_customer_group_display'))) {
            $customer_groups = $this->model_account_customer_group->getCustomerGroups();

            foreach ($customer_groups as $customer_group) {
                if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
                    $this->data['customer_groups'][] = $customer_group;
                }
            }
        }


        $this->data['text_button'] = $this->language->get('text_button');

        //Populating states list
        $this->load->model('localisation/zone');

        $zones = $this->model_localisation_zone->getZonesByCountryId($this->countryCode);
//        print_r($zones);
//        die;

        $this->data['zones'] = $zones;

        if (isset($this->session->data['shipping_address_id'])) {
            $this->data['address_id'] = $this->session->data['shipping_address_id'];
        } else {
            $this->data['address_id'] = $this->customer->getAddressId();
        }

        $this->load->model('account/address');

        $this->data['addresses'] = $this->model_account_address->getAddresses();

        if (isset($this->session->data['shipping_postcode'])) {
            $this->data['postcode'] = $this->session->data['shipping_postcode'];
        } else {
            $this->data['postcode'] = '';
        }

        if (isset($this->session->data['shipping_country_id'])) {
            $this->data['country_id'] = $this->session->data['shipping_country_id'];
        } else {
            $this->data['country_id'] = $this->config->get('config_country_id');
        }

        if (isset($this->session->data['shipping_zone_id'])) {
            $this->data['zone_id'] = $this->session->data['shipping_zone_id'];
        } else {
            $this->data['zone_id'] = '';
        }

        $this->load->model('localisation/country');

        $this->data['countries'] = $this->model_localisation_country->getCountries();

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/one_page_shipping_address.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/checkout/one_page_shipping_address.tpl';
        } else {
            $this->template = 'default/template/checkout/shipping_address.tpl';
        }

        $this->response->setOutput($this->render());
    }

    public function loggedin_user_payment_method() {
        $this->language->load('checkout/onepagecheckout');

        $this->load->model('account/address');
        $payment_address = '';
        if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
            $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
        } elseif (isset($this->session->data['guest'])) {
            $payment_address = $this->session->data['guest']['payment'];
        }
        
        if (!empty($payment_address)) {
            // Totals
            $total_data = array();
            $total = 0;
            $taxes = $this->cart->getTaxes();

            $this->load->model('setting/extension');

            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('total/' . $result['code']);

                    $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
                }
            }

            // Payment Methods
            $method_data = array();

            $this->load->model('setting/extension');

            $results = $this->model_setting_extension->getExtensions('payment');

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('payment/' . $result['code']);

                    $method = $this->{'model_payment_' . $result['code']}->getMethod($payment_address, $total);

                    if ($method) {
                        $method_data[$result['code']] = $method;
                    }
                }
            }

            $sort_order = array();

            foreach ($method_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $method_data);

            $this->session->data['payment_methods'] = $method_data;
        }

        $this->data['text_payment_method'] = $this->language->get('text_payment_method');
        $this->data['text_comments'] = $this->language->get('text_comments');

        $this->data['text_button'] = $this->language->get('text_button');

        if (empty($this->session->data['payment_methods'])) {
            $this->data['error_warning'] = sprintf($this->language->get('error_no_payment'), $this->url->link('information/contact'));
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['payment_methods'])) {
            $this->data['payment_methods'] = $this->session->data['payment_methods'];
        } else {
            $this->data['payment_methods'] = array();
        }

        if (isset($this->session->data['payment_method']['code'])) {
            $this->data['code'] = $this->session->data['payment_method']['code'];
        } else {
            $this->data['code'] = '';
        }

        if (isset($this->session->data['comment'])) {
            $this->data['comment'] = $this->session->data['comment'];
        } else {
            $this->data['comment'] = '';
        }

        if ($this->config->get('config_checkout_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

            if ($information_info) {
                $this->data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);
            } else {
                $this->data['text_agree'] = '';
            }
        } else {
            $this->data['text_agree'] = '';
        }

        if (isset($this->session->data['agree'])) {
            $this->data['agree'] = $this->session->data['agree'];
        } else {
            $this->data['agree'] = '';
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/one_page_payment.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/checkout/one_page_payment.tpl';
        } else {
            $this->template = 'default/template/checkout/payment_method.tpl';
        }

        $this->response->setOutput($this->render());
    }

    public function loggedin_user_shipping_address_validate() {
        
        $this->language->load('checkout/onepagecheckout');

        $json = array();

        // Validate if customer is logged in.
        if (!$this->customer->isLogged()) {
            $json['redirect'] = $this->url->link('checkout/onepagecheckout', '', 'SSL');
        }

        // Validate if shipping is required. If not the customer should not have reached this page.
        if (!$this->cart->hasShipping()) {
            $json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart');
        }

        // Validate minimum quantity requirments.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $json['redirect'] = $this->url->link('checkout/cart');
                break;
            }
        }

        if (!$json) {            
            if ($this->request->post['shipping_address'] == 'existing') {
                $this->load->model('account/address');

                if (empty($this->request->post['address_id'])) {
                    $json['error']['warning'] = $this->language->get('error_address');
                } elseif (!in_array($this->request->post['address_id'], array_keys($this->model_account_address->getAddresses()))) {
                    $json['error']['warning'] = $this->language->get('error_address');
                }

                if (!$json) {                    
                    $this->session->data['shipping_address_id'] = $this->request->post['address_id'];

                    // Default Shipping Address
                    $this->load->model('account/address');

                    $address_info = $this->model_account_address->getAddress($this->request->post['address_id']);
                    
                    if ($address_info) {
                        $this->session->data['shipping_country_id'] = $address_info['country_id'];
                        $this->session->data['shipping_zone_id'] = $address_info['zone_id'];
                        $this->session->data['shipping_postcode'] = $address_info['postcode'];
                    } else {
                        unset($this->session->data['shipping_country_id']);
                        unset($this->session->data['shipping_zone_id']);
                        unset($this->session->data['shipping_postcode']);                        
                    }
                    
                    unset($this->session->data['shipping_method']);
                    unset($this->session->data['shipping_methods']);
                }
            } else if ($this->request->post['shipping_address'] == 'new') {

                if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
                    $json['error']['email'] = $this->language->get('error_email');
                }
                if (utf8_strlen($this->request->post['postcode']) != 6) {
                    $json['error']['postcode'] = $this->language->get('error_postcode');
                } else {
                    $this->load->model('localisation/pincodes');

                    $postcode = $this->model_localisation_pincodes->getPincodeDetails($this->request->post['postcode']);
                    if ($postcode and sizeof($postcode) > 0) {
                        //            $this->load->model('localisation/zone');
                        //city_id, city_name, location, Z.zone_id, Z.name as state, C.country_id, C.name as country, courier_provider
//                        $json = array(
//                            'country_id' => $pincode['country_id'],
//                            'country' => $pincode['country'],
//                            'state' => $pincode['state'],
//                            'state_id' => $pincode['state_id'],
//                            'location' => $pincode['location'],
//                            'city_name' => $pincode['city_name'],
//                            'city_id' => $pincode['city_id'],
//                            'courier_provider' => $pincode['courier_provider'],
//                            'pin_code' => $pincode['pin_code']
//                        );
                    } else {
                        $this->language->load('checkout/onepagecheckout');
                        $json['error']['postcode'] = $this->language->get('error_no_shipping_to_this_pincode');
                    }
                }
                if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
                    $json['error']['firstname'] = $this->language->get('error_firstname');
                }

                if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
                    $json['error']['lastname'] = $this->language->get('error_lastname');
                }

                if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
                    $json['error']['city'] = $this->language->get('error_city');
                }

                if ((utf8_strlen($this->request->post['zone_id']) < 2) || (utf8_strlen($this->request->post['zone_id']) > 128)) {
                    $json['error']['zone'] = $this->language->get('error_zone');
                }
                if (utf8_strlen($this->request->post['telephone']) != 10) {
                    $json['error']['telephone'] = $this->language->get('error_telephone');
//			}
                }
                
                if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
                    $json['error']['address_1'] = $this->language->get('error_address_1');
                }

                if (!$json) {
                    // Default Shipping Address
                    $this->load->model('account/address');

                    $this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($this->request->post);

                    $this->session->data['shipping_country_id'] = $this->request->post['country_id'];
                    $this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
                    $this->session->data['shipping_postcode'] = $this->request->post['postcode'];

                    $address_info = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);

                    if ($address_info) {
                        $this->session->data['shipping_country_id'] = $address_info['country_id'];
                        $this->session->data['shipping_zone_id'] = $address_info['zone_id'];
                        $this->session->data['shipping_postcode'] = $address_info['postcode'];
                    } else {
                        unset($this->session->data['shipping_country_id']);
                        unset($this->session->data['shipping_zone_id']);
                        unset($this->session->data['shipping_postcode']);
                    }
                    unset($this->session->data['shipping_method']);
                    unset($this->session->data['shipping_methods']);
                }
            } else{
                 $json['error']['warning'] = $this->language->get('error_address');
            }            
            if(sizeof($json) == 0) {             
                $this->session->data['payment_address_id'] = $this->session->data['shipping_address_id'];
                $this->set_default_shipping();
            }
        }
        $this->response->setOutput(json_encode($json));
    }

    public function guest_checkout_form_validate() {
        $this->language->load('checkout/onepagecheckout');
        $json = array();


        // Validate if customer is logged in.
        if ($this->customer->isLogged()) {
            $json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart');
        }

        // Validate minimum quantity requirments.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }
            if ($product['minimum'] > $product_total) {
                $json['redirect'] = $this->url->link('checkout/cart');
                break;
            }
        }
        // Check if guest checkout is avaliable.
        if (!$this->config->get('config_guest_checkout') || $this->config->get('config_customer_price') || $this->cart->hasDownload()) {
            $json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
            $json['error']['email'] = $this->language->get('error_email');
        }
        if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
            $json['error']['firstname'] = $this->language->get('error_firstname');
        }

        if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
            $json['error']['lastname'] = $this->language->get('error_lastname');
        }

        if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
            $json['error']['address_1'] = $this->language->get('error_address_1');
        }

        if (utf8_strlen($this->request->post['telephone']) != 10) {
            $json['error']['telephone'] = $this->language->get('error_telephone');
//			}
        }
        if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
            $json['error']['city'] = $this->language->get('error_city');
        }

        if ((utf8_strlen($this->request->post['zone_id']) < 2) || (utf8_strlen($this->request->post['zone_id']) > 128)) {
            $json['error']['zone'] = $this->language->get('error_zone');
        }

//			$this->load->model('localisation/country');
//
//			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

        if (utf8_strlen($this->request->post['postcode']) != 6) {
            $json['error']['postcode'] = $this->language->get('error_postcode');
        } else {
            $this->load->model('localisation/pincodes');

            $postcode = $this->model_localisation_pincodes->getPincodeDetails($this->request->post['postcode']);
            if ($postcode and sizeof($postcode) > 0) {
                //            $this->load->model('localisation/zone');
                //city_id, city_name, location, Z.zone_id, Z.name as state, C.country_id, C.name as country, courier_provider
//                        $json = array(
//                            'country_id' => $pincode['country_id'],
//                            'country' => $pincode['country'],
//                            'state' => $pincode['state'],
//                            'state_id' => $pincode['state_id'],
//                            'location' => $pincode['location'],
//                            'city_name' => $pincode['city_name'],
//                            'city_id' => $pincode['city_id'],
//                            'courier_provider' => $pincode['courier_provider'],
//                            'pin_code' => $pincode['pin_code']
//                        );
            } else {
                $this->language->load('checkout/onepagecheckout');
                $json['error']['postcode'] = $this->language->get('error_no_shipping_to_this_pincode');
            }
        }
        if (empty($json['error'])) {
            $this->session->data['guest']['shipping']['firstname'] = trim($this->request->post['firstname']);
            $this->session->data['guest']['shipping']['lastname'] = trim($this->request->post['lastname']);
            $this->session->data['guest']['shipping']['company'] = trim($this->request->post['company']);
            $this->session->data['guest']['shipping']['address_1'] = $this->request->post['address_1'];
            $this->session->data['guest']['shipping']['address_2'] = $this->request->post['address_2'];
            $this->session->data['guest']['shipping']['postcode'] = $this->request->post['postcode'];
            $this->session->data['guest']['shipping']['city'] = $this->request->post['city'];
            $this->session->data['guest']['shipping']['country_id'] = $this->request->post['country_id'];
            $this->session->data['guest']['shipping']['zone_id'] = $this->request->post['zone_id'];

            $this->load->model('localisation/country');

            $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

            if ($country_info) {
                $this->session->data['guest']['shipping']['country'] = $country_info['name'];
                $this->session->data['guest']['shipping']['iso_code_2'] = $country_info['iso_code_2'];
                $this->session->data['guest']['shipping']['iso_code_3'] = $country_info['iso_code_3'];
                $this->session->data['guest']['shipping']['address_format'] = $country_info['address_format'];
            } else {
                $this->session->data['guest']['shipping']['country'] = '';
                $this->session->data['guest']['shipping']['iso_code_2'] = '';
                $this->session->data['guest']['shipping']['iso_code_3'] = '';
                $this->session->data['guest']['shipping']['address_format'] = '';
            }

            $this->load->model('localisation/zone');

            $zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

            if ($zone_info) {
                $this->session->data['guest']['shipping']['zone'] = $zone_info['name'];
                $this->session->data['guest']['shipping']['zone_code'] = $zone_info['code'];
            } else {
                $this->session->data['guest']['shipping']['zone'] = '';
                $this->session->data['guest']['shipping']['zone_code'] = '';
            }

            $this->session->data['shipping_country_id'] = $this->request->post['country_id'];
            $this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
            $this->session->data['shipping_postcode'] = $this->request->post['postcode'];

            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
        }
        return $json;
//		$this->response->setOutput(json_encode($json));
    }

}

?>