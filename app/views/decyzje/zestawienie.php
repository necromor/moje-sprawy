<?php require APPROOT . '/views/inc/header.php'; ?>

<h1><?php echo $data['title'] ?> z <?php echo $data['rok']; ?> roku</h1>

<div class="row">
  <div class="col-md-8 mx-auto">
  <?php echo flash('decyzje_edytuj'); ?>
  </div>
</div>

<div class="row">

  <div class="col-md-5">
    <form class="form-inline" action="<?php echo URLROOT; ?>/decyzje/zestawienie" method="post">
      <label for="rok" class="sr-only">Rok</label>
      <input type="number" name="rok" id="rok" class="form-control mr-sm-2 mb-2" value="<?php echo $data['rok']; ?>" min="2016" max="2100" step="1">
      <button type="submit" class="btn btn-primary mb-2 col">Zmień rok zestawienia</button>
    </form>
    </div>

    <div class="col-md-7">
    <form class="form-inline" action="<?php echo URLROOT; ?>/decyzje/zestawienie" method="post">
    <input type="hidden" name="rok" value="<?php echo $data['rok']; ?>">
      <label for="numerJrwa" class="sr-only">Numer jrwa</label>
      <input type="text" name="jrwa" id="numerJrwa" class="form-control mr-sm-2 mb-2 col-12 col-sm-4" list="listaJrwa" value="<?php echo $data['jrwa']; ?>">
      <button type="submit" class="btn btn-primary mb-2 col">Pokaż decyzje wybranego jrwa</button>
    </form>
  </div>

</div>

<table class="table table-hover table-responsive-md">
  <caption>Zestawienie decyzji wystawionych w roku <?php echo $data['rok']; ?>.</caption>
  <thead class="thead-dark">
  <tr>
    <th class="align-middle">Znak sprawy</th>
    <th class="align-middle">Oznaczenie decyzji</th>
    <th class="align-middle">Podmiot decyzji</th>
    <th class="align-middle">Data decyzji</th>
    <th class="align-middle">Dotyczy</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach($data['decyzje'] as $decyzja) : ?>
    <td class="align-middle"><a href="<?php echo URLROOT; ?>/sprawy/szczegoly/<?php echo $decyzja->sprawaId; ?>" title="Zobacz szczegóły sprawy"><?php echo $decyzja->znak; ?></a></td>
    <td class="align-middle"><a href="<?php echo URLROOT; ?>/decyzje/edytuj/<?php echo $decyzja->decyzjaId; ?>" title="Zmień dane decyzji"><?php echo $decyzja->decyzjaNumer; ?></a></td>
    <td class="align-middle"><?php echo $decyzja->nazwa; ?></br>
        <?php echo $decyzja->adres_1; ?><br>
        <?php echo $decyzja->adres_2; ?></td>
    <td class="align-middle"><?php echo $decyzja->decyzjaData; ?></td>
    <td class="align-middle"><?php echo $decyzja->decyzjaDotyczy; ?></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>

  <!-- lista -->
  <datalist id="listaJrwa">
    <?php foreach($data['jrwaLista'] as $jrwa) : ?>
    <option value="<?php echo $jrwa->numer; ?>">
    <?php endforeach; ?>
  </datalist>
  <!-- /lista -->

<?php require APPROOT . '/views/inc/footer.php'; ?>
