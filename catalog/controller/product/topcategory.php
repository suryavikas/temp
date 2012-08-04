<?php

class ControllerProductTopCategory extends Controller {

    public function index() {
        $this->language->load('product/category');

        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'p.sort_order';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit = $this->config->get('config_catalog_limit');
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        if (isset($this->request->get['path'])) {
            $path = '';

            $parts = explode('_', (string) $this->request->get['path']);

            foreach ($parts as $path_id) {
                if (!$path) {
                    $path = $path_id;
                } else {
                    $path .= '_' . $path_id;
                }

                $category_info = $this->model_catalog_category->getCategory($path_id);

                if ($category_info) {
                    $this->data['breadcrumbs'][] = array(
                        'text' => $category_info['name'],
                        'href' => $this->url->link('product/category', 'path=' . $path),
                        'separator' => $this->language->get('text_separator')
                    );
                }
            }

            $category_id = array_pop($parts);
        } else {
            $category_id = 0;
        }

        $category_info = $this->model_catalog_category->getCategory($category_id);

        if ($category_info) {
            $this->document->setTitle($category_info['name']);
            $this->document->setDescription($category_info['meta_description']);
            $this->document->setKeywords($category_info['meta_keyword']);

            $this->data['heading_title'] = $category_info['name'];

            $this->data['text_refine'] = $this->language->get('text_refine');
            $this->data['text_empty'] = $this->language->get('text_empty');
            $this->data['text_quantity'] = $this->language->get('text_quantity');
            $this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
            $this->data['text_model'] = $this->language->get('text_model');
            $this->data['text_price'] = $this->language->get('text_price');
            $this->data['text_tax'] = $this->language->get('text_tax');
            $this->data['text_points'] = $this->language->get('text_points');
            $this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
            $this->data['text_display'] = $this->language->get('text_display');
            $this->data['text_list'] = $this->language->get('text_list');
            $this->data['text_grid'] = $this->language->get('text_grid');
            $this->data['text_sort'] = $this->language->get('text_sort');
            $this->data['text_limit'] = $this->language->get('text_limit');

            $this->data['button_cart'] = $this->language->get('button_cart');
            $this->data['button_wishlist'] = $this->language->get('button_wishlist');
            $this->data['button_compare'] = $this->language->get('button_compare');
            $this->data['button_continue'] = $this->language->get('button_continue');

            if ($category_info['image']) {
                $this->data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
            } else {
                $this->data['thumb'] = '';
            }

            $this->data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
            $this->data['compare'] = $this->url->link('product/compare');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $this->data['categories'] = array();

            $results = $this->model_catalog_category->getCategories($category_id);
            
            //Storing results so, that same array can be used again
            $this->data['categories_arr'] = $results;
            $sub_category_arr = array();
           
            foreach ($results as $result) {
                $data = array(
                    'filter_category_id' => $result['category_id'],
                    'filter_sub_category' => true
                );

                $sub_category_arr = $this->model_catalog_category->getCategories($result['category_id']);
                
                $children_data_level2 = array();
                if (sizeof($sub_category_arr) > 0) {
                    foreach ($sub_category_arr as $child_level2) {
                        $children_data_level2[] = array(
                            'name' => $child_level2['name'],
                            'href' => $this->url->link('product/category', 'path=' . $category_id . '_' . $result['category_id'] . '_' . $child_level2['category_id'])
                        );
                    }
                }
                // Level 1
                $this->data['categories'][] = array(                    
                    'name' => $result['name'],
                    'children' => $children_data_level2,
                    'column' => $result['column'] ? $result['column'] : 1,
                    'href' => $this->url->link('product/category', 'path=' . $category_id . '_' . $result['category_id'])
                );
            }

            $this->data['products'] = array();

            $data = array(
                'filter_category_id' => $category_id,
                'filter_sub_category' => TRUE
            );

            $product_total = $this->model_catalog_product->getTotalProducts($data);
            $this->data['product_total'] = $product_total;
            $category_product_arr = array();
            $i=-1;
            foreach ($results as $categories) {
                $data = array(
                    'filter_category_id' => $categories['category_id'],
                    'filter_sub_category' => TRUE,
                    'start' => 0,
                    'limit' => 4
                );
                $i++;
                $categories['href'] = $this->url->link('product/category', 'path=' . $category_id . '_' . $categories['category_id']);

                $category_product_arr[$i]['category'] = $categories;
                $innerResults = $this->model_catalog_product->getProducts($data);
                foreach ($innerResults as $result) {

                    if ($result['image']) {
                        $image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
                    } else {
                        $image = false;
                    }

                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                        $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
                    } else {
                        $price = false;
                    }

                    if ((float) $result['special']) {
                        $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
                    } else {
                        $special = false;
                    }

                    if ($this->config->get('config_tax')) {
                        $tax = $this->currency->format((float) $result['special'] ? $result['special'] : $result['price']);
                    } else {
                        $tax = false;
                    }

                    if ($this->config->get('config_review_status')) {
                        $rating = (int) $result['rating'];
                    } else {
                        $rating = false;
                    }
                    $prodDis = $this->model_catalog_product->getProductDiscounts($result['product_id']);
                    $savings = 0;
                    if(sizeof($prodDis) > 0){
                        $prodDis = $prodDis[0];
                        $savings = $result['price'] - $prodDis['price'];
                        
                        if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                            $disPrice = $this->currency->format($this->tax->calculate($prodDis['price'], $result['tax_class_id'], $this->config->get('config_tax')));
                            $prodDis['price'] = $disPrice;
                        } else {
                            $prodDis = array();
                        }
                    }
                                       
                    $category_product_arr[$i]['category']['products'][] = array(
                        'product_id' => $result['product_id'],
                        'thumb' => $image,
                        'name' => $result['name'],
                        'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
                        'price' => $price,
                        'special' => $special,
                        'tax' => $tax,
                        'rating' => $result['rating'],
                        'reviews' => sprintf($this->language->get('text_reviews'), (int) $result['reviews']),
                        'href' => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id']),
                        'discount' => $prodDis,
                        'savings' =>$savings                        
                    );
                    
                }
                
            }
             $this->data['category_product_arr'] = $category_product_arr;
            

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/topcategory.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/product/topcategory.tpl';
            } else {
                $this->template = 'default/template/product/category.tpl';
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
    }

}

?>