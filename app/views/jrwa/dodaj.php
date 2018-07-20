<?php require APPROOT . '/views/inc/header.php'; ?>

<form action="<?php echo URLROOT; ?>/jrwa/dodaj" method="post">

  <div class="row">
    <h1 class="col-md-8 mx-auto"><?php echo $data['title'] ?></h1>
  </div>

  <div class="row mb-3">
    <div class="col-md-8 mx-auto">

        <div class="form-group row py-2">
          <div class="form-check col-9 offset-3">
            <input class="form-check-input" type="radio" name="czyGrupa" id="radioPojedynczy" value="0" <?php if($data['czy_grupa'] == '0') {echo "checked";} ?>>
            <label class="form-check-label" for="radioPojedynczy">Pojedynczy numer</label>
          </div>
          <div class="form-check col-9 offset-3">
            <input class="form-check-input" type="radio" name="czyGrupa" id="radioGrupa" value="1" <?php if($data['czy_grupa'] == '1') {echo "checked";} ?>>
            <label class="form-check-label" for="radioGrupa">Grupę numerów</label>
          </div>
        </div>

        <div class="form-group row" id="jrwaPojedynczy1">
          <label for="numer" class="col-sm-4 col-form-label">Numer jrwa:</label>
          <input type="number" class="form-control col-sm-7 col-md-3" id="numer" name="numer" min="0" max="9999" value="<?php echo $data['numer']; ?>">
          <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['numer_err']; ?></span>
        </div>

        <div class="form-group row" id="jrwaPojedynczy2">
          <label for="opis" class="col-sm-4 col-form-label">Opis:</label>
          <input type="text" class="form-control col-sm-7" id="opis" name="opis" value="<?php echo $data['opis']; ?>">
          <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['opis_err']; ?></span>
        </div>

        <div class="form-group row" id="jrwaGrupa">
          <label for="grupa" class="col-12 col-form-label">Grupa numerów:</label>
          <textarea class="form-control col-12" id="grupa" name="grupa" rows="8" placeholder="Każda linia w postaci @numer:opis"><?php echo $data['grupa']; ?></textarea>
          <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['grupa_err']; ?></span>
        </div>

    </div>
  </div>

  <button type="submit" class="btn btn-primary col-sm-4 offset-sm-4">Dodaj</button>

</form>

<?php require APPROOT . '/views/inc/footer.php'; ?>

