<?php require APPROOT . '/views/inc/header.php'; ?>


  <div class="row">
    <h1 class="col-md-8 mx-auto"><?php echo $data['title'] ?></h1>
  </div>

  <div class="row">

  <form class="form-inline mx-auto my-3"  action="<?php URLROOT; ?>/sprawy/wybierz" method="post">
    <label for="znak" class="sr-only">Znak sprawy:</label>
    <input type="text" class="form-control form-control-lg mr-sm-2 mb-2 w-50" id="znak" name="znak" list="listaSpraw" value="<?php echo $data['znak']; ?>" placeholder="Wpisz znak sprawy">
    <button type="submit" class="btn btn-primary btn-lg mb-2">Zobacz szczegóły</button>
  </form>
  </div>

  <?php if ($data['znak_err'] != '') : ?>
  <div class="row">
    <p class="alert alert-danger mx-auto"><?php echo $data['znak_err']; ?></p>
  </div>
  <?php endif; ?>

  <hr>
  <div class="row">
    <div class="form-inline mx-auto">
      <label for="rok" class="sr-only">Rok</label>
      <input type="number" name="rok" id="rok" class="form-control mr-sm-2 mb-2" value="<?php echo Date('Y'); ?>" min="2016" max="2100" step="1">
      <label for="rok" class="sr-only">Numer jrwa</label>
      <input type="text" name="jrwa" id="jrwa" class="form-control mr-sm-2 mb-2" list="listaJrwa" placeholder="numer JRWA">
      <button type="button" class="btn btn-info mb-2" id="filtrujListe">Filtruj listę</button>
    </div>

  </div>

  <div class="row">
    <p class="alert mx-auto" id="filtrujInfo"></p>
  </div>

  <hr>
  <div class="row">
    <div class="col-md-8 col-12 mx-auto">
      <h2>Informacje podręczne:</h2>
      <ol>
        <li>Domyślnie na liście znajdują się sprawy z bieżącego roku i wszyskich numerów jrwa.</li>
        <li>Możesz zmienić dane na liście poprzez wybór roku i/lub numeru jrwa.</li>
        <li>Możesz również bezpośrednio wpisać w polu <strong>pełny</strong> numer dowolnej sprawy (nawet takiej, której nie ma na liście) i wcisnąć przycisk <em>Zobacz szczegóły</em>.</li>
      </ol>
    </div>
  </div>

  <!-- listy -->
  <datalist id="listaSpraw">
    <?php foreach($data['sprawy'] as $sprawa) : ?>
    <option value="<?php echo $sprawa->znak; ?>">
    <?php endforeach; ?>
  </datalist>

  <datalist id="listaJrwa">
    <?php foreach($data['jrwa'] as $jrwa) : ?>
    <option value="<?php echo $jrwa->numer; ?>">
    <?php endforeach; ?>
  </datalist>
  <!-- /listy -->

<?php require APPROOT . '/views/inc/footer.php'; ?>

