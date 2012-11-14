<?php

class ControllerModuleViewed extends Controller {

    protected function index($setting) {
        $show_on_product = $this->config->get('show_on_product');

        if (isset($this->request->get['route']) && $this->request->get['route'] == 'product/product' && (!isset($show_on_product) || !$show_on_product)) {
            return;
        }
        $this->language->load('module/viewed');

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['button_cart'] = $this->language->get('button_cart');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        $this->data['products'] = array();

        $viewed_count = $this->config->get('viewed_count');
        if (isset($viewed_count) && $viewed_count > 0) {
            $products_info = $this->model_catalog_product->getMostViewedProducts($viewed_count);
            if (isset($this->request->get['route']) && $this->request->get['route'] == 'product/product') {
                $product_id = $this->request->get['product_id'];
                unset($products_info[$product_id]);
            }
            foreach ($products_info as $product_info) {

                if ($product_info) {
                    if ($product_info['image']) {
                        $image = $this->model_tool_image->resize($product_info['image'], $setting['image_width'], $setting['image_height']);
                    } else {
                        $image = false;
                    }

                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                        $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
                    } else {
                        $price = false;
                    }

                    if ((float) $product_info['special']) {
                        $special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
                    } else {
                        $special = false;
                    }
                    if(isset($product_info['special'])){
                                    $discount_percentage = ((($product_info['price'] - $product_info['special']) / $product_info['price']) * 100);
                                } else {
                                    $discount_percentage = '';
                                }
                    $this->data['products'][] = array(
                        'product_id' => $product_info['product_id'],
                        'thumb' => $image,
                        'name' => $product_info['name'],
                        'price' => $price,
                        'special' => $special,
                        'href' => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
                        'discount_percentage'   => $discount_percentage
                    );
                }
            }
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/viewed.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/viewed.tpl';
        } else {
            $this->template = 'default/template/module/viewed.tpl';
        }

        $this->render();
    }

}

?>