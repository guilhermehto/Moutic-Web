<?php
class ControllerAccountIngressos extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/download', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		//Pega a linguagem
		$this->load->language('account/download');

		//Bota título da página
		$this->document->setTitle($this->language->get('heading_title'));

		//Gera os breadcrumbs
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('Meus Ingressos'),
			'href' => $this->url->link('account/ingressos', '', true)
		);

		//Pega os models
		$this->load->model('account/download');
        $this->load->model('account/order');
        $this->load->model('catalog/product');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_empty'] = $this->language->get('text_empty');

		$data['column_order_id'] = $this->language->get('column_order_id');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_size'] = $this->language->get('column_size');
		$data['column_date_added'] = $this->language->get('column_date_added');

		$data['button_download'] = $this->language->get('button_download');
		$data['button_continue'] = $this->language->get('button_continue');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}


        //Cria array de ingressos
		$data['ingressos'] = array();

		//Checa o número total de ingressos
		$ingressos_total = $this->model_account_order->getTotalOrders();
		$data['ingressos_total'] = $ingressos_total;

		$results = $this->model_account_order->getOrders();


        $ingressos = array();
        $data['ingressos'] = array();

        //Percorre as orders para pegar os produtos(ingressos) das mesmas
        foreach($results as $result){
		    $ingressos = $this->model_account_order->getOrderProducts($result['order_id']);

            foreach ($ingressos as $ingresso) {
                $option_data = array();

                //Seleciona as options do produto(nome do ingresso)
                $options = $this->model_account_order->getOrderOptions($result['order_id'], $ingresso['order_product_id']);

                //Retorna uma array, então percorre ela
                foreach ($options as $option) {
                    if ($option['type'] != 'file') {
                        $value = $option['value'];
                    }

                    $option_data[] = array(
                        'name'  => $option['value'],
                    );
                }

                //Mais para teste, talvez remover em produção visto que todo produto terá uma option
                $nome_ingresso = "Não tem option";

                if(isset($option['value'])){
                    $nome_ingresso = $option['value'];
                }

                $utilizado = (int)$ingresso['utilizado'] == 0 ? 'Não' : 'Sim';
                //Cria a array com as informações do produto
                $data['ingressos'][] = array(
                    'festa'          => $ingresso['name'],
                    'ingresso'       => $nome_ingresso,
                    'utilizado'      => $utilizado,
                    'codigo'         => "link"
                );
            }
        }








		$data['downloads'] = array();

		$download_total = $this->model_account_download->getTotalDownloads();

		$results = $this->model_account_download->getDownloads(($page - 1) * $this->config->get($this->config->get('config_theme') . '_product_limit'), $this->config->get($this->config->get('config_theme') . '_product_limit'));



		$pagination = new Pagination();
		$pagination->total = $download_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get($this->config->get('config_theme') . '_product_limit');
		$pagination->url = $this->url->link('account/download', 'page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($download_total) ? (($page - 1) * $this->config->get($this->config->get('config_theme') . '_product_limit')) + 1 : 0, ((($page - 1) * $this->config->get($this->config->get('config_theme') . '_product_limit')) > ($download_total - $this->config->get($this->config->get('config_theme') . '_product_limit'))) ? $download_total : ((($page - 1) * $this->config->get($this->config->get('config_theme') . '_product_limit')) + $this->config->get($this->config->get($this->config->get('config_theme') . '_theme') . '_product_limit')), $download_total, ceil($download_total / $this->config->get($this->config->get('config_theme') . '_product_limit')));

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/ingressos', $data));
	}

	public function download() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/download', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->model('account/download');

		if (isset($this->request->get['download_id'])) {
			$download_id = $this->request->get['download_id'];
		} else {
			$download_id = 0;
		}

		$download_info = $this->model_account_download->getDownload($download_id);

		if ($download_info) {
			$file = DIR_DOWNLOAD . $download_info['filename'];
			$mask = basename($download_info['mask']);

			if (!headers_sent()) {
				if (file_exists($file)) {
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));

					if (ob_get_level()) {
						ob_end_clean();
					}

					readfile($file, 'rb');

					exit();
				} else {
					exit('Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		} else {
			$this->response->redirect($this->url->link('account/download', '', true));
		}
	}
}