<?php 
class ControllerToolGenerateSitemap extends Controller { 
	private $error = array();
	
	public function index() {
		$this->load->language('tool/generate_sitemap');

        $this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('tool/generate_sitemap');

		$this->data['heading_title'] = $this->language->get('heading_title');
		 
		$this->data['button_generate'] = $this->language->get('button_generate');
		
		$this->data['text_common'] = $this->language->get('text_common');
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
        
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        if (isset($this->session->data['output'])) {
            $this->data['output'] = $this->session->data['output'];
        
            unset($this->session->data['output']);
        } else {
            $this->data['output'] = '';
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),             
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('tool/generate_sitemap', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['generate_sitemap'] = $this->url->link('tool/generate_sitemap/generate', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['generate'] = $this->url->link('tool/generate_sitemap/generate', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->load->model('tool/generate_sitemap');
			
		$this->template = 'tool/generate_sitemap.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
        $this->response->setOutput($this->render());
	}
	
	public function generate() {
		$this->load->language('tool/generate_sitemap');

        $this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('tool/generate_sitemap');
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$this->session->data['output'] = $this->model_tool_generate_sitemap->generate();			
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->url->link('tool/generate_sitemap', 'token=' . $this->session->data['token'], 'SSL'));
        } else {
            return $this->forward('error/permission');
        }
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'tool/generate_sitemap')) {
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