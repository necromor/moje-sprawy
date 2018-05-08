<?php

  class Przychodzace extends Controller {

    public function __construct() {

      $this->przychodzacaModel = $this->model('Przychodzaca');
      $this->pracownikModel = $this->model('Pracownik');
    }

    public function zestawienie($rok) {

      // sprawdź czy nastąpiła zmiana roku
      // jeżeli tak to przekieruj 
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        redirect('przychodzace/zestawienie/' . $_POST['rok']);
      }

      $pisma = $this->przychodzacaModel->pobierzPrzychodzace($rok);
      // zamień id pracownika na imie i nazwisko
      // jeżeli korespondencja nie jest fakturą
      foreach($pisma as $pismo) {
        if($pismo->id_pracownik != 0) {
          $pismo->id_pracownik = $this->pracownikModel->pobierzImieNazwisko($pismo->id_pracownik);
        }
      } 

      $data = [
        'title' => 'Zestawienie korespondencji przychodzącej',
        'pisma' => $pisma,
        'rok' => $rok
      ];

      $this->view('przychodzace/zestawienie', $data);
    }

  }
