<?php

  function czyZalogowany() {
    /*
     * Sprawdza czy pracownik jest zalogowany
     *
     * Jeżeli nie przekierowuje na stronę główną.
     */

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['imie_nazwisko'])) {
      redirect('pages');
    }
  }

  function czyZalogowanyAdmin() {
    /*
     * Sprawdza czy admin jest zalogowany.
     * Wywoływana po czyZalogownay więc istnieje $_SESSION.
     *
     * Jeżeli nie przekierowuje na stronę główną.
     */

    if ($_SESSION['user_id'] != 0 || $_SESSION['imie_nazwisko'] != 'admin') {
      redirect('pages');
    }
  }

  function czyPosiadaDostep($poziom_pracownika, $wymagany_poziom) {
    /*
     * Sprawdza czy posiadany przez pracownika poziom jest właściwy.
     * Właściwy poziom to taki który jest <= od wymaganego.
     *
     * Jeżeli nie przekierowuje na stronę główną.
     */

    if ($poziom_pracownika > $wymagany_poziom) {
      redirect('pages');
    }
  }
