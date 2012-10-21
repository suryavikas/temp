<?php
class ControllerModuleSideCart extends Controller {
	protected function index() {
            $this->language->load('module/side_cart');
//
//            $this->data['heading_title'] = $this->language->get('heading_title');
//            $this->data['text_subtotal'] = $this->language->get('text_subtotal');
//            $this->data['text_cart'] = $this->language->get('text_cart');
//            $this->data['text_empty'] = $this->language->get('text_empty');
//            $this->data['cart'] = HTTP_SERVER . 'index.php?route=checkout/cart';
//            $this->data['text_checkout'] = $this->language->get('text_checkout');
//            $this->data['checkout'] = HTTP_SERVER . 'index.php?route=checkout/onepagecheckout';
//
//            $this->data['products'] = array();
//
//            foreach ($this->cart->getProducts() as $result) {
//        	$option_data = array();
//
//        	foreach ($result['option'] as $option) {
//          		$option_data[] = array(
//            		'name'  => $option['name'],
//            		'value' => $option['value']
//          		);
//        	}
//
//      		$this->data['products'][] = array(
//        		'name'     => $result['name'],
//				'option'   => $option_data,
//        		'quantity' => $result['quantity'],
//				'stock'    => $result['stock'],
//				'price'    => $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))),
//				'href'     => $this->url->link('product/product'. '&product_id=' . $result['product_id']),
//      		);
//    	}
//
//    	$this->data['subtotal'] = $this->currency->format($this->cart->getTotal());
//        $this->data['ajax'] = $this->config->get('cart_ajax');
//        $this->id = 'cart';
//        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/side_cart.tpl')) {
//                $this->template = $this->config->get('config_template') . '/template/module/side_cart.tpl';
//        } else {
//                $this->template = 'default/template/module/cart.tpl';
//        }
//
//        $this->response->setOutput($this->render());$this->render();

            if (isset($this->request->get['remove'])) {
          	$this->cart->remove($this->request->get['remove']);

			unset($this->session->data['vouchers'][$this->request->get['remove']]);
      	}

		// Totals
		$this->load->model('setting/extension');

		$total_data = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();

		// Display prices
		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
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

				$sort_order = array();

				foreach ($total_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $total_data);
			}
		}

		$this->data['totals'] = $total_data;

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_items'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
		$this->data['text_empty'] = $this->language->get('text_empty');
		$this->data['text_cart'] = $this->language->get('text_cart');
		$this->data['text_checkout'] = $this->language->get('text_checkout');

		$this->data['button_remove'] = $this->language->get('button_remove');

		$this->load->model('tool/image');

		$this->data['products'] = array();

		foreach ($this->cart->getProducts() as $product) {
			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
			} else {
				$image = '';
			}

			$option_data = array();

			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['option_value'];
				} else {
					$filename = $this->encryption->decrypt($option['option_value']);

					$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
				}

				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
					'type'  => $option['type']
				);
			}

			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$price = false;
			}

			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']);
			} else {
				$total = false;
			}

			$this->data['products'][] = array(
				'key'      => $product['key'],
				'thumb'    => $image,
				'name'     => $product['name'],
				'model'    => $product['model'],
				'option'   => $option_data,
				'quantity' => $product['quantity'],
				'price'    => $price,
				'total'    => $total,
				'href'     => $this->url->link('product/product', 'product_id=' . $product['product_id'])
			);
		}

		// Gift Voucher
		$this->data['vouchers'] = array();

		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$this->data['vouchers'][] = array(
					'key'         => $key,
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'])
				);
			}
		}

		$this->data['cart'] = $this->url->link('checkout/cart');

		$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/side_cart.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/side_cart.tpl';
		} else {
			$this->template = 'default/template/module/cart.tpl';
		}

		$this->response->setOutput($this->render());
	
	}

	public function callback() {
/*		$this->language->load('common/header');
*/		$this->language->load('module/side_cart');

//		$this->load->model('tool/seo_url');

    	$text_side_cart = $this->language->get('text_side_cart');
    	$cart = HTTP_SERVER . 'index.php?route=checkout/cart';
    	$text_checkout = $this->language->get('text_checkout');
    	$checkout =   HTTP_SERVER . 'index.php?route=checkout/shipping';
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (isset($this->request->post['option'])) {
				$option = $this->request->post['option'];
			} else {
				$option = array();
			}

      		$this->cart->add($this->request->post['product_id'], $this->request->post['quantity'], $option);

			unset($this->session->data['shipping_methods']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['payment_method']);
		}

		$output = '<table cellpadding="2" cellspacing="0" style="width: 100%;">';

    	foreach ($this->cart->getProducts() as $product) {
      		$output .= '<tr>';
        	$output .= '<td width="1" valign="top" align="right">' . $product['quantity'] . '&nbsp;x&nbsp;</td>';
        	$output .= '<td align="left" valign="top"><a href="' . $this->url->link('product/product'. '&product_id=' . $result['product_id']) . '">' . $product['name'] . '</a>';
          	$output .= '<div>';

			foreach ($product['option'] as $option) {
            	$output .= ' - <small style="color: #999;">' . $option['name'] . ' ' . $option['value'] . '</small><br />';
            }

			$output .= '</div></td>';
			$output .= '</tr>';
      	}

		$output .= '</table>';
    	$output .= '<br />';
    	$output .= '<div style="text-align: right;">' . $this->language->get('text_subtotal') . '&nbsp;' .  $this->currency->format($this->cart->getTotal()) . '</div>';
    	$output .= '<div style="text-align: center; margin-top: 10px; float:left;"><a href="' . $cart . '" class="button" style="text-decoration: none;"><span>' . $text_cart . '</span></a></div>';
    	$output .= '<div style="text-align: center; margin-top: 10px; float:right;"><a href="' . $checkout . '" class="button" style="text-decoration: none;"><span>' . $text_checkout . '</span></a></div>';
    	$output .= '<div  class="clear"></div>';
		$this->response->setOutput($output, $this->config->get('config_compression'));
	}
}
?>