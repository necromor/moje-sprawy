<?php

  function pobierzIdNazwy($nazwa) {
    /*
     * funkcja która pobiera numer (id) z podanego ciągu w formacie:
     * id# pozostały tekst 
     * zwraca id jako string
     */

    $el = explode('#', $nazwa);
    return $el[0];
  }

  function utworzIdNazwa($id, $nazwa) {
    /*
     * funkcja która tworzy ciąg typu id# nazwa z podanych danych
     * zwraca string
     */

    return $id . '# ' . $nazwa; 
  }
