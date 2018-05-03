<?php require APPROOT . '/views/inc/header.php'; ?>



<form action="<?php echo URLROOT; ?>/nadawcy/dodaj" method="post">
  <div class="row mb-3">
    <div class="col-md-8 mx-auto py-3">

         <h1><?php echo $data['title'] ?></h1>

          <div class="form-group row">
            <label for="nazwaPodmiotu" class="col-sm-4 col-form-label">Nazwa:</label>
            <input type="text" class="form-control col-sm-8 <?php echo (!empty($data['nazwa_podmiotu_err'])) ? 'is-invalid' : ''; ?>" id="nazwaPodmiotu" name="nazwaPodmiotu" value="<?php echo $data['nazwa_podmiotu']; ?>">
            <span class="invalid-feedback offset-sm-4"><?php echo $data['nazwa_podmiotu_err']; ?></span>
          </div>

          <div class="form-group row">
            <label for="adresPodmiotu" class="col-sm-4 col-form-label">Ulica (miejscowość):</label>
            <input type="text" class="form-control col-sm-8 <?php echo (!empty($data['adres_podmiotu_err'])) ? 'is-invalid' : ''; ?>" id="adresPodmiotu" name="adresPodmiotu" value="<?php echo $data['adres_podmiotu']; ?>">
            <span class="invalid-feedback offset-sm-4"><?php echo $data['adres_podmiotu_err']; ?></span>
          </div>

          <div class="form-group row">
            <label for="pocztaPodmiotu" class="col-sm-4 col-form-label">Kod pocztowy i poczta:</label>
            <input type="text" class="form-control col-sm-8 <?php echo (!empty($data['poczta_podmiotu_err'])) ? 'is-invalid' : ''; ?>" id="pocztaPodmiotu" name="pocztaPodmiotu" value="<?php echo $data['poczta_podmiotu']; ?>">
            <span class="invalid-feedback offset-sm-4"><?php echo $data['poczta_podmiotu_err']; ?></span>
          </div>

          <button type="submit" class="col-sm-4 offset-sm-4 btn btn-primary">Dodaj</button>

    </div>
  </div><!-- /row podmiot -->


</form>


<?php require APPROOT . '/views/inc/footer.php';
