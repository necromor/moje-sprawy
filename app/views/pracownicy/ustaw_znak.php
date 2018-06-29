<?php require APPROOT . '/views/inc/header.php'; ?>

<form action="<?php echo URLROOT; ?>/pracownicy/ustaw_znak" method="post">
  <div class="row mb-3">
    <div class="col-md-8 mx-auto py-3">

         <h1><?php echo $data['title'] ?></h1>

          <div class="row">
            <div class="col-md-8 mx-auto">
            <?php echo flash('pracownicy_ustaw_znak'); ?>
            </div>
          </div>

          <div class="form-group row">
            <label for="przedrostek" class="col-sm-4 col-form-label">Przedrostek:</label>
            <input type="text" class="form-control col-sm-8 <?php echo (!empty($data['przedrostek_err'])) ? 'is-invalid' : ''; ?>" id="przedrostek" name="przedrostek" value="<?php echo $data['przedrostek']; ?>">
            <span class="invalid-feedback offset-sm-4"><?php echo $data['przedrostek_err']; ?></span>
          </div>

          <div class="form-group row">
            <label for="przyrostek" class="col-sm-4 col-form-label">Przyrostek<sup>*</sup>:</label>
            <input type="text" class="form-control col-sm-8" id="przyrostek" name="przyrostek" value="<?php echo $data['przyrostek']; ?>">
            <p class="col offset-sm-4 mt-2"><sup>*</sup> pole nieobowiązkowe - może pozostać puste</p>
          </div>

          <button type="submit" class="col-sm-4 offset-sm-4 btn btn-primary">Ustaw wzór znaku sprawy</button>

          <div class="card mt-3">
            <div class="card-header">
              Podgląd znaku (jrwa=1234, numer sprawy=567)
            </div>
            <div class="card-body">
              <p id="wzor_znaku" class="text-center"><?php echo $data['wzor']; ?></p>
            </div>
          </div>

    </div>
  </div>

</form>

<?php require APPROOT . '/views/inc/footer.php';
