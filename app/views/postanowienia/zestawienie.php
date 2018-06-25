<?php require APPROOT . '/views/inc/header.php'; ?>

<h1><?php echo $data['title'] ?> z <?php echo $data['rok']; ?> roku</h1>

<div class="row">
  <div class="col-md-8 mx-auto">
  <?php echo flash('postanowienia_edytuj'); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <form class="form-inline" action="<?php echo URLROOT; ?>/postanowienia/zestawienie" method="post">
      <label for="rok" class="sr-only">Rok</label>
      <input type="number" name="rok" id="rok" class="form-control mr-sm-2 mb-2" value="<?php echo $data['rok']; ?>" min="2016" max="2100" step="1">
      <button type="submit" class="btn btn-primary mb-2">Zmień rok zestawienia</button>
    </form>
    </div>
    <div class="col-md-8">
    <form class="form-inline" action="<?php echo URLROOT; ?>/postanowienia/zestawienie" method="post">
    <input type="hidden" name="rok" value="<?php echo $data['rok']; ?>">
      <label for="numerJrwa" class="sr-only">Numer jrwa</label>
      <input type="text" name="jrwa" id="numerJrwa" class="form-control mr-sm-2 mb-2" list="listaJrwa" value="<?php echo $data['jrwa']; ?>">
      <button type="submit" class="btn btn-primary mb-2">Pokaż postanowienia wybranego jrwa</button>
    </form>
  </div>
</div>

<table class="table table-hover">
  <caption>Zestawienie postanowień wystawionych w roku <?php echo $data['rok']; ?>.</caption>
  <thead class="thead-dark">
  <tr>
    <th class="align-middle">Znak sprawy</th>
    <th class="align-middle">Oznaczenie postanowienia</th>
    <th class="align-middle">Podmiot postanowienia</th>
    <th class="align-middle">Data postanowienia</th>
    <th class="align-middle">Dotyczy</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach($data['postanowienia'] as $postanowienie) : ?>
    <td class="align-middle"><a href="<?php echo URLROOT; ?>/sprawy/szczegoly/<?php echo $postanowienie->sprawaId; ?>" title="Zobacz szczegóły sprawy"><?php echo $postanowienie->znak; ?></a></td>
    <td class="align-middle"><a href="<?php echo URLROOT; ?>/postanowienia/edytuj/<?php echo $postanowienie->postanowienieId; ?>" title="Zmień dane postanowienia"><?php echo $postanowienie->postanowienieNumer; ?></a></td>
    <td class="align-middle"><?php echo $postanowienie->nazwa; ?></br>
        <?php echo $postanowienie->adres_1; ?><br>
        <?php echo $postanowienie->adres_2; ?></td>
    <td class="align-middle"><?php echo $postanowienie->postanowienieData; ?></td>
    <td class="align-middle"><?php echo $postanowienie->postanowienieDotyczy; ?></td>
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
