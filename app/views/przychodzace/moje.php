<?php require APPROOT . '/views/inc/header.php'; ?>

<h1><?php echo $data['title'] ?></h1>
<hr>

<div class="row">
  <div class="col-md-8 mx-auto">
  <?php echo flash('moje_info'); ?>
  </div>
</div>

<div class="row">

  <h2 class="col-12">Moje pisma bez sprawy i nie oznaczone ad acta</h2>

  <div class="col-12">
    <table class="table table-hover">
      <caption>Zestawienie pism przychodzących bez sprawy i nie oznaczonych jako ad acta, które zadekretowane są do zalogowanego pracownika.</caption>
      <thead class="thead-dark">
      <tr>
        <th class="align-middle">Nr rejestru</th>
        <th class="align-middle">Znak <br> [Wpływ]</th>
        <th class="align-middle">Nadawca</th>
        <th class="align-middle">Dotyczy</th>
        <th class="align-middle">Oznacz ad acta</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach($data['pisma'] as $p) : ?>
      <tr>
        <td><?php echo $p->nr_rejestru; ?></td>
        <td><?php echo $p->znak; ?> <br> [<?php echo $p->data_wplywu; ?>]</td>
        <td><?php echo $p->nazwa; ?></td>
        <td><?php echo $p->dotyczy; ?></td>
        <td><a href="<?php echo URLROOT; ?>/przychodzace/adacta/<?php echo $p->id; ?>" class="btn btn-success">Ad acta <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div><!-- /row -->

<div class="row">

  <h2 class="col-12">Ostatnie sprawy z moją aktywnością</h2>

  <?php foreach($data['sprawy'] as $s) : ?>
  <a href="<?php echo URLROOT; ?>/sprawy/szczegoly/<?php echo $s->id; ?>" class="btn btn-info col-auto mx-2" title="Zobacz szczegóły sprawy"><?php echo $s->znak; ?></a>
  <?php endforeach; ?>

</div><!-- /row -->

<?php require APPROOT . '/views/inc/footer.php'; ?>
