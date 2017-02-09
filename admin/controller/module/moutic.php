<?php
class ControllerModuleMoutic extends Controller {
    public function index() {
        $this->install();
    }

    public function install() {
        $this->load->model("module/moutic");
        $this->model_module_moutic->createSchema();
    }


    public function uninstall() {
        $this->load->model("module/moutic");
        $this->model_module_moutic->deleteSchema();
    }
}
?>

