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
            <div class="dropdown-menu" aria-labelledby="zestawienia">
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/przychodzace/dodaj">Przychodzące</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/podmioty/dodaj">Podmioty</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="zestawienia" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Zestawienia</a>
            <div class="dropdown-menu" aria-labelledby="zestawienia">
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/przychodzace/zestawienie/<?php echo date("Y"); ?>">Przychodzące</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/faktury/zestawienie/<?php echo date("Y"); ?>">Faktury</a>
              <a class="dropdown-item" href="<?php echo URLROOT; ?>/podmioty/zestawienie">Podmioty</a>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/pages/about">About</a>
          </li>
        </ul>

        <ul class="navbar-nav ml-auto">
          <?php if(isset($_SESSION['user_id'])) : ?>
          <li class="nav-item">
            <a class="nav-link" href="#">Welcome <?php echo $_SESSION['user_name']; ?></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/users/logout">Logout</a>
          </li>

          <?php else : ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/users/register">Register</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/users/login">Login</a>
          </li>
          <?php endif; ?>
        </ul>

      </div>
      </div>
    </nav>
