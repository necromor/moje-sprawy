<?php require APPROOT . '/views/inc/header.php'; ?>

<form action="<?php URLROOT; ?>/sprawy/edytuj/<?php echo $data['id']; ?>" method="post">

  <div class="row">
    <h1 class="col-md-8 mx-auto"><?php echo $data['title'] ?></h1>
  </div>

  <div class="row">
    <div class="col-md-8 mx-auto">

      <div class="form-group row">
        <label for="temat" class="col-sm-4 col-form-label">Temat:</label>
        <textarea class="form-control col-sm-8" id="temat" name="temat" rows="3"><?php echo $data['temat']; ?></textarea>
        <span class="invalid-feedback offset-sm-4 d-block"><?php echo $data['temat_err']; ?></span>
      </div>

    </div>
  </div>

  <div class="form-group row">
    <div class="col-sm-8 offset-sm-4">
      <button type="submit" class="btn btn-primary">Zatwierdź zmianę tematu</button>
      <a href="<?php echo URLROOT; ?>/sprawy/szczegoly/<?php echo $data['id']; ?>" class="btn btn-info"><i class="fa fa-angle-double-left"></i> Wróć do szczegółów sprawy</a>
    </div>
  </div>

</form>

<?php require APPROOT . '/views/inc/footer.php'; ?>

