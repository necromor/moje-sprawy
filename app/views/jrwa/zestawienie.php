<?php require APPROOT . '/views/inc/header.php'; ?>

<h1><?php echo $data['title'] ?></h1>

  <div class="row">
    <div class="col-md-8 mx-auto">
    <?php echo flash('jrwa_wiadomosc'); ?>
    </div>
  </div>

<table class="table table-hover">
  <caption>Zestawienie numerów Jednolitego Rzeczowego Wykazu Akt wraz z opisem.</caption>
  <thead class="thead-dark">
  <tr>
    <th class="align-middle">Numer</th>
    <th>Opis</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach($data['jrwa'] as $jrwa) : ?>
    <tr>
      <td> <a href="<?php echo URLROOT; ?>/jrwa/edytuj/ <?php echo $jrwa->id; ?>" title="Zmień dane numeru JRWA"> <?php echo $jrwa->numer; ?></a></td>
      <td> <?php echo $jrwa->opis; ?> </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php require APPROOT . '/views/inc/footer.php'; ?>
