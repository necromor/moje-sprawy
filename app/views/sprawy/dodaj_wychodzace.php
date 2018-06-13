<?php require APPROOT . '/views/inc/header.php'; ?>

<form action="<?php URLROOT; ?>/sprawy/dodaj_wychodzace/<?php echo $data['id']; ?>" method="post">

  <div class="row">
    <h1 class="col-md-8 mx-auto"><?php echo $data['title'] ?></h1>
  </div>

  <div class="row mb-3"><!-- nadawca -->
    <div class="col-md-8 mx-auto bg-light py-3">
       <div id="danePodmiotu">

        <div class="form-group row py-2">
            <div class="form-check form-check-inline offset-3">
              <input class="form-check-input" type="radio" name="czyNowy" id="radioIstniejacy" value="0" <?php if($data['czy_nowy'] == '0') {echo "checked";} ?>>
              <label class="form-check-label" for="radioIstniejacy">Istniejący nadawca</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="czyNowy" id="radioNowy" value="1" <?php if($data['czy_nowy'] == '1') {echo "checked";} ?>>
              <label class="form-check-label" for="radioNowy">Nowy nadawca</label>
            </div>
          </div>

          <div class="form-group row">
            <label for="nazwaPodmiotu" class="col-sm-4 col-form-label">Nazwa:</label>
            <input type="text" class="form-control col-sm-8" id="nazwaPodmiotu" name="nazwaPodmiotu" list="listaPodmiotow" value="<?php echo $data['podmiot_nazwa']; ?>">
            <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['podmiot_nazwa_err']; ?></span>
          </div>

          <div class="form-group row">
            <label for="adresPodmiotu" class="col-sm-4 col-form-label">Ulica (miejscowość):</label>
            <input type="text" class="form-control col-sm-8" id="adresPodmiotu" name="adresPodmiotu" value="<?php echo $data['podmiot_adres']; ?>" <?php echo ($data['czy_nowy'] == '0') ? 'disabled' : ''; ?>>
            <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['podmiot_adres_err']; ?></span>
          </div>

          <div class="form-group row">
            <label for="pocztaPodmiotu" class="col-sm-4 col-form-label">Kod pocztowy i poczta:</label>
            <input type="text" class="form-control col-sm-8" id="pocztaPodmiotu" name="pocztaPodmiotu" value="<?php echo $data['podmiot_poczta']; ?>" <?php echo ($data['czy_nowy'] == '0') ? 'disabled' : ''; ?>>
            <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['podmiot_poczta_err']; ?></span>
          </div>
       </div><!-- /danePodmiotu -->
    </div>
  </div><!-- /nadawca -->

  <div class="row"><!-- część wspólna -->
    <div class="col-md-8 mx-auto">

      <div class="form-group row">
        <label for="dotyczy" class="col-sm-4 col-form-label">Dotyczy:</label>
        <textarea class="form-control col-sm-8" id="dotyczy" name="dotyczy" rows="3"><?php echo $data['dotyczy']; ?></textarea>
        <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['dotyczy_err']; ?></span>
      </div>

    </div>
  </div><!-- /część wspólna -->

  <div class="row mb-3"><!-- decyzja-postanowienie -->
    <div class="col-md-8 mx-auto bg-light py-3">

        <div class="form-group row py-2">
          <div class="form-check form-check-inline offset-3">
            <input class="form-check-input" type="radio" name="radioDP" id="radioDP0" value="0" <?php if($data['czy_dp'] == '0') {echo "checked";} ?>>
            <label class="form-check-label" for="radioDP0">Zwykłe pismo</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="radioDP" id="radioDP1" value="1" <?php if($data['czy_dp'] == '1') {echo "checked";} ?>>
            <label class="form-check-label" for="radioDP1">Decyzja</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="radioDP" id="radioDP2" value="2" <?php if($data['czy_dp'] == '2') {echo "checked";} ?>>
            <label class="form-check-label" for="radioDP2">Postanowienie</label>
          </div>
        </div>

        <div class="form-group row wychodzace-dp">
          <label for="oznaczenieDP" class="col-sm-4 col-form-label">Oznaczenie:</label>
          <input type="text" class="form-control col-sm-7" id="oznaczenieDP" name="oznaczenieDP" value="<?php echo $data['oznaczenie_dp']; ?>">
          <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['oznaczenie_dp_err']; ?></span>
        </div>

        <div class="form-group row wychodzace-dp">
          <label for="dotyczyDP" class="col-4 col-form-label">Dotyczy</label>
          <textarea class="form-control col-sm-8" id="dotyczyDP" name="dotyczyDP" rows="3"><?php echo $data['dotyczy_dp']; ?></textarea>
          <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['dotyczy_dp_err']; ?></span>
        </div>

    </div>
  </div><!-- /decyzja-postanowienie -->

  <div class="form-group row">
    <div class="col-sm-8 offset-sm-4">
      <button type="submit" class="btn btn-primary">Dodaj pismo</button>
      <a href="<?php echo URLROOT; ?>/sprawy/szczegoly/<?php echo $data['id']; ?>" class="btn btn-info"><i class="fa fa-angle-double-left"></i> Wróć do szczegółów sprawy</a>
    </div>
  </div>

  <!-- listy -->
  <datalist id="listaPodmiotow">
    <?php foreach($data['podmioty'] as $podmiot) : ?>
    <option value="<?php echo $podmiot->id; ?># <?php echo $podmiot->nazwa; ?>">
    <?php endforeach; ?>
  </datalist>
  <!-- / listy -->

</form>

<?php require APPROOT . '/views/inc/footer.php'; ?>

