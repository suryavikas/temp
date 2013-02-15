<?php 
class ControllerProducttestimonial extends Controller {
	
	public function index() {  
    	$this->language->load('product/testimonial');
		
		$this->load->model('catalog/testimonial');

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
      		'separator' => false
   		);

		
		$testimonial_total = $this->model_catalog_testimonial->getTotalTestimonials();
			
		if ($testimonial_total) {

	  		$this->document->SetTitle ($this->language->get('heading_title'));

	   		$this->data['breadcrumbs'][] = array(
	       		'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('product/testimonial'),
	      		'separator' => $this->language->get('text_separator')
	   		);

						
      		$this->data['heading_title'] = $this->language->get('heading_title');
      		$this->data['text_auteur'] = $this->language->get('text_auteur');
      		$this->data['text_city'] = $this->language->get('text_city');
      		$this->data['button_continue'] = $this->language->get('button_continue');
      		$this->data['showall'] = $this->language->get('text_showall');
      		$this->data['write'] = $this->language->get('text_write');
      		$this->data['text_average'] = $this->language->get('text_average');
      		$this->data['text_stars'] = $this->language->get('text_stars');
      		$this->data['text_no_rating'] = $this->language->get('text_no_rating');
			
			$this->data['continue'] = HTTP_SERVER . 'index.php?route=common/home';
			
			$this->page_limit = $this->config->get('config_catalog_limit');
			
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else { 
				$page = 1;
			}	

			$this->data['testimonials'] = array();
			
			if ( isset($this->request->get['testimonial_id']) ){
				$results = $this->model_catalog_testimonial->getTestimonial($this->request->get['testimonial_id']);
			}
			else{
				$results = $this->model_catalog_testimonial->getTestimonials(($page - 1) * $this->page_limit, $this->page_limit);
			}
			
			foreach ($results as $result) {

				//$result['description'] = strip_tags(html_entity_decode($result['description']));

				
				$this->data['testimonials'][] = array(
					'name'		=> $result['name'],
					'title'    		=> $result['title'],
					'rating'		=> $result['rating'],
					'description'	=> $result['description'],
					'city'		=> $result['city'],
					'date_added'	=> $result['date_added']

				);
			}
			
			$url = '';
	
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
				$this->data['write_url'] = $this->url->link('product/isitestimonial'); 	
			
			if ( isset($this->request->get['testimonial_id']) ){
				$this->data['showall_url'] = $this->url->link('product/testimonial'); 	
			}
			else{
				$pagination = new Pagination();
				$pagination->total = $testimonial_total;
				$pagination->page = $page;
				$pagination->limit = $this->page_limit; 
				$pagination->text = $this->language->get('text_pagination');
				$pagination->url = $this->url->link('product/testimonial', '&page={page}');
				$this->data['pagination'] = $pagination->render();				

			}


			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/testimonial.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/product/testimonial.tpl';
			} else {
				$this->template = 'default/template/product/testimonial.tpl';
			}
			
			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);		
			
	  		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    	} else {

				
	  		$this->document->SetTitle ( $this->language->get('text_error'));
			
      		$this->data['heading_title'] = $this->language->get('text_error');

      		$this->data['text_error'] = $this->language->get('text_error');

      		$this->data['button_continue'] = $this->language->get('button_continue');

      		$this->data['continue'] = HTTP_SERVER . 'index.php?route=common/home';

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/error/not_found.tpl';
			} else {
				$this->template = 'default/template/error/not_found.tpl';
			}
			
			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);
		
	  		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    	}
  	}
}
?>