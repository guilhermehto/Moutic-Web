<?php

class ControllerModuleMoutic extends Controller {

    public function senha() {
        $json = array();

        //Não precisamos encryptar ela aqui pois ela vai ser encryptada na função
        //Que adciona ela no banco utilizando $senha_encrypt = hash('ripemd160', $senha);
        $senha = $this->request->post['input-senha'];
        $erro = false;
        if(strlen($senha) < 6) {
            $json['error'] = 'Sua senha precisa ter no mínimo 6 caracteres.';
            $erro = true;
        }

        if(!$erro) {
            $id_usuario = $this->customer->getId();

            $this->load->model('account/customer');

            $this->load->model('account/activity');

            //Adiciona activity na dash
            $activity_data = array(
                'customer_id' => $this->customer->getId(),
                'name' => $this->customer->getFirstName() . ' ' . $this->customer->getLastName(),
            );

            //Adiciona atividade para dash de admin
            $this->model_account_activity->addActivity('senha_acesso_update', $activity_data);

            //Atualiza a senha de acesso
            $this->model_account_customer->editSenhaAcesso($senha, $id_usuario);

            $json['success'] = 'Senha atualizada com sucesso!';
        }
        //Envia resposta
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
