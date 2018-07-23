<?php require APPROOT . '/views/inc/header.php'; ?>

<form action="<?php echo URLROOT; ?>/ustawienia/zmien_termin" method="post">
  <div class="row mb-3">
    <div class="col-md-8 mx-auto py-3">

         <h1><?php echo $data['title'] ?></h1>

          <div class="row">
            <div class="col-md-8 mx-auto">
            <?php echo flash('zmien_termin'); ?>
            </div>
          </div>

          <div class="form-group row">
            <label for="termin" class="col-sm-6 col-form-label">Liczba dni ważności hasła:</label>
            <input type="number" class="form-control col-sm-2" id="termin" name="termin" value="<?php echo $data['termin']; ?>" min="0" max="366">
          </div>
          <p class="alert alert-info">Ustaw <strong>0</strong> jeżeli chcesz żeby hasła nie miały sprawdzanego terminu ważności.</p>

          <button type="submit" class="col-sm-4 offset-sm-4 btn btn-primary">Zmień termin ważności</button>

    </div>
  </div>

</form>

<?php require APPROOT . '/views/inc/footer.php';
