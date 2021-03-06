<?php
  /*
   *  Kontroler Podmioty odpowiedzialny jest za obsługę modelu Podmiot z widokami.
   *  Nazwy metod jak w każdym kontrolerze odpowiadają częściom adresu url (co wynika
   *  ze specifiki TraversyMVC)
   *
   */

  class Podmioty extends Controller {

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z modelami
       */

      $this->podmiotModel = $this->model('Podmiot');
      $this->pracownikModel = $this->model('Pracownik');

      $this->validator = new Validator();
    }

    public function index() {
      /*
       * Służy ona do obsługi wszystkich adresów, które nie mają odzwierciedleń w funkcjach.
       *
       * Z uwagi na konstrukcję TraversyMVC żądania nie mające funkcji
       * będa wyświetlać błąd jeżeli nie będzie index.
       *
       * Przekierowuje na 'pages', które dzieli w zależności od poziomu dostępu.
       */

      redirect('pages');
    }

    public function zestawienie() {
      /*
       * Tworzy zestaw obiektów Podmiot
       *
       * Obsługuje widok: podmioty/zestawienie
       */

      // tylko zalogowany sekretariat
      sprawdzCzyPosiadaDostep(0,0);

      $podmioty = $this->podmiotModel->pobierzPodmioty();

      $data = [
        'title' => 'Zestawienie podmiotów',
        'podmioty' => $podmioty
      ];

      $this->view('podmioty/zestawienie', $data); 
    }

    public function dodaj() {
      /*
       * Obsługuje proces dodawania nowego podmiotu.
       * Działa w dwóch trybach: wyświetlanie formularza, obsługa formularza.
       * Tryb wybierany jest w zależności od metody dostępu do strony: 
       * POST czy GET.
       * POST oznacza, że formularz został wysłany, 
       * każda inna forma dostępu powoduje wyświetlenie formularza.
       *
       * Tryb wyświetlania formularza może mieć dwa stany:
       * czysty - gdy wyświetlany jest formularz po raz pierwszy
       * brudny - gdy wyświetlany jest formularz z błędami
       * Tryb czysty zawiera puste dane, tryb brudny przechowuje dane przesłane przez
       * użytkownika i umieszcza je w stosownych polach formularza
       *
       * Tryb obsługi odpowiada za sprawdzenie wprowadzonych danych 
       * i w zależności od tego czy są błędy wywołuje metodę modelu dodawania 
       * podmiotu lub wyświetla brudny formularz.
       * Sprawdzanie poprawności wprowadzonych danych polega jedynie
       * na sprawdzeniu czy pola nie są puste.
       * Teoretycznie należało by jeszcze sprawdzić długość wprowadzonych
       * ciągów, ale z uwagi na fakt, że minimalna wartość była by niewielka
       * 2 lub 3 znaki to różnicy nie ma dużej między 1 a 3.
       * Podmioty zagraniczne mają inne kody pocztowe więc i tu zbyt
       * restrykcyjne reguły mogłyby prowadzić do frustracji.
       *
       * Obsługuje widok: podmioty/dodaj
       * Widok ten nie powinien być wykorzystywany w zwykłej pracy.
       * Dodawanie odbywa się w momencie rejestracji korespondencji.
       */

      // tylko zalogowany sekretariat
      sprawdzCzyPosiadaDostep(0,0);

      $data = [
        'title' => 'Dodaj podmiot',
        'nazwa_podmiotu' => '',
        'adres_podmiotu' => '',
        'poczta_podmiotu' => '',
        'nazwa_podmiotu_err' => '',
        'adres_podmiotu_err' => '',
        'poczta_podmiotu_err' => ''
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data['nazwa_podmiotu'] = trim($_POST['nazwaPodmiotu']);
        $data['adres_podmiotu'] = trim($_POST['adresPodmiotu']);
        $data['poczta_podmiotu'] = trim($_POST['pocztaPodmiotu']);
        $data['nazwa_podmiotu_err'] = $this->validator->sprawdzDlugosc($data['nazwa_podmiotu'], 4);
        $data['adres_podmiotu_err'] = $this->validator->sprawdzDlugosc($data['adres_podmiotu'], 4);
        $data['poczta_podmiotu_err'] = $this->validator->sprawdzDlugosc($data['poczta_podmiotu'], 8);

        // Dodaj do bazy danych tylko gdy nie ma błędów przypisanych do
        // któregokolwiek pola
        if (empty($data['nazwa_podmiotu_err']) &&
            empty($data['adres_podmiotu_err']) &&
            empty($data['poczta_podmiotu_err'])) {

          // Pomyślna walidacja - dodaj do bazy danych
          $this->podmiotModel->dodajPodmiot($data);

          redirect('podmioty/zestawienie');
        }

      }

      $this->view('podmioty/dodaj', $data);
    }

    public function edytuj($id) {
      /*
       * Obsługuje proces edycji istniejącego podmiotu.
       * Sposób działania jest identyczy jak funkcji dodaj() z niewielką różnicą
       * w trybie czystym - do pól formularza wprowadzane są dane edytowanego podmiotu.
       *
       * Obsługuje widok: podmioty/edytuj/id
       *
       * Parametry:
       *  - id => id edytowanego podmiotu
       */

      // tylko zalogowany sekretariat
      sprawdzCzyPosiadaDostep(0,0);

      $podmiot = $this->podmiotModel->pobierzDanePodmiotu($id);

      $data = [
        'title' => 'Zmień dane podmiotu',
        'nazwa_podmiotu' => $podmiot->nazwa,
        'adres_podmiotu' => $podmiot->adres_1,
        'poczta_podmiotu' => $podmiot->adres_2,
        'nazwa_podmiotu_err' => '',
        'adres_podmiotu_err' => '',
        'poczta_podmiotu_err' => '',
        'id' => $id
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data['nazwa_podmiotu'] = trim($_POST['nazwaPodmiotu']);
        $data['adres_podmiotu'] = trim($_POST['adresPodmiotu']);
        $data['poczta_podmiotu'] = trim($_POST['pocztaPodmiotu']);
        $data['nazwa_podmiotu_err'] = $this->validator->sprawdzDlugosc($data['nazwa_podmiotu'], 4);
        $data['adres_podmiotu_err'] = $this->validator->sprawdzDlugosc($data['adres_podmiotu'], 4);
        $data['poczta_podmiotu_err'] = $this->validator->sprawdzDlugosc($data['poczta_podmiotu'], 8);


        // Dodaj do bazy danych tylko gdy nie ma błędów przypisanych do 
        // któregokolwiek pola
        if (empty($data['nazwa_podmiotu_err']) &&
            empty($data['adres_podmiotu_err']) &&
            empty($data['poczta_podmiotu_err'])) {

          // Pomyślna walidacja - dodaj do bazy danych
          $this->podmiotModel->edytujPodmiot($data);

          redirect('podmioty/zestawienie');
        }
      }

      $this->view('podmioty/edytuj', $data);
    }



    public function ajax_podmiot($id) {
      /*
       * Pobiera dane podmiotu i drukuje je w postaci json.
       * Zastosowanie do zapytania ajax.
       * Jeżeli podmiot nie istnieje w miejsu id wstawiona zostaje wartość -1
       *
       * Funkcja nie obsługuje widoku.
       *
       * Parametry:
       *  - id => id pobieranego podmiotu
       * Zwraca:
       *  - echo json postaci: { id:, nazwa:, adres_1:, adres_2: }
       */

       // tylko zalogowany
       sprawdzCzyPosiadaDostep(4,0);

       $podmiot = $this->podmiotModel->pobierzDanePodmiotu($id);
       if ($podmiot) {
         echo json_encode($podmiot);
       } else {
         echo '{"id":"-1"}';
       }
    }


  }
