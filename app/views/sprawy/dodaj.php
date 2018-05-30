<?php require APPROOT . '/views/inc/header.php'; ?>

<form action="<?php URLROOT; ?>/sprawy/dodaj" method="post">

  <div class="row">
    <h1 class="col-md-8 mx-auto"><?php echo $data['title'] ?></h1>
  </div>

  <div class="row">
    <div class="col-md-8 mx-auto">

      <div class="form-group row">
        <label for="jrwa" class="col-sm-4 col-form-label">Numer JRWA:</label>
        <input type="text" class="form-control col-sm-7" id="jrwa" name="jrwa" list="listaJrwa" value="<?php echo $data['jrwa']; ?>">
        <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['jrwa_err']; ?></span>
      </div>

      <div class="form-group row">
        <p id="opisJrwa" class="col-sm-8 offset-sm-4 alert alert-info">Wybierz numer z Jednolitego Rzeczowego Wykazu Akt</p>
      </div>

      <div class="form-group row">
        <label for="temat" class="col-sm-4 col-form-label">Temat:</label>
        <textarea class="form-control col-sm-8" id="temat" name="temat" rows="3"><?php echo $data['temat']; ?></textarea>
        <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['temat_err']; ?></span>
      </div>

    </div>
  </div>

  <div class="form-group row">
    <div class="col-sm-4 offset-sm-4">
      <button type="submit" class="btn btn-primary">Zarejestruj sprawę</button>
    </div>
  </div>

  <!-- lista -->
  <datalist id="listaJrwa">
    <?php foreach($data['jrwaLista'] as $jrwa) : ?>
    <option value="<?php echo $jrwa->numer; ?>">
    <?php endforeach; ?>
  </datalist>

  <!-- /lista -->

</form>

<?php require APPROOT . '/views/inc/footer.php'; ?>

