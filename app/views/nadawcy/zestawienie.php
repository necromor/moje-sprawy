<?php require APPROOT . '/views/inc/header.php'; ?>

<h1><?php echo $data['title'] ?></h1>
<table class="table">
  <tr>
    <th>Id</th><th>Nazwa</th><th>Adres</th>
  </tr>
  <?php foreach($data['podmioty'] as $podmiot) : ?>
    <tr>
      <td> <a href="<?php echo URLROOT; ?>/nadawcy/edytuj/ <?php echo $podmiot->id; ?>" title="Edytuj podmiot"> <?php echo $podmiot->id; ?></a></td>
      <td> <?php echo $podmiot->nazwa; ?> </td>
      <td> <?php echo $podmiot->adres_1; ?> <br> 
           <?php echo $podmiot->adres_2; ?> </td> 
    </tr>
  <?php endforeach; ?>
</table>

<?php require APPROOT . '/views/inc/footer.php'; ?>
