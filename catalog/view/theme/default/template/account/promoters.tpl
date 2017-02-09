<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <h2>Meus Promoters</h2>
      <h4>Informações sobre seus promoters</h4>
      <table class="table" id="tabela-meus-ingressos">
        <thead>
        <tr>
            <th>Promoter</th>
            <th>Ingressos Vendidos</th>
            <th>Faturado</th>
            <th>Comissão</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>Diogo Rodrigues</td>
                <td>50</td>
                <td>R$250,00</td>
                <td>R$12,50</td>
            </tr>
        </tbody>
      </table>
    </div>

    <?php echo $content_bottom; ?>
    <?php echo $column_right; ?>
</div>
<?php echo $footer; ?>