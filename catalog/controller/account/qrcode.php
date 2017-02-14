<?php
class ControllerAccountQrcode extends Controller {
    private $error = array();

    public function index() {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/edit', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        $this->load->language('account/edit');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

        $this->load->model('account/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_account_customer->editCustomer($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            // Add to activity log
            if ($this->config->get('config_customer_activity')) {
                $this->load->model('account/activity');

                $activity_data = array(
                    'customer_id' => $this->customer->getId(),
                    'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName()
                );

                $this->model_account_activity->addActivity('edit', $activity_data);
            }

            $this->response->redirect($this->url->link('account/account', '', true));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_account'),
            'href'      => $this->url->link('account/account', '', true)
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_edit'),
            'href'      => $this->url->link('account/qrcode', '', true)
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $this->load->model('account/customer');

        $usuario = $this->model_account_customer->getCustomer($this->customer->getId());

        $qrcode = $usuario['qrcode'];

        $data['text_qrcode'] = $qrcode;

        $data['back'] = $this->url->link('account/account', '', true);

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->document->addScript('assets/js/davidshimjs-qrcodejs/qrcode.js');

        $this->response->setOutput($this->load->view('account/qrcode', $data));
    }
}