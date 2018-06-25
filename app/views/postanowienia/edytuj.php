<?php require APPROOT . '/views/inc/header.php'; ?>

<form action="<?php URLROOT; ?>/postanowienia/edytuj/<?php echo $data['id']; ?>" method="post">

  <div class="row">
    <h1 class="col-md-8 mx-auto"><?php echo $data['title']; ?></h1>
  </div>

  <input type="hidden" name="zrodlo" value="<?php echo $data['zrodlo']; ?>">

  <div class="row">
    <div class="col-md-8 mx-auto">

        <div class="form-group row wychodzace-dp">
          <label for="numer" class="col-sm-4 col-form-label">Oznaczenie:</label>
          <input type="text" class="form-control col-sm-7" id="numer" name="numer" value="<?php echo $data['numer']; ?>">
          <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['numer_err']; ?></span>
        </div>

        <div class="form-group row wychodzace-dp">
          <label for="dotyczy" class="col-4 col-form-label">Dotyczy</label>
          <textarea class="form-control col-sm-8" id="dotyczy" name="dotyczy" rows="3"><?php echo $data['dotyczy']; ?></textarea>
          <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['dotyczy_err']; ?></span>
        </div>

    </div>
  </div>

  <div class="form-group row">
    <div class="col-sm-8 offset-sm-4">
      <button type="submit" class="btn btn-primary">Zatwierdź zmiany</button>
      <?php if ($data['zrodlo'] == 'zestawienie') : ?>
      <a href="<?php echo URLROOT; ?>/postanowienia/zestawienie/<?php echo $data['rok']; ?>" class="btn btn-info"><i class="fa fa-angle-double-left"></i> Wróć do zestawienia</a>
      <?php else : ?>
      <a href="<?php echo URLROOT; ?>/sprawy/szczegoly/<?php echo $data['sprawaId']; ?>" class="btn btn-info"><i class="fa fa-angle-double-left"></i> Wróć do szczegółów sprawy</a>
      <?php endif; ?>
    </div>
  </div>

</form>

<?php require APPROOT . '/views/inc/footer.php'; ?>

