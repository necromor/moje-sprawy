<?php require APPROOT . '/views/inc/header.php'; ?>

<form action="<?php echo URLROOT; ?>/pracownicy/zmien_haslo/<?php echo $data['id']; ?>" method="post">
  <div class="row mb-3">
    <div class="col-md-8 mx-auto py-3">

         <h1><?php echo $data['title'] ?></h1>

          <div class="form-group row">
            <label for="hasloS" class="col-sm-4 col-form-label">Stare hasło:</label>
            <input type="password" class="form-control col-sm-8 <?php echo (!empty($data['hasloS_err'])) ? 'is-invalid' : ''; ?>" id="hasloS" name="hasloS">
            <span class="invalid-feedback offset-sm-4"><?php echo $data['hasloS_err']; ?></span>
          </div>

          <div class="form-group row">
            <label for="hasloN1" class="col-sm-4 col-form-label">Nowe hasło:</label>
            <input type="password" class="form-control col-sm-8 <?php echo (!empty($data['hasloN1_err'])) ? 'is-invalid' : ''; ?>" id="hasloN1" name="hasloN1">
            <span class="invalid-feedback offset-sm-4"><?php echo $data['hasloN1_err']; ?></span>
          </div>

          <div class="form-group row">
            <label for="hasloN2" class="col-sm-4 col-form-label">Powtórz nowe hasło:</label>
            <input type="password" class="form-control col-sm-8 <?php echo (!empty($data['hasloN2_err'])) ? 'is-invalid' : ''; ?>" id="hasloN2" name="hasloN2">
            <span class="invalid-feedback offset-sm-4"><?php echo $data['hasloN2_err']; ?></span>
          </div>

          <button type="submit" class="col-sm-4 offset-sm-4 btn btn-primary">Zmień hasło</button>

    </div>
  </div>

</form>

<?php require APPROOT . '/views/inc/footer.php';
