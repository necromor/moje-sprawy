<?php require APPROOT . '/views/inc/header.php'; ?>

<form action="<?php echo URLROOT; ?>/jrwa/edytuj/<?php echo $data['id']; ?>" method="post">

  <div class="row">
    <h1 class="col-md-8 mx-auto"><?php echo $data['title'] ?></h1>
  </div>

  <div class="row mb-3">
    <div class="col-md-8 mx-auto">

        <div class="form-group row">
          <label for="numer" class="col-sm-4 col-form-label">Numer jrwa:</label>
          <input type="number" class="form-control col-sm-7 col-md-3" id="numer" name="numer" min="0" max="9999" value="<?php echo $data['numer']; ?>">
          <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['numer_err']; ?></span>
        </div>

        <div class="form-group row">
          <label for="opis" class="col-sm-4 col-form-label">Opis:</label>
          <input type="text" class="form-control col-sm-7" id="opis" name="opis" value="<?php echo $data['opis']; ?>">
          <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['opis_err']; ?></span>
        </div>

    </div>
  </div>

  <button type="submit" class="btn btn-primary col-sm-4 offset-sm-4">Zatwierdź zmiany</button>

</form>

<?php require APPROOT . '/views/inc/footer.php'; ?>

