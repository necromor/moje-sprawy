<?php require APPROOT . '/views/inc/header.php'; ?>

<h1><?php echo $data['title'] ?> z <?php echo $data['rok']; ?> roku</h1>

<form class="form-inline" action="<?php echo URLROOT; ?>/przychodzace/zestawienie" method="post">
  <label for="rok" class="sr-only">Rok</label>
  <input type="number" name="rok" id="rok" class="form-control mr-sm-2 mb-2" value="<?php echo $data['rok']; ?>" min="2016" max="2100" step="1">
  <button type="submit" class="btn btn-primary mb-2">Zmień rok zestawienia</button>
</form>

<table class="table table-hover">
  <caption>Zestawienie faktur, które wpłynęły w roku <?php echo $data['rok']; ?>.</caption>
  <thead class="thead-dark">
  <tr>
    <th class="align-middle">Nr w rejestrach</th>
    <th class="align-middle">Data faktury/wpływu</th>
    <th class="align-middle">Oznaczenie</th>
    <th class="align-middle">Wystawca</th>
    <th class="align-middle">Dotyczy</th>
    <th class="align-middle">Kwota</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach($data['faktury'] as $faktura) : ?>
  <tr>
    <td class="align-middle"><?php echo $faktura->nr_rejestru_faktur; ?>
    (<a href="<?php echo URLROOT; ?>/przychodzace/edytuj/<?php echo $faktura->id; ?>" title="Zmień dane faktury"><?php echo $faktura->nr_rejestru; ?></a>)</td>
    <td class="align-middle"><?php echo $faktura->data_pisma; ?><br>
        <?php echo $faktura->data_wplywu; ?></td>
    <td class="align-middle"><?php echo $faktura->znak; ?></td>
    <td class="align-middle"><?php echo $faktura->nazwa; ?></br>
        <?php echo $faktura->adres_1; ?><br>
        <?php echo $faktura->adres_2; ?></td>
    <td class="align-middle"><?php echo $faktura->dotyczy; ?></td>
    <td class="align-middle"><?php echo $faktura->kwota; ?> zł </td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require APPROOT . '/views/inc/footer.php'; ?>
