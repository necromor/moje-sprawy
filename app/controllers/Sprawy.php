<?php
  /*
   *  Kontroler Sprawy odpowiedzialny jest za obsługę modelu Sprawa z widokami.
   *  Nazwy metod jak w każdym kontrolerze odpowiadają częściom adresu url (co wynika
   *  ze specifiki TraversyMVC)
   *
   */

  class Sprawy extends Controller {

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z modelami 
       */

      $this->sprawaModel = $this->model('Sprawa');
      $this->metrykaModel = $this->model('Metryka');
      $this->pracownikModel = $this->model('Pracownik');
      $this->przychodzacaModel = $this->model('Przychodzaca');
    }

    public function szczegoly($id) {
      /*
       * Serce całego modułu spraw.
       *
       * Wyświetla szczegóły sprawy czyli:
       *  - metrykę
       *  - przypisane do niej dokumenty
       *  - guziki umożliwiające przypisywanie pism
       *  - guziki umożliwiające edycję tematu
       *
       * Parametry:
       *  - id => id wyświetlanej sprawy
       *
       * Obsługuje widok: sprawy/szczegoly/id
       *
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      $sprawa = $this->sprawaModel->pobierzSprawePoId($id);
      $metryka = $this->metrykaModel->pobierzMetrykeSprawy($id);

      // podmiana danych w metryce na bardziej czytelne
      $lp = 1;
      foreach($metryka as $m) {
        // zastąp id liczbą porządkową
        $m->id = $lp;
        $lp++;

        // zastąp id_pracownik imieniem i nazwiskiem
        $m->id_pracownik = $this->pracownikModel->pobierzImieNazwisko($m->id_pracownik);

        // zastąp id_dokumentu unikalnym oznaczeniem
        // 0 - brak dokumentu
        // 1 - przychodzące
        // 2 - wychodzące
        // 3 - inny dokument
        switch ($m->rodzaj_dokumentu) {
          case 1:
            $przych = $this->przychodzacaModel->pobierzPrzychodzacaPoId($m->id_dokument);
            $m->dokument = 'p' . time($przych->utworzone);
            break;
          default:
            $m->dokument = '=====';
        }

      }


      $data = [
        'title' => 'Szczegóły sprawy ' . $sprawa->znak,
        'id' => $id,
        'sprawa' => $sprawa,
        'metryka' => $metryka
      ];

      $this->view('sprawy/szczegoly', $data);
    }





  }
