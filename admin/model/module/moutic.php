<?php
class ModelModuleMoutic extends Model {

    public function createSchema() {
        //To usando try catch pra n dar pau quando re-instalar e já tiver as paradas no banco
        try {
            $this->db->query("
            ALTER TABLE " . DB_PREFIX . "affiliate 
            ADD COLUMN festa_id int(11) DEFAULT NULL
            ");
        } catch (Exception $e){
            error_log("Exception: $e");
        }

        //Campo da senha de acesso utilizada na API
        try {
            $this->db->query("
            ALTER TABLE " . DB_PREFIX . "customer 
            ADD COLUMN senha_festa VARCHAR(250) DEFAULT NULL
            ");
        } catch (Exception $e){
            error_log("Exception: $e");
        }

        //Campo do QrCode do usuario
        try {
            $this->db->query("
            ALTER TABLE " . DB_PREFIX . "customer 
            ADD COLUMN qrcode VARCHAR(250) DEFAULT NULL
            ");
        } catch (Exception $e){
            error_log("Exception: $e");
        }
    }

    public function deleteSchema() {
        $this->db->query("
            ALTER TABLE " . DB_PREFIX . "affiliate
            DROP COLUMN festa_id int(11) DEFAULT NULL
            ");

        $this->db->query("
            ALTER TABLE " . DB_PREFIX . "customer
            DROP COLUMN senha_festa VARCHAR(250) DEFAULT NULL
            ");

        $this->db->query("
            ALTER TABLE " . DB_PREFIX . "customer
            DROP COLUMN qrcode VARCHAR(250) DEFAULT NULL
            ");
    }
}

?>