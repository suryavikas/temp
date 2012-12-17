<?php
class ModelCatalogTellafriend extends Model {
	public function sendMail($data) {
		$friends = array();
		$friends[] = $data['friend'];
		if (isset($data['friends'])) {
			foreach ($data['friends'] as $friend) {
				$friends[] = $friend;
			}
		}
		
		$this->notifyAdmin($data);    		// Comment the following line to stop sending an email to the site admin		
		
		foreach ($friends as $friend) {
			$this->notifyFriend($friend, $data['email'], $data['name']);

		}
	}

	public function notifyFriend($friend, $email, $name) {
		$this->load->language('information/tellafriend');
		$subject = sprintf($this->language->get('email_subject'), $name);
		$message = $this->language->get('email_greeting') . "\n\n";
		$message .= sprintf($this->language->get('email_text_1'), $name, $email) . "\n\n";
		$message .= sprintf($this->language->get('email_text_2'), $name) . "\n\n";
		$message .= $this->config->get('config_name') . ': ' . $this->config->get('config_url');
		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');
		$mail->setTo($friend);
		$mail->setFrom($email);
		$mail->setSender($name);
		$mail->setSubject($subject);
		$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
		$mail->send();
	}

	public function notifyAdmin($data) {
		$this->load->language('information/tellafriend');
		$subject = $this->language->get('email_admin');
		$message = sprintf($this->language->get('email_sender'), $data['name'], $data['email']) . "\n\n";
		$message .= $this->language->get('email_recommend') . "\n";
		$message .= $data['friend'] . "\n";
		if (isset($data['friends'])) {
			foreach ($data['friends'] as $friend) {
				$message .= $friend . "\n";
			}
		}
		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');
		$mail->setTo($this->config->get('config_email'));
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject($subject);
		$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
		$mail->send();
	}
}
?>