<?php require APPROOT . '/views/inc/header.php'; ?>


<h1 class="row"><?php echo $data['title'] ?></h1>

<div class="row">

  <div class="col-md-8 col-12">

  <?php if ($data['czy_wyniki'] == -1) : ?>
    <p class="mt-3">Wyszukaj korespondencję przychodzącą uzupełniając jedno lub kilka pól.</p>
    <p>Formularz pamięta wpisane wartości w polach więc można zacząć od szerszego zakresu i stopniowo go zmniejszać.</p>
    <p>Przykłady dotyczące wyszukiwania po datach. Jeżeli w polu <em>data wpływu</em> wpiszemy następujące wartości:</p>
    <ul>
      <li><strong>2018</strong> to system wyszuka pisma, które wpłynęły w roku 2018</li>
      <li><strong>2018-03</strong> to system wyszuka pisma, które wpłynęły w marcu w roku 2018</li>
      <li><strong>2018-03-14</strong> to system wyszuka pisma, które wpłynęły dokładnie dnia 14 marca 2018 roku</li>
    </ul>
  <?php else : ?>
    <p class="col-10 mx-auto alert
    <?php if ($data['czy_wyniki'] == 0) : ?>
      alert-danger
    <?php else : ?>
       alert-success
    <?php endif; ?>
      ">Liczba znalezionych pozycji: <?php echo $data['czy_wyniki']; ?></p>

    <table class="table table-hover">
      <caption>Wyniki wyszukiwania korespondencji. Znalezionych pozycji: <?php echo $data['czy_wyniki']; ?>.</caption>
      <thead class="thead-dark">
      <tr>
        <th class="align-middle">Nr rejestru</th>
        <th class="align-middle">Znak pisma</th>
        <th class="align-middle">Nadawca pisma</th>
        <th class="align-middle">Szczegóły</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach($data['pisma'] as $pismo) : ?>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  </div><!-- /wyniki szukania -->

  <div class="col-md-4 col-12">
    <form action="<?php URLROOT; ?>/przychodzace/szukaj" method="post">

      <div class="form-group row">
        <label for="nr_rej" class="col-12 col-form-label">Numer rejestru:</label>
        <input type="text" class="form-control col-10 mx-auto" id="nr_rej" name="nr_rej" value="<?php echo $data['nr_rej']; ?>">
      </div>

      <div class="form-group row">
        <label for="znak" class="col-12 col-form-label">Znak pisma:</label>
        <input type="text" class="form-control col-10 mx-auto" id="znak" name="znak" value="<?php echo $data['znak']; ?>">
      </div>

      <div class="form-group row">
        <label for="data_pisma" class="col-12 col-form-label">Data pisma:</label>
        <input type="text" class="form-control col-10 mx-auto" id="data_pisma" name="data_pisma" value="<?php echo $data['data_pisma']; ?>" placeholder="rrrr-mm-dd lub fragment">
      </div>

      <div class="form-group row">
        <label for="data_wplywu" class="col-12 col-form-label">Data wpływu:</label>
        <input type="text" class="form-control col-10 mx-auto" id="data_wplywu" name="data_wplywu" value="<?php echo $data['data_wplywu']; ?>" placeholder="rrrr-mm-dd lub fragment">
      </div>

      <div class="form-group row">
        <label for="nazwa" class="col-12 col-form-label">Nazwa nadawcy:</label>
        <input type="text" class="form-control col-10 mx-auto" id="nazwa" name="nazwa" value="<?php echo $data['nazwa']; ?>">
      </div>

      <div class="form-group row">
        <label for="dotyczy" class="col-12 col-form-label">Treść dotyczy:</label>
        <input type="text" class="form-control col-10 mx-auto" id="dotyczy" name="dotyczy" value="<?php echo $data['dotyczy']; ?>">
      </div>

      <div class="form-group row">
        <button type="submit" class="btn btn-primary col-10 mx-auto">Szukaj</button>
      </div>

    </form>
  </div><!-- /formularz szukania -->

</div>


<?php require APPROOT . '/views/inc/footer.php'; ?>
