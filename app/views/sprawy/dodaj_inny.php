<?php require APPROOT . '/views/inc/header.php'; ?>

<form action="<?php echo URLROOT; ?>/sprawy/dodaj_inny/<?php echo $data['id']; ?>" method="post">

  <div class="row">
    <h1 class="col-md-8 mx-auto"><?php echo $data['title'] ?></h1>
  </div>

  <div class="row">
    <div class="col-md-8 mx-auto">

      <div class="form-group row">
        <label for="rodzaj" class="col-sm-4 col-form-label">Rodzaj:</label>
        <input class="form-control col-sm-8" type="text" id="rodzaj" name="rodzaj" value="<?php echo $data['rodzaj']; ?>">
        <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['rodzaj_err']; ?></span>
      </div>

    </div>
  </div>

  <div class="row">
    <div class="col-md-8 mx-auto">

      <div class="form-group row">
        <label for="dotyczy" class="col-sm-4 col-form-label">Dotyczy:</label>
        <textarea class="form-control col-sm-8" id="dotyczy" name="dotyczy" rows="3"><?php echo $data['dotyczy']; ?></textarea>
        <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['dotyczy_err']; ?></span>
      </div>

    </div>
  </div>

  <div class="row">
    <button type="submit" class="btn btn-primary offset-md-4 col-md-4">Dodaj dokument</button>
  </div>

  <div class="row my-4">
    <a href="<?php echo URLROOT; ?>/sprawy/szczegoly/<?php echo $data['id']; ?>" class="btn btn-info offset-md-4 col-md-4"><i class="fa fa-angle-double-left"></i> Wróć do szczegółów sprawy</a>
  </div>

</form>

<?php require APPROOT . '/views/inc/footer.php'; ?>

