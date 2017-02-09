<?php
class ControllerApiMoutic extends Controller {
    public function getCasas(){

        //Pega o modelo de sellers
        $this->load->model('seller/seller');

        //Pega ais
        $casas = $this->model_seller_seller->getsellers();

        foreach($casas as $casa){
            $json[] = array(
                'id'    => $casa['customer_id'],
                'nome'  => $casa['name']
            );
        }
        //Retorna a informação em forma de json
        $this->response->setOutput(json_encode($json));
    }

    public function getFestasFromCasa() {
        $casa_id = $this->request->get['casa_id'];

        //Pega o modelo de sellers
        $this->load->model('seller/seller');

        //Pega as festas(produtos) baseado na casa(seller)
        $festas = $this->model_seller_seller->getProducts(array('filter_seller_id' => $casa_id));
        foreach($festas as $festa){
            $json[] = array(
                'id'    => $festa['product_id'],
                'nome'  => $festa['name']
            );
        }
        //Retorna as festas(produtos)
        $this->response->setOutput(json_encode($json));
    }

    public function verificarSenha(){
        //Verifica se todos os dados foram enviados para a API
        if(!array_key_exists('casa_id', $this->request->get)
            || !array_key_exists('senha', $this->request->get)){
            $this->response->setOutput(json_encode(array('erro' => 'É necessário informar uma casa e uma senha!')));
            return;
        }

        $casa_id = $this->request->get['casa_id'];

        $senha = $this->request->get['senha'];

        $this->load->model('seller/seller');

        $casa = $this->model_seller_seller->getseller($casa_id);

        //Verifica se o id fornecido é de fato de uma casa de festas
        if(!isset($casa) || is_null($casa) || $casa == false){
            $this->response->setOutput(json_encode(array('erro' => 'Casa de festas inválida')));
            return;
        }

        //Verificar se a senha informada é correta
        $senha_festa = $casa['senha_festa'];
        if($senha === $senha_festa){
            $this->response->setOutput(json_encode(array('acesso' => true)));
        } else {
            $this->response->setOutput(json_encode(array('acesso' => false)));
        }
    }

    public function verificarIngressoUsuario(){
        if(!array_key_exists('usuario_id', $this->request->get)
            || !array_key_exists('festa_id', $this->request->get)
            || !array_key_exists('senha', $this->request->get)
            || !array_key_exists('casa_id', $this->request->get)){
            $this->response->setOutput(json_encode(array('erro' => 'É necessário informar uma casa, usuário, festa e uma senha de acesso!')));
            return;
        }

        $casa_id = $this->request->get['casa_id'];

        $usuario_id = $this->request->get['usuario_id'];

        $festa_id = $this->request->get['festa_id'];

        $senha = $this->request->get['senha'];

        if(!$this->validarSenha($senha, $casa_id)){
            $this->response->setOutput(json_encode(array('erro' => 'Você informou algum dado incorretamente, por favor, tente novamente.')));
            return;
        }

        $ingressos = $this->getIngressosUsuario($usuario_id);
        $ingresso_validado = false;
        $ingresso_comprado = null;
        foreach($ingressos as $ingresso) {
            if($ingresso['festa_id'] == $festa_id){
                $ingresso_validado = true;
                $ingresso_comprado = $ingresso;
                break;
            }
        }

        $this->load->model('account/customer');

        $usuario = $this->model_account_customer->getCustomer($usuario_id);

        if($ingresso_validado){
            $resposta = array(
                'nome'      => $usuario['firstname'] . ' ' . $usuario['lastname'],
                'ingresso'  => $ingresso_comprado['ingresso'],
                'utilizado' => $ingresso_comprado['utilizado']
            );

            $this->response->setOutput(json_encode($resposta));

            return;
        } else {
            $this->response->setOutput(json_encode(array('erro' => 'Usuário não comprou ingresso para essa festa.')));
            return;
        }
    }

    public function atualizarIngressoUsuario(){
        if(!array_key_exists('usuario_id', $this->request->get)
            || !array_key_exists('festa_id', $this->request->get)
            || !array_key_exists('senha', $this->request->get)
            || !array_key_exists('casa_id', $this->request->get)){
            $this->response->setOutput(json_encode(array('erro' => 'É necessário informar uma casa, usuário, festa e uma senha de acesso!')));
            return;
        }

        $casa_id = $this->request->get['casa_id'];

        $usuario_id = $this->request->get['usuario_id'];

        $festa_id = $this->request->get['festa_id'];

        $senha = $this->request->get['senha'];

        if(!$this->validarSenha($senha, $casa_id)){
            $this->response->setOutput(json_encode(array('erro' => 'Você informou algum dado incorretamente, por favor, tente novamente.')));
            return;
        }

        $ingressos = $this->getIngressosUsuario($usuario_id);
        $ingresso_validado = false;
        $ingresso_comprado = null;
        foreach($ingressos as $ingresso) {
            if($ingresso['festa_id'] == $festa_id){
                $ingresso_validado = true;
                $ingresso_comprado = $ingresso;
                break;
            }
        }

        if(!$ingresso_validado){
            $this->response->setOutput(json_encode(array('erro' => 'Usuário não comprou ingresso.')));
            return;
        }


        if($ingresso_comprado['utilizado'] == true){
            $this->response->setOutput(json_encode(array('erro' => 'Ingresso já utilizado')));
            return;
        } else {
            $this->load->model('order/order');
            $this->model_order_order->updateIngressoUtilizado($ingresso_comprado['pedido_id'], $ingresso_comprado['ingresso_id']);
            $this->response->setOutput(json_encode(array('sucesso' => 'Ingresso utilizado com sucesso!')));
            return;
        }



    }




    //HELPER METHODS
    private function validarSenha($senha, $casa_id){
        $this->load->model('seller/seller');

        $casa = $this->model_seller_seller->getseller($casa_id);

        //Verifica se o id fornecido é de fato de uma casa de festas
        if(!isset($casa) || is_null($casa) || $casa == false){
            return false;
        }

        //Verificar se a senha informada é correta
        $senha_festa = $casa['senha_festa'];
        if($senha === $senha_festa){
            return true;
        } else {
            return false;
        }
    }

    private function getIngressosUsuario($usuario_id){
        $ingressos = array();
        $data['ingressos'] = array();
        $this->load->model('account/order');
        $results = $this->model_account_order->getOrdersFromCustomer($usuario_id);
        //Percorre as orders para pegar os produtos(ingressos) das mesmas
        foreach($results as $result) {
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
                        'name' => $option['value'],
                    );
                }

                $nome_ingresso = "Não tem option";

                if (isset($option['value'])) {
                    $nome_ingresso = $option['value'];
                }

                $utilizado = (int)$ingresso['utilizado'] == 0 ? false : true;
                //Cria a array com as informações do produto
                $data['ingressos'][] = array(
                    'festa_id'      => $ingresso['product_id'],
                    'ingresso_id'   => $ingresso['order_product_id'],
                    'pedido_id'     => $ingresso['order_id'],
                    'ingresso'      => $nome_ingresso,
                    'utilizado'     => $utilizado,
                );
            }
        }

        return $data['ingressos'];
    }




}
