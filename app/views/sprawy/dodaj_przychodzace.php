<?php require APPROOT . '/views/inc/header.php'; ?>

<h1><?php echo $data['title'] ?></h1>
<hr>
<a href="<?php echo URLROOT; ?>/sprawy/szczegoly/<?php echo $data['id']; ?>" class="btn btn-info col-md-6 col-12 btn-block mx-auto mb-3">
  <i class="fa fa-angle-double-left"></i> Wróć do szczegółów sprawy
</a>

<div class="row">

  <div class="col-12">
    <table class="table table-hover table-responsive-md">
      <caption>Zestawienie pism przychodzących bez sprawy i nie oznaczonych jako ad acta, które zadekretowane są do zalogowanego pracownika.</caption>
      <thead class="thead-dark">
      <tr>
        <th colspan="2">Znak sprawy</th>
        <th colspan="3" class="bg-transparent text-dark"><?php echo $data['sprawa']->znak; ?></th>
      </tr>
      <tr>
        <th colspan="2">Temat sprawy</th>
        <th colspan="3" class="bg-transparent text-dark"><?php echo $data['sprawa']->temat; ?></th>
      </tr>
      <tr>
        <th class="align-middle">Nr rejestru</th>
        <th class="align-middle">Znak <br> [Wpływ]</th>
        <th class="align-middle">Nadawca</th>
        <th class="align-middle">Dotyczy</th>
        <th class="align-middle">Przypisz</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach($data['pisma'] as $p) : ?>
      <tr>
        <td><?php echo $p->nr_rejestru; ?></td>
        <td><?php echo $p->znak; ?> <br> [<?php echo $p->data_wplywu; ?>]</td>
        <td><?php echo $p->nazwa; ?></td>
        <td><?php echo $p->dotyczy; ?></td>
        <td><a href="<?php echo URLROOT; ?>/sprawy/dodaj_przychodzace/<?php echo $data['id']; ?>/<?php echo $p->id; ?>" class="btn btn-success">Przypisz</a></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div><!-- /row -->

<?php require APPROOT . '/views/inc/footer.php'; ?>
