<?php

  function formatujKwote($kwota) {
    /*
     * funkcja która z podanej liczby w formacie dddd.dd
     * tworzy liczbę w formacie d.ddd,dd
     * (tysiące i miliony oddzielone kropką, grosze przecinkiem)
     */

    $st = '.'; // separator tysięcy
    $sg = ','; // separator groszy
    

    // podziel kwotę na złote i grosze
    $podzial = explode('.', $kwota);  
    $zlote = $podzial[0];
    if (isset($podzial[1])) {
      $grosze = $podzial[1];
    } else {
      $grosze = '00';
    }

    // sprawdź znak
    // jeżeli jest wstaw do zmiennej i usuń
    $znak = '';
    if (substr($zlote, 0, 1) == '-') {
      $znak = '-';
      $zlote = substr($zlote, 1);
    }

    // dodaj separator tysięcy po milionach i tysiącach
    $dlugosc = strlen($zlote);
    if ($dlugosc > 6) {
      $zlote = substr($zlote, 0, -6) . $st . substr($zlote, -6, -3) . $st . substr($zlote, -3);
    } elseif (strlen($zlote) > 3) {
      $zlote = substr($zlote, 0, -3) . $st . substr($zlote, -3);
    }

    // sprawdź czy grosze mają dwie cyfry
    if (strlen($grosze) < 2) {
      $grosze.='0';
    }
   
    $wynikowa = $znak . $zlote . $sg . $grosze;
    
    return $wynikowa;
  }
