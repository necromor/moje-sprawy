<?php require APPROOT . '/views/inc/header.php'; ?>

<h1><?php echo $data['title'] ?> z <?php echo $data['rok']; ?> roku</h1>

  <div class="row">
    <div class="col-md-8 mx-auto">
    <?php echo flash('wychodzace_edytuj'); ?>
    </div>
  </div>

<form class="form-inline" action="<?php echo URLROOT; ?>/wychodzace/zestawienie" method="post">
  <label for="rok" class="sr-only">Rok</label>
  <input type="number" name="rok" id="rok" class="form-control mr-sm-2 mb-2" value="<?php echo $data['rok']; ?>" min="2016" max="2100" step="1">
  <button type="submit" class="btn btn-primary mb-2">Zmień rok zestawienia</button>
</form>

<table class="table table-hover">
  <caption>Zestawienie pism wychodzących, dodanych w roku <?php echo $data['rok']; ?>.</caption>
  <thead class="thead-dark">
  <tr>
    <th class="align-middle">Data pisma</th>
    <th class="align-middle">Znak sprawy</th>
    <th class="align-middle">Odbiorca</th>
    <th class="align-middle">Dotyczy</th>
    <th class="align-middle">Dostarczone</th>
    <th class="align-middle">Uwagi</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach($data['pisma'] as $pismo) : ?>
  <tr>
    <td class="align-middle"><a href="<?php echo URLROOT; ?>/wychodzace/edytuj/<?php echo $pismo->id; ?>" title="Zmień dane pisma"><?php echo $pismo->utworzone; ?></a></td>
    <td class="align-middle"><a href="<?php echo URLROOT; ?>/sprawy/szczegoly/<?php echo $pismo->sprawaId; ?>" title="Zobacz szczegóły sprawy"><?php echo $pismo->znak; ?></a></td>
    <td class="align-middle"><?php echo $pismo->nazwa; ?></br>
        <?php echo $pismo->adres_1; ?><br>
        <?php echo $pismo->adres_2; ?></td>
    <td class="align-middle"><?php echo $pismo->dotyczy; ?></td>
    <?php if($pismo->data_wyjscia == NULL) : ?>
    <td class="align-middle">pismo nie zostało dostarczone</td>
    <?php else : ?>
    <td class="align-middle">sposób odbioru</td>
    <?php endif; ?>
    <td class="align-middle">decyzja czy postanowienie</td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require APPROOT . '/views/inc/footer.php'; ?>
