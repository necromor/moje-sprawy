<?php require APPROOT . '/views/inc/header.php'; ?>

<h1><?php echo $data['title'] ?> z <?php echo $data['rok']; ?> roku</h1>

<form class="form-inline" action="<?php echo URLROOT; ?>/przychodzace/zestawienie" method="post">
  <label for="rok" class="sr-only">Rok</label>
  <input type="number" name="rok" id="rok" class="form-control mr-sm-2 mb-2" value="<?php echo $data['rok']; ?>" min="2016" max="2100" step="1">
  <button type="submit" class="btn btn-primary mb-2">Zmień rok zestawienia</button>
</form>

<table class="table table-hover">
  <caption>Zestawienie korespondencji przychodzącej, która wpłynęła w roku <?php echo $data['rok']; ?>.</caption>
  <thead class="thead-dark">
  <tr>
    <th class="align-middle">Nr rejestru</th>
    <th class="align-middle">Data pisma<br>Data wpływu</th>
    <th class="align-middle">Znak</th>
    <th class="align-middle">Nadawca</th>
    <th class="align-middle">Dotyczy</th>
    <th class="align-middle">Uwagi</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach($data['pisma'] as $pismo) : ?>
  <tr>
    <td class="align-middle text-center"><a href="<?php echo URLROOT; ?>/przychodzace/edytuj/<?php echo $pismo->id; ?>" title="Zmień dane korespondencji">
        <?php echo $pismo->nr_rejestru; ?></a></td>
    <td class="align-middle"><?php echo $pismo->data_pisma; ?><br>
        <?php echo $pismo->data_wplywu; ?></td>
    <td class="align-middle"><?php echo $pismo->znak; ?></td>
    <td class="align-middle"><?php echo $pismo->nazwa; ?></br>
        <?php echo $pismo->adres_1; ?><br>
        <?php echo $pismo->adres_2; ?></td>
    <td class="align-middle"><?php echo $pismo->dotyczy; ?></td>
    <?php if($pismo->czy_faktura == 0) : ?>
    <td class="align-middle">Liczba załączników: <?php echo $pismo->liczba_zalacznikow; ?> <br>
        Dekretacja: <?php echo $pismo->id_pracownik; ?></td>
    <?php else : ?>
    <td class="align-middle">Kwota: <?php echo $pismo->kwota; ?> PLN <br>
        Nr w rejestrze faktur: <?php echo $pismo->nr_rejestru_faktur; ?></td>
    <?php endif; ?>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require APPROOT . '/views/inc/footer.php'; ?>
