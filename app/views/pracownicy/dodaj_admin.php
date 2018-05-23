<?php require APPROOT . '/views/inc/header.php'; ?>

<?php if ($data['istnieje']) : ?>
  <div class="card text-center col-md-8 mx-auto p-0">
    <div class="card-header bg-success text-white">
      Konto admina zostało utworzone.
    </div>
    <div class="card-body">
      <p class="card-text">W systemie może istnieć tylko jedno konto admina.</p>
      <a href="<?php echo URLROOT; ?>/pracownicy/zaloguj" class="btn btn-primary">Zaloguj się</a>
    </div>
  </div>

<?php else : ?>
<form action="<?php echo URLROOT; ?>/pracownicy/dodaj_admin" method="post">
  <div class="row mb-3">
    <div class="col-md-8 mx-auto py-3">

         <h1><?php echo $data['title'] ?></h1>

          <div class="form-group row">
            <label for="login" class="col-sm-4 col-form-label">Login:</label>
            <input type="text" class="form-control col-sm-8" id="login" name="login" value="admin" disabled>
          </div>

          <div class="form-group row">
            <label for="haslo1" class="col-sm-4 col-form-label">Hasło:</label>
            <input type="password" class="form-control col-sm-8 <?php echo (!empty($data['haslo1_err'])) ? 'is-invalid' : ''; ?>" id="haslo1" name="haslo1">
            <span class="invalid-feedback offset-sm-4"><?php echo $data['haslo1_err']; ?></span>
          </div>

          <div class="form-group row">
            <label for="haslo2" class="col-sm-4 col-form-label">Powtórz hasło:</label>
            <input type="password" class="form-control col-sm-8 <?php echo (!empty($data['haslo2_err'])) ? 'is-invalid' : ''; ?>" id="haslo2" name="haslo2">
            <span class="invalid-feedback offset-sm-4"><?php echo $data['haslo2_err']; ?></span>
          </div>

          <button type="submit" class="col-sm-4 offset-sm-4 btn btn-primary">Utwórz konto admina</button>

    </div>
  </div>

</form>
<?php endif; ?>

<?php require APPROOT . '/views/inc/footer.php';
