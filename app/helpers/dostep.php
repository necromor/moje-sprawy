<?php

  function czyZalogowany() {
    /*
     * Sprawdza czy pracownik jest zalogowany
     *
     * Zwraca boolean - true jeżeli jest zalogowany
     */

    return (isset($_SESSION['user_id']) && isset($_SESSION['imie_nazwisko']));
  }

  function sprawdzCzyZalogowany(){
    /*
     * Sprawdza czy pracownik jest zalogowany
     *
     * Jeżeli nie przekierowuje na stronę główną.
     */

    if (!czyZalogowany()) {
      redirect('pages');
    }
  }

  function czyPosiadaDostep($min, $max) {
    /*
     * Sprawdza czy pracownik posiada dostęp, który zawiera się w granicach min max
     * Z uwagi na fakt, że wyższe poziomy dostępu mają niższe numery
     * min max są odwrócone.
     *
     * Jeżeli nie przekierowuje na stronę główną.
     *
     * Parametry:
     *  - min => maksymalny poziom dostępu
     *  - max => dolny limit - służy głównie przy oddzielaniu admina
     */

    // $_SESSION istnieje tylko jak zalogowany
    if (!czyZalogowany()){
      return false;
    }

    $poziom = $_SESSION['poziom'];

    return ($poziom <= $min) && ($poziom >= $max);
    //return true;
  }

  function sprawdzCzyPosiadaDostep($min, $max) {
    /*
     * Sprawdza czy pracownik posiada dostęp.
     *
     * Jeżeli nie przekierowuje na stronę główną.
     *
     * Parametry:
     *  - min => maksymalny poziom dostępu
     *  - max => dolny limit - służy głównie przy oddzielaniu admina
     */
    if (!czyPosiadaDostep($min, $max)) {
      redirect('pages');
    }
  }

