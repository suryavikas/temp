<?php  
class ControllerModuletestimonial extends Controller {
	protected function index($setting) {
		$this->language->load('module/testimonial');

		$this->data['testimonial_title'] = html_entity_decode($setting['testimonial_title'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');

      	$this->data['heading_title'] = $this->language->get('heading_title');
      	$this->data['text_more'] = $this->language->get('text_more');
      	$this->data['text_more2'] = $this->language->get('text_more2');
		$this->data['isi_testimonial'] = $this->language->get('isi_testimonial');
		$this->data['show_all'] = $this->language->get('show_all');
		$this->data['showall_url'] = $this->url->link('product/testimonial'); 
		$this->data['more'] = $this->url->link('product/testimonial', 'testimonial_id='); 
		$this->data['isitesti'] = $this->url->link('product/isitestimonial');

		$this->load->model('catalog/testimonial');
		
		$this->data['testimonials'] = array();
		
		$this->data['total'] = $this->model_catalog_testimonial->getTotalTestimonials();
		$results = $this->model_catalog_testimonial->getTestimonials(0, $setting['testimonial_limit'], (isset($setting['testimonial_random']))?true:false);


		foreach ($results as $result) {
			

			//$result['description'] = strip_tags(html_entity_decode($result['description']));

			if (!isset($setting['testimonial_character_limit']))
				$setting['testimonial_character_limit'] = 0;

			if ($setting['testimonial_character_limit']>0)
			{
				$lim = $setting['testimonial_character_limit'];
				if (mb_strlen($result['description'],'UTF-8')>$lim) 
					$result['description'] = mb_substr($result['description'], 0, $lim-3, 'UTF-8'). ' ' .'<a href="'.$this->data['more']. $result['testimonial_id'] .'" title="'.$this->data['text_more2'].'">'. $this->data['text_more'] . '</a>';
				else
					$result['description'] = $result['description'] . ' ' .'<a href="'.$this->data['more']. $result['testimonial_id'] .'" title="'.$this->data['text_more2'].'">'. $this->data['text_more'] . '</a>';

			}
			else
				$result['description'] = $result['description'] . ' ' .'<a href="'.$this->data['more']. $result['testimonial_id'] .'" title="'.$this->data['text_more2'].'">'. $this->data['text_more'] . '</a>';


			$this->data['testimonials'][] = array(
				'id'			=> $result['testimonial_id'],											  
				'title'		=> $result['title'],
				'description'	=> $result['description'],
				'rating'		=> $result['rating'],
				'name'		=> $result['name'],
				'date_added'	=> $result['date_added'],
				'city'		=> $result['city']

			);
		}

		

		$this->id = 'testimonial';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/testimonial.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/testimonial.tpl';
		} else {
			$this->template = 'default/template/module/testimonial.tpl';
		}
		
		$this->render();
	}
}
?>