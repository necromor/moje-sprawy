<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="row">
  <div class="col-md-8 mx-auto">

    <h1><?php echo $data['title'] ?></h1>

    <table class="table table-bordered">
      <caption>Informacje o piśmie przychodzącym.</caption>
      <thead class="thead-dark">
      <tr>
        <th class="align-middle w-25">Numer w rejestrze</th>
        <td class="align-middle"><?php echo $data['pismo']->nr_rejestru; ?>
      </tr>
      <tr>
        <th class="align-middle">Dotyczy</th>
        <td><?php echo $data['pismo']->dotyczy; ?></td>
      </tr>
    </table>
    <hr>

  </div>
</div>

<form action="<?php echo URLROOT; ?>/przychodzace/adacta/<?php echo $data['id']; ?>" method="post">

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

    </div>
  </div>

  <div class="form-group row">
    <div class="col-sm-8 offset-sm-4">
      <button type="submit" class="btn btn-primary">Oznacz jako ad acta</button>
      <a href="<?php echo URLROOT; ?>/przychodzace/moje" class="btn btn-info"><i class="fa fa-angle-double-left"></i> Wróć do Twoich pism</a>
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
