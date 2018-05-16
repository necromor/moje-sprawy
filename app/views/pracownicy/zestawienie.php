<?php require APPROOT . '/views/inc/header.php'; ?>

<h1><?php echo $data['title'] ?></h1>

  <div class="row">
    <div class="col-md-8 mx-auto">
    <?php echo flash('pracownicy_wiadomosc'); ?>
    </div>
  </div>

<table class="table table-hover">
  <caption>Zestawienie pracowników wraz z ich określonym poziomem dostępu i statusem aktywności.</caption>
  <thead class="thead-dark">
  <tr>
    <th class="align-middle">Id</th>
    <th class="align-middle">Imię i Nazwisko</th>
    <th class="align-middle">Login</th>
    <th class="align-middle">Poziom</th>
    <th class="align-middle">Aktywny</th>
    <th class="align-middle">Resetuj hasło</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach($data['pracownicy'] as $pracownik) : ?>
  <tr <?php if ($pracownik->aktywny == 'Nie') { echo 'class="table-danger"';} ?>>
    <td class="align-middle text-center"><a href="<?php echo URLROOT; ?>/pracownicy/edytuj/<?php echo $pracownik->id; ?>" title="Zmień dane pracownika">
        <?php echo $pracownik->id; ?></a></td>
    <td><?php echo $pracownik->imie; ?> <?php echo $pracownik->nazwisko; ?></td>
    <td><?php echo $pracownik->login; ?></td>
    <td class="align-middle"><?php echo $pracownik->poziom; ?></td>
    <td class="align-middle"><?php echo $pracownik->aktywny; ?></td>
    <td class="align-middle">
      <?php if ($pracownik->login == $pracownik->haslo) : ?>
      hasło domyślne
      <?php else : ?>
      <a href="<?php echo URLROOT; ?>/pracownicy/resetuj/<?php echo $pracownik->id; ?>" class="btn btn-danger" title="Resetuj hasło pracownikowi. UWAGA - operacja jest nieodwracalna!">Resetuj hasło
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require APPROOT . '/views/inc/footer.php'; ?>
