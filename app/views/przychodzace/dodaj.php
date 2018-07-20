<?php require APPROOT . '/views/inc/header.php'; ?>

<form action="<?php echo URLROOT; ?>/przychodzace/dodaj" method="post">

  <div class="row">
    <h1 class="col-md-8 mx-auto"><?php echo $data['title'] ?></h1>
  </div>

  <div class="row">
    <div class="col-md-8 mx-auto">
    <?php echo flash('korespondencja_dodaj'); ?>
    </div>
  </div>

  <div class="row mb-3"><!-- nadawca -->
    <div class="col-md-8 mx-auto bg-light py-3">
       <div id="danePodmiotu">

        <div class="form-group row py-2">
            <div class="form-check col-9 offset-3">
              <input class="form-check-input" type="radio" name="czyNowy" id="radioIstniejacy" value="0" <?php if($data['czy_nowy'] == '0') {echo "checked";} ?>>
              <label class="form-check-label" for="radioIstniejacy">Istniejący nadawca</label>
            </div>
            <div class="form-check col-9 offset-3">
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
        <label for="znak" class="col-sm-4 col-form-label">Oznaczenie pisma:</label>
        <input type="text" class="form-control col-sm-8" id="znak" name="znak" placeholder="wpisz brak jeżeli pismo nie ma oznaczenia" value="<?php echo $data['znak']; ?>">
        <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['znak_err']; ?></span>
      </div>

      <div class="form-group row">
        <label for="dataPisma" class="col-5 col-sm-4 col-form-label">Data pisma:</label>
        <input type="date" class="form-control col-6 col-sm-5" id="dataPisma" name="dataPisma" value="<?php echo $data['data_pisma']; ?>">
        <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['data_pisma_err']; ?></span>
      </div>

      <div class="form-group row">
        <label for="dataWplywu" class="col-5 col-sm-4 col-form-label">Data wpływu:</label>
        <input type="date" class="form-control col-6 col-sm-5" id="dataWplywu" name="dataWplywu" value="<?php echo $data['data_wplywu']; ?>">
        <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['data_wplywu_err']; ?></span>
      </div>

      <div class="form-group row">
        <label for="dotyczy" class="col-sm-4 col-form-label">Dotyczy:</label>
        <textarea class="form-control col-sm-8" id="dotyczy" name="dotyczy" rows="3"><?php echo $data['dotyczy']; ?></textarea>
        <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['dotyczy_err']; ?></span>
      </div>

    </div>
  </div><!-- /część wspólna -->

  <div class="row mb-3"><!-- pismo-faktura -->
    <div class="col-md-8 mx-auto bg-light py-3">

        <div class="form-group row py-2">
          <div class="form-check col-9 offset-3">
            <input class="form-check-input" type="radio" name="czyFaktura" id="radioPismo" value="0" <?php if($data['czy_faktura'] == '0') {echo "checked";} ?>>
            <label class="form-check-label" for="radioPismo">Pismo</label>
          </div>
          <div class="form-check col-9 offset-3">
            <input class="form-check-input" type="radio" name="czyFaktura" id="radioFaktura" value="1" <?php if($data['czy_faktura'] == '1') {echo "checked";} ?>>
            <label class="form-check-label" for="radioFaktura">Faktura</label>
          </div>
        </div>

        <div class="form-group row" id="pismoRow1">
          <label for="liczbaZalacznikow" class="col-6 col-sm-4 col-form-label">Liczba załączników:</label>
          <input type="number" class="form-control col-6 col-sm-3" id="liczbaZalacznikow" name="liczbaZalacznikow" min="0" max="99" value="<?php echo $data['liczba_zalacznikow']; ?>">
          <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['liczba_zalacznikow_err']; ?></span>
        </div>

        <div class="form-group row" id="pismoRow2">
          <label for="dekretacja" class="col-sm-4 col-form-label">Zadekretowane do:</label>
          <input type="text" class="form-control col-sm-7" id="dekretacja" name="dekretacja" list="listaPracownikow" value="<?php echo $data['dekretacja']; ?>">
          <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['dekretacja_err']; ?></span>
        </div>

        <div class="form-group row" id="fakturaRow1">
          <label for="kwota" class="col-4 col-form-label">Kwota:</label>
          <input type="number" class="form-control col-8 col-sm-5" id="kwota" name="kwota" step="0.01" value="<?php echo $data['kwota']; ?>">
          <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['kwota_err']; ?></span>
        </div>

    </div>
  </div><!-- /pismo-faktura -->

  <button type="submit" class="btn btn-primary col-sm-4 offset-sm-4 mb-4">Dodaj</button>

  <!-- listy -->
  <datalist id="listaPodmiotow">
    <?php foreach($data['podmioty'] as $podmiot) : ?>
    <option value="<?php echo $podmiot->id; ?># <?php echo $podmiot->nazwa; ?>">
    <?php endforeach; ?>
  </datalist>

  <datalist id="listaPracownikow">
    <?php foreach($data['pracownicy'] as $pracownik) : ?>
    <option value="<?php echo $pracownik->id; ?># <?php echo $pracownik->imie;?> <?php echo $pracownik->nazwisko; ?>">
    <?php endforeach; ?>
  </datalist>
  <!-- / listy -->

</form>

<?php require APPROOT . '/views/inc/footer.php'; ?>

