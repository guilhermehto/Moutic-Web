<?php echo $header; ?>
<div class="container">
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
    <?php } ?>
    <div class="row"><?php echo $column_left; ?>
        <?php if ($column_left && $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } elseif ($column_left || $column_right) { ?>
        <?php $class = 'col-sm-9'; ?>
        <?php } else { ?>
        <?php $class = 'col-sm-12'; ?>
        <?php } ?>
        <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
            <h2>Meu Código</h2>
            <p>Esse é o código que você deve mostrar na entrada da festa.</p>
            <p>Se você sabe que não terá internet na entrada, tire um <i>print</i> para que seu qrcode fique salvo em suas imagens.</p>
            <div id="js-codigo">
            </div>
            <?php echo $content_bottom; ?></div>
        <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>
<script src="assets/js/davidshimjs-qrcodejs/qrcode.js"></script>
<script>
    $(document).ready(function () {
        var qrcode = new QRCode('js-codigo');
        var qrcodetexto = '<?php echo $text_qrcode ?>';
        qrcode.makeCode(qrcodetexto);
    })
</script>


