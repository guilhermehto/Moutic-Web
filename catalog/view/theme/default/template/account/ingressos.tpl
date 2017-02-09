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
      <h2>Meus Ingressos</h2>
      <?php if($ingressos_total == 0) { ?>
      <h5>Oh! Você ainda não comprou nenhum ingresso! :(</h5>
      <?php } else { ?>
      <h4>Aqui você encontra todos os seus ingressos.</h4>
      <table class="table" id="tabela-meus-ingressos">
        <thead>
        <tr>
            <th>Festa</th>
            <th>Ingresso</th>
            <th>Utilizado</th>
            <th>Código</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach($ingressos as $ingresso) { ?>
            <tr>
                <td><?php echo $ingresso['festa'] ?></td>
                <td><?php echo $ingresso['ingresso'] ?></td>
                <td><?php echo $ingresso['utilizado'] ?></td>
                <td><?php echo $ingresso['codigo'] ?></td>
            </tr>

        <?php } ?>
        </tbody>
      </table>
      <?php } ?>
    </div>

    <?php echo $content_bottom; ?>
    <?php echo $column_right; ?>
</div>
<?php echo $footer; ?>