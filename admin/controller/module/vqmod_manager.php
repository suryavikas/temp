<?php
class ControllerModuleVQModManager extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('module/vqmod_manager');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {

			// Upload VQMod
			if (isset($this->request->post['upload'])) {
				$this->vqmod_upload();

			// Settings
			} else {
				$this->model_setting_setting->editSetting('vqmod_manager', $this->request->post);

				$this->session->data['success'] = $this->language->get('text_success');

				$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
			}
		}

		// Language
		$this->data = array_merge($this->data, $this->load->language('module/vqmod_manager'));

		// Warning
		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$this->data['error_warning'] = '';
		}

		// Success
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		// Breadcrumbs
		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'text'      => $this->language->get('text_home'),
			'separator' => FALSE
		);

		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'text'      => $this->language->get('text_module'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('module/vqmod_manager', 'token=' . $this->session->data['token'], 'SSL'),
			'text'      => $this->language->get('heading_title'),
			'separator' => ' :: '
		);

		// Action Buttons
		$this->data['action'] = $this->url->link('module/vqmod_manager', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		// Clear Cache / Logs
		$this->data['clear_log'] = $this->url->link('module/vqmod_manager/clear_log', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['clear_vqcache'] = $this->url->link('module/vqmod_manager/clear_cache', 'token=' . $this->session->data['token'], 'SSL');

		// Backup VQMods
		$this->data['backup'] = $this->url->link('module/vqmod_manager/vqmod_backup', 'token=' . $this->session->data['token'], 'SSL');

		// Get VQMod path
		if (isset($this->request->post['vqmod_path'])) {
			$this->data['vqmod_path'] = $this->request->post['vqmod_path'];
		} else {
			$this->data['vqmod_path'] = $this->config->get('vqmod_path');
		}

		// Attempts to autodetect VQMod path
		if (is_null($this->data['vqmod_path']) || strlen($this->data['vqmod_path']) < 1 || !is_dir($this->data['vqmod_path'])) {
			$this->data['path_set'] = FALSE;
			$path = substr_replace(DIR_SYSTEM, '', -7);

			if (is_dir($path . 'vqmod/')) {
				$this->data['vqmod_path'] = $path . 'vqmod/';

				$this->data['text_autodetect'] = $this->language->get('text_autodetect');
			} else {
				$this->data['text_autodetect'] = $this->language->get('text_autodetect_fail');
			}
		} else {
			$this->data['path_set'] = TRUE;
		}

		// Detect mods
		$vqmods = glob($this->data['vqmod_path'] . 'xml/*.xml*');

		if (!empty($vqmods)) {
			foreach ($vqmods as $vqmod) {
				if (strpos($vqmod, '.xml_')) {
					$file = basename($vqmod, '.xml_');
				} else {
					$file = basename($vqmod, '.xml');
				}

				$action = array();

				if (strpos($vqmod, '.xml_')) {
					$action[] = array(
						'text' => $this->language->get('text_install'),
						'href' => $this->url->link('module/vqmod_manager/vqmod_install', 'token=' . $this->session->data['token'] . '&vqmod=' . $file, 'SSL')
					);
				} else {
					$action[] = array(
						'text' => $this->language->get('text_uninstall'),
						'href' => $this->url->link('module/vqmod_manager/vqmod_uninstall', 'token=' . $this->session->data['token'] . '&vqmod=' . $file, 'SSL')
					);
				}

				$xml = simplexml_load_file($vqmod);

				$this->data['vqmods'][$vqmod] = array(
					'file_name'  => basename($vqmod, ''),
					'id'         => isset($xml->id) ? $xml->id : $this->language->get('text_unavailable'),
					'version'    => isset($xml->version) ? $xml->version : $this->language->get('text_unavailable'),
					'vqmver'     => isset($xml->vqmver) ? $xml->vqmver : $this->language->get('text_unavailable'),
					'author'     => isset($xml->author) ? $xml->author : $this->language->get('text_unavailable'),
					'status'     => strpos($vqmod, '.xml_') ? $this->language->get('text_disabled') : $this->language->get('text_enabled'),
					'delete'     => $this->url->link('module/vqmod_manager/vqmod_delete', 'token=' . $this->session->data['token'] . '&vqmod=' . basename($vqmod), 'SSL'),
					'action'     => $action
				);
			}
		}

		// VQCache files
		if (isset($this->data['vqmod_path'])) {
			$vqcache_dir = $this->data['vqmod_path'] . 'vqcache/';
			$this->data['vqcache'] = array_diff(scandir($vqcache_dir), array('.', '..'));
		}

		// VQMod Error Log
		$log_file = $this->config->get('vqmod_path') . 'vqmod.log';

		if (file_exists($log_file) && filesize($log_file) > 0) {

			// Error if log file is larger than 6MB
			if (filesize($log_file) > 6291456) {
				$this->data['error_warning'] = sprintf($this->language->get('error_log_size'), (round((filesize($log_file) / 1048576), 2)));
				$this->data['log'] = sprintf($this->language->get('error_log_size'), (round((filesize($log_file) / 1048576), 2)));

			// Regular log
			} else {
				$this->data['log'] = file_get_contents($log_file, FILE_USE_INCLUDE_PATH, NULL);
			}

		// No log / empty log
		} else {
			$this->data['log'] = '';
		}

		// Template
		$this->template = 'module/vqmod_manager.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function vqmod_install() {
		$this->load->language('module/vqmod_manager');

		if (!$this->user->hasPermission('modify', 'module/vqmod_manager')) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$path = $this->config->get('vqmod_path') . 'xml/';
			$vqmod = $this->request->get['vqmod'];

			if (file_exists($path . $vqmod . '.xml_')) {
				rename($path . $vqmod . '.xml_', $path . $vqmod . '.xml');

				$this->clear_cache($return = true);

				$this->session->data['success'] = $this->language->get('success_install');
			} else {
				$this->session->data['error'] = $this->language->get('error_install');
			}
		}

		$this->redirect($this->url->link('module/vqmod_manager', 'token=' . $this->session->data['token'], 'SSL'));
	}

	public function vqmod_uninstall() {
		$this->load->language('module/vqmod_manager');

		if (!$this->user->hasPermission('modify', 'module/vqmod_manager')) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$path = $this->config->get('vqmod_path') . 'xml/';
			$vqmod = $this->request->get['vqmod'];

			if (file_exists($path . $vqmod . '.xml')) {
				rename($path . $vqmod . '.xml', $path . $vqmod . '.xml_');

				$this->clear_cache($return = true);

				$this->session->data['success'] = $this->language->get('success_uninstall');
			} else {
				$this->session->data['error'] = $this->language->get('error_uninstall');
			}
		}

		$this->redirect($this->url->link('module/vqmod_manager', 'token=' . $this->session->data['token'], 'SSL'));
	}

	public function vqmod_upload() {
		$this->load->language('module/vqmod_manager');

		if (!$this->user->hasPermission('modify', 'module/vqmod_manager')) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$file = $this->request->files['vqmod_file']['tmp_name'];
			$file_name = $this->request->files['vqmod_file']['name'];

			if ($this->request->files['vqmod_file']['error'] > 0) {

				switch($this->request->files['vqmod_file']['error']) {
					case 1:
						$this->session->data['error'] = $this->language->get('error_ini_max_file_size');
						break;
					case 2:
						$this->session->data['error'] = $this->language->get('error_form_max_file_size');
						break;
					case 3:
						$this->session->data['error'] = $this->language->get('error_partial_upload');
						break;
					case 4:
						$this->session->data['error'] = $this->language->get('error_no_upload');
						break;
					case 6:
						$this->session->data['error'] = $this->language->get('error_no_temp_dir');
						break;
					case 7:
						$this->session->data['error'] = $this->language->get('error_write_fail');
						break;
					case 8:
						$this->session->data['error'] = $this->language->get('error_php_conflict');
						break;
					default:
						$this->session->data['error'] = $this->language->get('error_unknown');
				}

			} else {
				if ($this->request->files['vqmod_file']['type'] != 'text/xml') {
					$this->session->data['error'] = $this->language->get('error_filetype');

				} else {
					libxml_use_internal_errors(true);
					simplexml_load_file($file);

					if (libxml_get_errors()) {
						libxml_clear_errors();
						$this->session->data['error'] = $this->language->get('error_invalid_xml');

					} elseif (move_uploaded_file($file, $this->config->get('vqmod_path') . 'xml/' . $file_name) == FALSE) {
						$this->session->data['error'] = $this->language->get('error_move');

					} else {
						$this->clear_cache($return = true);

						$this->session->data['success'] = $this->language->get('success_upload');
					}
				}
			}
		}

		$this->redirect($this->url->link('module/vqmod_manager', 'token=' . $this->session->data['token'], 'SSL'));
	}

	public function vqmod_delete() {
		$this->load->language('module/vqmod_manager');

		if (!$this->user->hasPermission('modify', 'module/vqmod_manager')) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$path = $this->config->get('vqmod_path') . 'xml/';
			$vqmod = $this->request->get['vqmod'];

			if (unlink($path . $vqmod)) {
				$this->clear_cache($return = true);

				$this->session->data['success'] = $this->language->get('success_delete');
			} else {
				$this->session->data['error'] = $this->language->get('error_delete');
			}
		}

		$this->redirect($this->url->link('module/vqmod_manager', 'token=' . $this->session->data['token'], 'SSL'));
	}

	public function vqmod_backup() {
		$this->load->language('module/vqmod_manager');

		if (!$this->user->hasPermission('modify', 'module/vqmod_manager')) {
			$this->session->data['error'] = $this->language->get('error_permission');
			$this->redirect($this->url->link('module/vqmod_manager', 'token=' . $this->session->data['token'], 'SSL'));
		} else {
			$vqmods = glob($this->config->get('vqmod_path') . 'xml/*.xml*');

			$temp = tempnam('tmp', 'zip');

			$zip = new ZipArchive();
			$zip->open($temp, ZipArchive::OVERWRITE);

			foreach ($vqmods as $vqmod) {
				$zip->addFile($vqmod, basename($vqmod));
			}

			$zip->close();

			header('Pragma: public');
			header('Expires: 0');
			header('Content-Description: File Transfer');
			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename=vqmod_backup_' . date('Y-m-d') . '.zip');
			header('Content-Transfer-Encoding: binary');
			readfile($temp);
			@unlink($temp);
		}
	}

	public function clear_cache($return = false) {
		$this->load->language('module/vqmod_manager');

		if (!$this->user->hasPermission('modify', 'module/vqmod_manager')) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$files = glob($this->config->get('vqmod_path') . 'vqcache/' . 'vq*');

			if ($files) {
				foreach ($files as $file) {
					if (file_exists($file)) {
						@unlink($file);
						clearstatcache();
					}
				}
			}

			if ($return) {
				return;
			}

			$this->session->data['success'] = $this->language->get('success_clear_cache');
		}

		$this->redirect($this->url->link('module/vqmod_manager', 'token=' . $this->session->data['token'], 'SSL'));
	}

	public function clear_log() {
		$this->load->language('module/vqmod_manager');

		if (!$this->user->hasPermission('modify', 'module/vqmod_manager')) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$file = $this->config->get('vqmod_path') . 'vqmod.log';

			$handle = fopen($file, 'w+');

			fclose($handle);

			$this->session->data['success'] = $this->language->get('success_clear_log');
		}

		$this->redirect($this->url->link('module/vqmod_manager', 'token=' . $this->session->data['token'], 'SSL'));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/vqmod_manager')) {
			$this->session->data['error'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>