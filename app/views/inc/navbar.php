    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
      <div class="container">
      <a class="navbar-brand" href="<?php echo URLROOT; ?>"><?php echo SITENAME; ?></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dodaj" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dodaj</a>
            <div class="dropdown-menu" aria-labelledby="dodaj">
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/przychodzace/dodaj">Przychodzące</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/podmioty/dodaj">Podmiot</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/pracownicy/dodaj">Pracownika</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/jrwa/dodaj">JRWA</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/sprawy/dodaj">Sprawę</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="zestawienia" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Zestawienia</a>
            <div class="dropdown-menu" aria-labelledby="zestawienia">
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/przychodzace/zestawienie/<?php echo date("Y"); ?>">Przychodzące</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/przychodzace/faktury/<?php echo date("Y"); ?>">Faktury</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/podmioty/zestawienie">Podmioty</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/pracownicy/zestawienie">Pracownicy</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/jrwa/zestawienie">JRWA</a>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/sprawy/wybierz">Szczegóły sprawy</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/przychodzace/moje">Moje pisma</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/pages/about">About</a>
          </li>
        </ul>

        <ul class="navbar-nav ml-auto">
          <?php if(isset($_SESSION['user_id']) && isset($_SESSION['imie_nazwisko'])) : ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="zalogowany" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Zalogowano jako: <?php echo $_SESSION['imie_nazwisko']; ?></a>
            <div class="dropdown-menu" aria-labelledby="zalogowany">
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/pracownicy/zmien_haslo">Zmień hasło</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/pracownicy/wyloguj">Wyloguj</a>
            </div>

          <?php else : ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/pracownicy/zaloguj">Dostęp ograniczony. Zaloguj się</a>
          </li>
          <?php endif; ?>
        </ul>

      </div>
      </div>
    </nav>
