<?php
class ControllerAccountPromoters extends Controller {
    public function index() {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/download', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        $this->load->language('account/download');

        $this->document->setTitle($this->language->get('heading_title'));

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
            'text' => 'Promoters',
            'href' => $this->url->link('account/promoters', '', true)
        );

        $this->load->model('account/download');
        $this->load->model('moutic/promoter');


        $data['promoters'] = array();
        $promoters = $this->model_moutic_promoter->getPromoters();


        foreach($promoters as $promoter){
            //Pego a porcentagem de comissão do cabeça
            $tipo_comissao = (float)$promoter['commission'];
            $comissao = (float)$promoter['commission'];
            //Calculo em cima disso quanto ele faturou para a empresa
            $faturado =

            $data['promoters'][] = array(
                'nome'      => $promoter['name'],
                'vendidos'  => 'todo',
                'comissao'  => $promoter['commission'],
            );
        }


        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_empty'] = $this->language->get('text_empty');

        $data['column_order_id'] = $this->language->get('column_order_id');
        $data['column_name'] = $this->language->get('column_name');
        $data['column_size'] = $this->language->get('column_size');
        $data['column_date_added'] = $this->language->get('column_date_added');

        $data['button_download'] = $this->language->get('button_download');
        $data['button_continue'] = $this->language->get('button_continue');




        $data['continue'] = $this->url->link('account/account', '', true);

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('account/promoters', $data));
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