    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
      <div class="container">
      <a class="navbar-brand" href="<?php echo URLROOT; ?>"><?php echo SITENAME; ?></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">

        <?php if(isset($_SESSION['poziom']) && $_SESSION['poziom'] == '-1'): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dodaj" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dodaj</a>
            <div class="dropdown-menu" aria-labelledby="dodaj">
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/pracownicy/dodaj">Pracownika</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/jrwa/dodaj">JRWA</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="zestawienia" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Zestawienia</a>
            <div class="dropdown-menu" aria-labelledby="zestawienia">
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/pracownicy/zestawienie">Pracownicy</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/jrwa/zestawienie">JRWA</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="ustawienia" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ustawienia</a>
            <div class="dropdown-menu" aria-labelledby="ustawienia">
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/ustawienia/zmien_termin">Ważność hasła</a>
            </div>
          </li>

        <?php elseif(isset($_SESSION['poziom']) && $_SESSION['poziom'] == '0'): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="sprawy" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Sprawy</a>
            <div class="dropdown-menu" aria-labelledby="sprawy">
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/przychodzace/moje">Moje pisma</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/sprawy/dodaj">Dodaj sprawę</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/sprawy/wybierz">Szczegóły sprawy</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="sekretariat" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Sekretariat</a>
            <div class="dropdown-menu" aria-labelledby="sekretariat">
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/przychodzace/dodaj">Dodaj przychodzące</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/podmioty/dodaj">Dodaj podmiot</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="zestawienia" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Zestawienia</a>
            <div class="dropdown-menu" aria-labelledby="zestawienia">
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/przychodzace/zestawienie/<?php echo date("Y"); ?>">Przychodzące</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/przychodzace/faktury/<?php echo date("Y"); ?>">Faktury</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/podmioty/zestawienie">Podmioty</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/wychodzace/zestawienie/<?php echo date("Y"); ?>">Wychodzące</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/decyzje/zestawienie/<?php echo date("Y"); ?>">Decyzje</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/postanowienia/zestawienie/<?php echo date("Y"); ?>">Postanowienia</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="szukaj" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Szukaj</a>
            <div class="dropdown-menu" aria-labelledby="zestawienia">
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/przychodzace/szukaj">Przychodzących</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/wychodzace/szukaj">Wychodzących</a>
            </div>
          </li>

        <?php elseif(isset($_SESSION['poziom']) && $_SESSION['poziom'] >= '1'): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/przychodzace/moje">Moje pisma</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/sprawy/dodaj">Dodaj sprawę</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/sprawy/wybierz">Szczegóły sprawy</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="zestawienia" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Zestawienia</a>
            <div class="dropdown-menu" aria-labelledby="zestawienia">
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/przychodzace/zestawienie/<?php echo date("Y"); ?>">Przychodzące</a>
              <?php if(isset($_SESSION['poziom']) && $_SESSION['poziom'] == '1'): ?>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/przychodzace/faktury/<?php echo date("Y"); ?>">Faktury</a>
              <?php endif; ?>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/wychodzace/zestawienie/<?php echo date("Y"); ?>">Wychodzące</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/decyzje/zestawienie/<?php echo date("Y"); ?>">Decyzje</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/postanowienia/zestawienie/<?php echo date("Y"); ?>">Postanowienia</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="szukaj" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Szukaj</a>
            <div class="dropdown-menu" aria-labelledby="zestawienia">
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/przychodzace/szukaj">Przychodzących</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/wychodzace/szukaj">Wychodzących</a>
            </div>
          </li>

        <?php endif; ?>
        </ul>

        <ul class="navbar-nav ml-auto">
          <?php if(isset($_SESSION['user_id']) && isset($_SESSION['imie_nazwisko'])) : ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="zalogowany" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Zalogowano jako: <?php echo $_SESSION['imie_nazwisko']; ?></a>
            <div class="dropdown-menu" aria-labelledby="zalogowany">
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/pracownicy/zmien_haslo">Zmień hasło</a>
              <?php if(isset($_SESSION['poziom']) && $_SESSION['poziom'] != '-1'): ?>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/pracownicy/ustaw_znak">Ustaw wzór znaku sprawy</a>
              <?php endif; ?>
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
