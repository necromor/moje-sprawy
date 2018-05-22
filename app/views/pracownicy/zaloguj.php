<?php require APPROOT . '/views/inc/header.php'; ?>

<form action="<?php echo URLROOT; ?>/pracownicy/zaloguj" method="post">
  <div class="row mb-3">
    <div class="col-md-8 mx-auto py-3">

         <h1><?php echo $data['title'] ?></h1>

          <div class="form-group row">
            <label for="login" class="col-sm-4 col-form-label">Login:</label>
            <input type="text" class="form-control col-sm-8 <?php echo (!empty($data['login_err'])) ? 'is-invalid' : ''; ?>" id="login" name="login">
            <span class="invalid-feedback offset-sm-4"><?php echo $data['login_err']; ?></span>
          </div>

          <div class="form-group row">
            <label for="haslo" class="col-sm-4 col-form-label">Hasło:</label>
            <input type="password" class="form-control col-sm-8 <?php echo (!empty($data['haslo_err'])) ? 'is-invalid' : ''; ?>" id="haslo" name="haslo">
            <span class="invalid-feedback offset-sm-4"><?php echo $data['haslo_err']; ?></span>
          </div>

          <button type="submit" class="col-sm-4 offset-sm-4 btn btn-primary">Zaloguj się</button>

    </div>
  </div>

</form>

<?php require APPROOT . '/views/inc/footer.php';
