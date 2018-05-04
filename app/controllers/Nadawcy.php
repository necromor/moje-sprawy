<?php

  class Nadawcy extends Controller {

    public function __construct() {


      $this->nadawcaModel = $this->model('Nadawca');
    }

    public function dodaj() {

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
          'title' => 'Dodaj nadawcę',
          'nazwa_podmiotu' => trim($_POST['nazwaPodmiotu']),
          'adres_podmiotu' => trim($_POST['adresPodmiotu']),
          'poczta_podmiotu' => trim($_POST['pocztaPodmiotu']),
          'nazwa_podmiotu_err' => '',
          'adres_podmiotu_err' => '',
          'poczta_podmiotu_err' => ''
        ];

        // tymczasowa walidacja
        if (empty($data['nazwa_podmiotu'])) {
          $data['nazwa_podmiotu_err'] = 'Nazwa nie może pozostać pusta';
        }

        if (empty($data['adres_podmiotu'])) {
          $data['adres_podmiotu_err'] = 'Ulica (miejscowość) nie może pozostać pusta';
        }

        if (empty($data['poczta_podmiotu'])) {
          $data['poczta_podmiotu_err'] = 'Kod pocztwoy i poczta nie może pozostać puste';
        }


        // Dodaj do bazy danych tylko gdy nie ma błędów przypisanych do 
        // któregokolwiek pola
        if (empty($data['nazwa_podmiotu_err']) && empty($data['adres_podmiotu_err']) && empty($data['poczta_podmiotu_err'])) {

          // Pomyślna walidacja - dodaj do bazy danych
          $this->nadawcaModel->dodajNadawce($data);

          // tymczasowo
          redirect('nadawcy/zestawienie');

        } else {

          // Wyświetl powtórnie formularz z podanymi danymi i błędami
          $this->view('nadawcy/dodaj', $data);
        }


      } else {

        $data = [
          'title' => 'Dodaj nadawcę',
          'nazwa_podmiotu' => '',
          'adres_podmiotu' => '',
          'poczta_podmiotu' => '',
          'nazwa_podmiotu_err' => '',
          'adres_podmiotu_err' => '',
          'poczta_podmiotu_err' => ''
        ];

        $this->view('nadawcy/dodaj', $data);
      }

    }


   public function zestawienie() {

     $podmioty = $this->nadawcaModel->pobierzNadawcow();

     $data = [
       'title' => 'Zestawienie podmiotów',
       'podmioty' => $podmioty
     ];

     $this->view('nadawcy/zestawienie', $data); 
   }


  }
