<?php require APPROOT . '/views/inc/header.php'; ?>

<form action="<?php echo URLROOT; ?>/pracownicy/dodaj" method="post">
  <div class="row mb-3">
    <div class="col-md-8 mx-auto py-3">

         <h1><?php echo $data['title'] ?></h1>

          <div class="form-group row">
            <label for="imie" class="col-sm-4 col-form-label">Imię:</label>
            <input type="text" class="form-control col-sm-8 <?php echo (!empty($data['imie_err'])) ? 'is-invalid' : ''; ?>" id="imie" name="imie" value="<?php echo $data['imie']; ?>">
            <span class="invalid-feedback offset-sm-4"><?php echo $data['imie_err']; ?></span>
          </div>

          <div class="form-group row">
            <label for="nazwisko" class="col-sm-4 col-form-label">Nazwisko:</label>
            <input type="text" class="form-control col-sm-8 <?php echo (!empty($data['nazwisko_err'])) ? 'is-invalid' : ''; ?>" id="nazwisko" name="nazwisko" value="<?php echo $data['nazwisko']; ?>">
            <span class="invalid-feedback offset-sm-4"><?php echo $data['nazwisko_err']; ?></span>
          </div>

          <div class="form-group row">
            <label for="login" class="col-sm-4 col-form-label">Login:</label>
            <input type="text" class="form-control col-sm-8 <?php echo (!empty($data['login_err'])) ? 'is-invalid' : ''; ?>" id="login" name="login" value="<?php echo $data['login']; ?>">
            <span class="invalid-feedback offset-sm-4"><?php echo $data['login_err']; ?></span>
          </div>

          <div class="form-group row">
            <label for="poziom" class="col-sm-4 col-form-label">Poziom:</label>
            <select class="form-control col-sm-8" id="poziom" name="poziom">
              <?php foreach (array_keys($data['poziomy']) as $key) : ?>
              <option value="<?php echo $key; ?>" <?php if ($data['poziom'] == $key) {echo "selected"; } ?>><?php echo $data['poziomy'][$key]; ?>
              <?php endforeach; ?>
            </select>
          </div>

          <button type="submit" class="col-sm-4 offset-sm-4 btn btn-primary">Dodaj</button>

    </div>
  </div><!-- /row podmiot -->

</form>

<?php require APPROOT . '/views/inc/footer.php';
