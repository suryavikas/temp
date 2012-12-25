<?php
class ControllerModuleCR2HTMLModule extends Controller {
	private $error = array();
	private $_name = 'cr2htmlmodule';
	private $_version = '1.5c';

	public function index() {
		$this->load->language('module/' . $this->_name);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		$this->data['cr2htmlmodule_version'] = $this->_version;

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting($this->_name, $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');

		$this->data['entry_product'] = $this->language->get('entry_product');
		$this->data['entry_area_id'] = $this->language->get('entry_area_id');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_borderless'] = $this->language->get('entry_borderless');
		$this->data['entry_borderlesswarn'] = $this->language->get('entry_borderlesswarn');
		$this->data['entry_classname'] = $this->language->get('entry_classname');
		$this->data['entry_classnameinfo'] = $this->language->get('entry_classnameinfo');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');
		$this->data['entry_yes'] = $this->language->get( 'entry_yes' );
		$this->data['entry_no']	= $this->language->get( 'entry_no' );

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

          $this->data['entry_area'] = $this->language->get('entry_area');
          $this->data['entry_code'] = $this->language->get('entry_code');
		$this->data['entry_header'] = $this->language->get( 'entry_header' );
		$this->data['entry_title'] = $this->language->get( 'entry_title' );

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $language) {
			if (isset($this->error['code' . $language['language_id']])) {
				$this->data['error_code' . $language['language_id']] = $this->error['code' . $language['language_id']];
			} else {
				$this->data['error_code' . $language['language_id']] = '';
			}
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/'.$this->_name, 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$this->data['action'] = $this->url->link('module/'.$this->_name, 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];

		$this->load->model('localisation/language');

		foreach ($languages as $language) {
			if (isset($this->request->post[$this->_name . '_code1_' . $language['language_id']])) {
				$this->data[$this->_name . '_code1_' . $language['language_id']] = $this->request->post[$this->_name . '_code1_' . $language['language_id']];
			} else {
				$this->data[$this->_name . '_code1_' . $language['language_id']] = $this->config->get($this->_name . '_code1_' . $language['language_id']);
			}
			if (isset($this->request->post[$this->_name . '_code2_' . $language['language_id']])) {
				$this->data[$this->_name . '_code2_' . $language['language_id']] = $this->request->post[$this->_name . '_code2_' . $language['language_id']];
			} else {
				$this->data[$this->_name . '_code2_' . $language['language_id']] = $this->config->get($this->_name . '_code2_' . $language['language_id']);
			}
			if (isset($this->request->post[$this->_name . '_code3_' . $language['language_id']])) {
				$this->data[$this->_name . '_code3_' . $language['language_id']] = $this->request->post[$this->_name . '_code3_' . $language['language_id']];
			} else {
				$this->data[$this->_name . '_code3_' . $language['language_id']] = $this->config->get($this->_name . '_code3_' . $language['language_id']);
			}
		}

		foreach ($languages as $language) {
			if (isset($this->request->post[$this->_name . '_title1_' . $language['language_id']])) {
				$this->data[$this->_name . '_title1_' . $language['language_id']] = $this->request->post[$this->_name . '_title1_' . $language['language_id']];
			} else {
				$this->data[$this->_name . '_title1_' . $language['language_id']] = $this->config->get($this->_name . '_title1_' . $language['language_id']);
			}
			if (isset($this->request->post[$this->_name . '_title2_' . $language['language_id']])) {
				$this->data[$this->_name . '_title2_' . $language['language_id']] = $this->request->post[$this->_name . '_title2_' . $language['language_id']];
			} else {
				$this->data[$this->_name . '_title2_' . $language['language_id']] = $this->config->get($this->_name . '_title2_' . $language['language_id']);
			}
			if (isset($this->request->post[$this->_name . '_title3_' . $language['language_id']])) {
				$this->data[$this->_name . '_title3_' . $language['language_id']] = $this->request->post[$this->_name . '_title3_' . $language['language_id']];
			} else {
				$this->data[$this->_name . '_title3_' . $language['language_id']] = $this->config->get($this->_name . '_title3_' . $language['language_id']);
			}
		}

		$this->data['languages'] = $languages;

		if( isset( $this->request->post[$this->_name . '_header1'] ) ) {
			$this->data[$this->_name . '_header1'] = $this->request->post[$this->_name . '_header1'];
		}else{
			$this->data[$this->_name . '_header1'] = $this->config->get( $this->_name . '_header1' );
		}
		if( isset( $this->request->post[$this->_name . '_header2'] ) ) {
			$this->data[$this->_name . '_header2'] = $this->request->post[$this->_name . '_header2'];
		}else{
			$this->data[$this->_name . '_header2'] = $this->config->get( $this->_name . '_header2' );
		}
		if( isset( $this->request->post[$this->_name . '_header3'] ) ) {
			$this->data[$this->_name . '_header3'] = $this->request->post[$this->_name . '_header3'];
		}else{
			$this->data[$this->_name . '_header3'] = $this->config->get( $this->_name . '_header3' );
		}

		if( isset( $this->request->post[$this->_name . '_borderless1'] ) ) {
			$this->data[$this->_name . '_borderless1'] = $this->request->post[$this->_name . '_borderless1'];
		}else{
			$this->data[$this->_name . '_borderless1'] = $this->config->get( $this->_name . '_borderless1' );
		}
		if( isset( $this->request->post[$this->_name . '_borderless2'] ) ) {
			$this->data[$this->_name . '_borderless2'] = $this->request->post[$this->_name . '_borderless2'];
		}else{
			$this->data[$this->_name . '_borderless2'] = $this->config->get( $this->_name . '_borderless2' );
		}
		if( isset( $this->request->post[$this->_name . '_borderless3'] ) ) {
			$this->data[$this->_name . '_borderless3'] = $this->request->post[$this->_name . '_borderless3'];
		}else{
			$this->data[$this->_name . '_borderless3'] = $this->config->get( $this->_name . '_borderless3' );
		}

		$this->load->model('design/layout');

		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->data['modules'] = array();

		if (isset($this->request->post['cr2htmlmodule_module'])) {
			$this->data['modules'] = $this->request->post['cr2htmlmodule_module'];
		} elseif ($this->config->get('cr2htmlmodule_module')) {
			$this->data['modules'] = $this->config->get('cr2htmlmodule_module');
		}

		$this->template = 'module/' . $this->_name . '.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}

	private function validate() {

		if (!$this->user->hasPermission('modify', 'module/' . $this->_name)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

     	if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>
