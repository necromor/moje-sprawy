<?php require APPROOT . '/views/inc/header.php'; ?>


<h1 class="row"><?php echo $data['title'] ?></h1>

<div class="row">

  <div class="col-md-8 col-12">

  <?php if ($data['czy_wyniki'] == -1) : ?>
    <p class="mt-3">Wyszukaj pisma wychodzące uzupełniając jedno lub kilka pól.</p>
    <p>Formularz pamięta wpisane wartości w polach więc można zacząć od szerszego zakresu i stopniowo go zmniejszać.</p>
    <p>Przykłady dotyczące wyszukiwania po datach. Jeżeli w polu <em>data pisma</em> wpiszemy następujące wartości:</p>
    <ul>
      <li><strong>2018</strong> to system wyszuka pisma, które dodano do systemu w roku 2018</li>
      <li><strong>2018-03</strong> to system wyszuka pisma, które dodano do systemu w marcu w roku 2018</li>
      <li><strong>2018-03-14</strong> to system wyszuka pisma, które dodano do systemu dokładnie dnia 14 marca 2018 roku</li>
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
        <th class="align-middle">Znak sprawy</th>
        <th class="align-middle">Data pisma</th>
        <th class="align-middle">Odbiorca pisma</th>
        <th class="align-middle">Szczegóły</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach($data['pisma'] as $pismo) : ?>
      <tr>
        <td><a href="<?php echo URLROOT; ?>/sprawy/szczegoly/<?php echo $pismo->sprawaId; ?>" title="Zobacz szczegóły sprawy"><?php echo $pismo->znak; ?></a></td>
        <td><?php echo substr($pismo->utworzone, 0, 10); ?></td>
        <td><?php echo $pismo->nazwa; ?></td>
        <td>
          <button class="btn btn-block btn-info" title="Zobacz szczegóły korespondencji"
                  data-toggle="collapse" data-target="#szczegoly_<?php echo $pismo->id; ?>">
            pokaż
          </button>
        </td>
      </tr>
      <tr>
        <td colspan="4" class="collapse" id="szczegoly_<?php echo $pismo->id; ?>">
          <?php echo $pismo->szczegoly; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  </div><!-- /wyniki szukania -->

  <div class="col-md-4 col-12">
    <form action="<?php echo URLROOT; ?>/wychodzace/szukaj" method="post">

      <div class="form-group row">
        <label for="znak" class="col-12 col-form-label">Znak sprawy:</label>
        <input type="text" class="form-control col-10 mx-auto" id="znak" name="znak" value="<?php echo $data['znak']; ?>">
      </div>

      <div class="form-group row">
        <label for="data_pisma" class="col-12 col-form-label">Data pisma:</label>
        <input type="text" class="form-control col-10 mx-auto" id="data_pisma" name="data_pisma" value="<?php echo $data['data_pisma']; ?>" placeholder="rrrr-mm-dd lub fragment">
      </div>

      <div class="form-group row">
        <label for="nazwa" class="col-12 col-form-label">Nazwa odbiorcy:</label>
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
