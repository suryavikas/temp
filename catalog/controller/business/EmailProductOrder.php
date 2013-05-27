<?php
/**
 * Description of EmailProductOrder
 *
 * @author surya
 */
class ControllerBusinessEmailProductOrder extends Controller {

    public function before_payment_email() {

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
                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
                    'type' => $option['type']
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

            $products[] = array(
                'key' => $product['key'],
                'name' => $product['name'],
                'model' => $product['model'],
                'option' => $option_data,
                'quantity' => $product['quantity'],
                'price' => $price,
                'total' => $total,
                'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'])
            );
        }
       
        // Admin Alert Mail
        if ($this->config->get('config_alert_mail')) {
             echo "HELLO";
            $this->language->load('business/emailproductorder');
            $subject = sprintf($this->language->get('text_new_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

            // Text
            $text = $this->language->get('text_new_received') . "\n\n";
//				$text .= $this->language->->get('text_new_order_id') . ' ' . $order_id . "\n";
            $text .= $this->language->get('text_new_date_added') . ' ' . date($this->language->get('date_format_short')) . "\n";
//				$text .= $this->language->get('text_new_order_status') . ' ' . $order_status . "\n\n";
            $text .= $this->language->get('text_new_products') . "\n";
            foreach ($products as $product) {
                $text .= $product['name'] . '\n';
                foreach ($product['option'] as $option) {
                    $text .= '\t' . $option['name'] . '    ' . $option['value'] . '\n';
                }
                $text .= $this->language->get('text_new_quantity') . '    ' . $product['quantity'] . '\n';
                $text .= $this->language->get('text_new_order_total') . '    ' . $product['total'] . '\n';
            }
            echo $text;
            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_smtp_host');
            $mail->username = $this->config->get('config_smtp_username');
            $mail->password = $this->config->get('config_smtp_password');
            $mail->port = $this->config->get('config_smtp_port');
            $mail->timeout = $this->config->get('config_smtp_timeout');
            $mail->setTo($this->config->get('config_email'));
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender(html_entity_decode($this->config->get('config_name')));
            $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
            $mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
            $mail->send();

            // Send to additional alert emails
            $emails = explode(',', $this->config->get('config_alert_emails'));

            foreach ($emails as $email) {
                if ($email && preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {
                    $mail->setTo($email);
                    $mail->send();
                }
            }
        }
    }
    

}

?>
