<?php require APPROOT . '/views/inc/header.php'; ?>

<h1><?php echo $data['title'] ?></h1>
<hr>

  <div class="row">
    <div class="col-md-8 mx-auto">
    <?php echo flash('sprawy_szczegoly'); ?>
    </div>
  </div>

<div class="row">

  <div class="col-md-9 col-12">
    <h2>Metryka sprawy</h2>
    <table class="table table-hover">
      <caption>Metryka sprawy</caption>
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
        <th class="align-middle">Lp</th>
        <th class="align-middle">Data czynności</th>
        <th class="align-middle">Wykonawca czynności</th>
        <th class="align-middle">Wykonana czynność</th>
        <th class="align-middle">Identyfikator <br>dokumentu</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach($data['metryka'] as $m) : ?>
      <tr>
        <td><?php echo $m->id; ?></td>
        <td><?php echo $m->utworzone; ?></td>
        <td><?php echo $m->id_pracownik; ?></td>
        <td><?php echo $m->czynnosc; ?></td>
        <td>
          <?php if ($m->rodzaj_dokumentu != 0 ) : ?>
          <button class="btn btn-block btn-info pokazSzczegoly" id="<?php echo $m->rodzaj_dokumentu . '-' . $m->id_dokument; ?>" title="Wyświetl szczegóły pisma">
          <?php echo $m->dokument; ?>
          </button>
          <?php else : ?>
          <?php echo $m->dokument; ?>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div><!-- /metryka -->

  <div class="col-md-3 col-12">
    <details id="detPisma">
      <summary>Szczegóły wybranego dokumentu</summary>
      <div id="szczegolyKorespondencji">
      <ul class="list-group list-group-flush">
      <li class="list-group-item list-group-item-warning">Nie wybrano dokumentu!</li>
      <li class="list-group-item list-group-item-info">Kliknij na identyfikator dokumentu aby zobaczyć jego szczegóły.</li>
      </ul>
      </div>
    </details>
    <h2>Operacje</h2>
    <a href="<?php echo URLROOT; ?>/sprawy/dodaj_przychodzace/<?php echo $data['id']; ?>" class="btn btn-block btn-info">Przypisz przychodzące</a>
    <a href="<?php echo URLROOT; ?>/sprawy/dodaj_wychodzace/<?php echo $data['id']; ?>" class="btn btn-block btn-info">Dodaj wychodzące</a>
    <a href="<?php echo URLROOT; ?>/sprawy/dodaj_inny/<?php echo $data['id']; ?>" class="btn btn-block btn-info">Dodaj inny dokument</a>
    <a href="<?php echo URLROOT; ?>/sprawy/edytuj/<?php echo $data['id']; ?>" class="btn btn-block btn-dark">Edytuj temat sprawy</a>
    <?php if ($data['zakonczona']) : ?>
    <a href="<?php echo URLROOT; ?>/sprawy/wznow/<?php echo $data['id']; ?>" class="btn btn-block btn-success">Wznów sprawę</a>
    <?php else : ?>
    <a href="<?php echo URLROOT; ?>/sprawy/zakoncz/<?php echo $data['id']; ?>" class="btn btn-block btn-danger">Zakończ sprawę</a>
    <?php endif; ?>
  </div>

</div><!-- /row metryka + guziki -->

<?php require APPROOT . '/views/inc/footer.php'; ?>
