<?php require APPROOT . '/views/inc/header.php'; ?>

<h1><?php echo $data['title'] ?> z <?php echo $data['rok']; ?> roku</h1>

  <div class="row">
    <div class="col-md-8 mx-auto">
    <?php echo flash('wychodzace_edytuj'); ?>
    </div>
  </div>

<form class="form-inline" action="<?php echo URLROOT; ?>/wychodzace/zestawienie" method="post">
  <label for="rok" class="sr-only">Rok</label>
  <input type="number" name="rok" id="rok" class="form-control mr-sm-2 mb-2" value="<?php echo $data['rok']; ?>" min="2016" max="2100" step="1">
  <button type="submit" class="btn btn-primary mb-2">Zmień rok zestawienia</button>
</form>

<table class="table table-hover">
  <caption>Zestawienie pism wychodzących, dodanych w roku <?php echo $data['rok']; ?>.</caption>
  <thead class="thead-dark">
  <tr>
    <th class="align-middle">Znak sprawy <br> [Data pisma]</th>
    <th class="align-middle">Odbiorca</th>
    <th class="align-middle w-25">Dotyczy</th>
    <th class="align-middle">Dostarczone</th>
    <th class="align-middle">Uwagi</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach($data['pisma'] as $pismo) : ?>
  <tr>
    <td class="align-middle">
      <a href="<?php echo URLROOT; ?>/sprawy/szczegoly/<?php echo $pismo->sprawaId; ?>" title="Zobacz szczegóły sprawy"><?php echo $pismo->znak; ?></a>
      <br>[<a href="<?php echo URLROOT; ?>/wychodzace/edytuj/<?php echo $pismo->id; ?>" title="Zmień dane pisma"><?php echo substr($pismo->utworzone,0, 10); ?></a>]
    </td>
    <td class="align-middle"><?php echo $pismo->nazwa; ?></br>
        <?php echo $pismo->adres_1; ?><br>
        <?php echo $pismo->adres_2; ?></td>
    <td class="align-middle"><?php echo $pismo->dotyczy; ?></td>
    <td class="align-middle">
    <?php if($pismo->data_wyjscia == NULL) : ?>
      oznacz odbiór:
      <div class="btn-group" role="group" aria-label="Oznaczenie sposobu wysyłki">
      <a href="<?php echo URLROOT; ?>/wychodzace/odbior/<?php echo $pismo->id; ?>/1" class="btn btn-primary">osobiście</a>
        <a href="<?php echo URLROOT; ?>/wychodzace/odbior/<?php echo $pismo->id; ?>/2" class="btn btn-secondary">pocztą</a>
      </div>
    <?php else : ?>
      <?php if($pismo->sposob_wyjscia == '1') : ?>
        odebrane osobiście dnia:
      <?php else : ?>
        wysłane dnia:
      <?php endif; ?>
      <br><?php echo substr($pismo->data_wyjscia, 0, 10); ?>
    <?php endif; ?>
    </td>
    <td class="align-middle">
      <?php if($pismo->decyzjaId) : ?>
        decyzja<br> <?php echo $pismo->decyzjaNumer; ?>
      <?php elseif($pismo->postanowienieId) : ?>
        postanowienie<br> <?php echo $pismo->postanowienieNumer; ?>
      <?php else : ?>
        =====
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require APPROOT . '/views/inc/footer.php'; ?>
