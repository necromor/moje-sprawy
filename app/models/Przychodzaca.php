<?php
  /*
   * Model Przychodzace obsługuje wszystkie funkcje związane
   * z korespondencją przychodząca.
   * Funkcje te obejmują:
   *  - dodawanie nowej korespondencji - pismo lub faktura
   *  - edycję istniejącej korespondencji
   *  - tworzenie zestawień korespondencji w poszczególnych latach
   *  - tworzenie zestawień faktur w poszczególnych latach
   *
   */

  class Przychodzaca {

    private $db;

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z bazą danych
       */

      $this->db = new Database;
    }

    public function pobierzPrzychodzace($rok) {
      /*
       * Pobiera wszystkie dane o korespondencji przychodzącej
       * gdzie data wyływu odpowiada określonemu rokowi.
       *
       * Parametry:
       *  - rok => rok z daty wpływu szukanej korespondencji
       * Zwraca:
       *  - set zawierający dane korespondencji
       */

      $sql = "SELECT
              przychodzace.id,
              przychodzace.data_pisma,
              przychodzace.data_wplywu,
              przychodzace.nr_rejestru,
              przychodzace.znak,
              przychodzace.dotyczy,
              przychodzace.czy_faktura,
              przychodzace.id_pracownik,
              przychodzace.liczba_zalacznikow,
              przychodzace.kwota,
              przychodzace.nr_rejestru_faktur,
              podmioty.nazwa,
              podmioty.adres_1,
              podmioty.adres_2
              FROM przychodzace, podmioty
              WHERE data_wplywu LIKE :rok
                AND przychodzace.id_podmiot=podmioty.id
              ORDER BY nr_rejestru ASC";
      $this->db->query($sql);
      $this->db->bind(':rok', $rok . '%');

      return $this->db->resultSet();
    }

    public function pobierzFaktury($rok, $id=0) {
      /*
       * Pobiera wszystkie dane o fakturach gdzie data wyływu 
       * odpowiada określonemu rokowi.
       * Dodatkowo umożliwia zawężenie poszukiwań tylko do faktur
       * podanego podmiotu.
       *
       * Parametry:
       *  - rok => rok z daty wpływu szukanej korespondencji
       *  - id => id podmiotu, który wystawił faktury
       * Zwraca:
       *  - set zawierający dane faktur
       */

      // sprawdź czy wyszczegółowiony został podmiot
      // dla którego należy utworzyć podzestawienie
      if ($id == 0) {
        $sql = "SELECT
                przychodzace.id,
                przychodzace.data_pisma,
                przychodzace.data_wplywu,
                przychodzace.nr_rejestru,
                przychodzace.znak,
                przychodzace.dotyczy,
                przychodzace.kwota,
                przychodzace.nr_rejestru_faktur,
                podmioty.nazwa,
                podmioty.adres_1,
                podmioty.adres_2
                FROM przychodzace, podmioty
                WHERE data_wplywu LIKE :rok
                  AND przychodzace.id_podmiot=podmioty.id
                  AND czy_faktura=1
                ORDER BY nr_rejestru_faktur ASC";
        $this->db->query($sql);
        $this->db->bind(':rok', $rok . '%');
      } else {
        $sql = "SELECT
                przychodzace.id,
                przychodzace.data_pisma,
                przychodzace.data_wplywu,
                przychodzace.nr_rejestru,
                przychodzace.znak,
                przychodzace.dotyczy,
                przychodzace.kwota,
                przychodzace.nr_rejestru_faktur,
                podmioty.nazwa,
                podmioty.adres_1,
                podmioty.adres_2
                FROM przychodzace, podmioty
                WHERE data_wplywu LIKE :rok
                  AND przychodzace.id_podmiot=:id
                  AND przychodzace.id_podmiot=podmioty.id
                  AND czy_faktura=1
                ORDER BY nr_rejestru_faktur ASC";
        $this->db->query($sql);
        $this->db->bind(':rok', $rok . '%');
        $this->db->bind(':id', $id);
      }

      return $this->db->resultSet();
    }

    public function pobierzPrzychodzacaPoId($id) {
      /*
       * Pobiera wszystkie dane o korespondencji na podstawie podanego id.
       *
       * Parametry:
       *  - id => id szukanej korespondencji
       * Zwraca: 
       *  - wiersz z bazy zawierający wszystkie dane korespondencji
       */

      $sql = "SELECT przychodzace.*, podmioty.nazwa, podmioty.adres_1, podmioty.adres_2 
                     FROM przychodzace, podmioty
                     WHERE przychodzace.id=:id
                       AND przychodzace.id_podmiot=podmioty.id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      return $this->db->single();
    }

    public function pobierzPismaDoZrobienia($pracownik) {
      /*
       * Pobiera wszystkie pisma przychodzące zadekretowane do danego pracownika,
       * które nie są przypisane do żadnej sprawy lub oznaczone jako ad acta.
       * Pisma są posortowane od tego, które wpłynęło najwcześniej.
       *
       * Parametry:
       *  - pracownik => id pracownika, którego pism szukamy
       * Zwraca:
       *  - set zawierający dane korespondencji
       */

      $sql = "SELECT
              przychodzace.*,
              podmioty.nazwa,
              podmioty.adres_1,
              podmioty.adres_2
              FROM przychodzace, podmioty
              WHERE id_pracownik=:pracownik
                AND przychodzace.id_podmiot=podmioty.id
                AND przychodzace.id NOT IN (SELECT id_dokument FROM metryka WHERE rodzaj_dokumentu='1')
                AND przychodzace.id NOT IN (SELECT id_przychodzace FROM przychodzace_adacta)
              ORDER BY data_wplywu ASC";
      $this->db->query($sql);
      $this->db->bind(':pracownik', $pracownik);

      return $this->db->resultSet();
    }

    public function dodajPrzychodzaca($data) {
      /*
       * Dodaje nową korespondencję do bazy danych.
       * Na podstawie daty wpływu tworzy numer rejestru oraz w przypadku faktury
       * nume rejestru faktur.
       *
       * Parametry:
       *  - data => tablica zawierająca dane nowej korespondencji
       * Zwraca:
       *  - boolean
       */

      // pobierz kolejny numer rejestru w danym roku
      $nr_rejestru = $this->tworzNrRejestru($data['data_wplywu']);

      // ustaw wartości domyślne na pozostałe pola
      // w zależności od tego czy dodawane pismo czy faktura
      if ($data['czy_faktura'] == '0') {
        $liczba_zalacznikow = $data['liczba_zalacznikow'];
        $id_pracownik = pobierzIdNazwy($data['dekretacja']);
        $kwota = 0.00;
        $nr_rejestru_faktur = 0;
      } else {
        $liczba_zalacznikow = 0;
        $id_pracownik = 0;
        $kwota = $data['kwota'];
        // pobierz kolejny nr rejestru faktur
        $nr_rejestru_faktur = $this->tworzNrRejestruFaktur($data['data_wplywu']);
      }

      $id_podmiot = pobierzIdNazwy($data['podmiot_nazwa']);

      $sql = "INSERT INTO przychodzace (nr_rejestru, znak, data_pisma, data_wplywu, dotyczy, id_podmiot, czy_faktura, id_pracownik, liczba_zalacznikow, kwota, nr_rejestru_faktur) VALUES (:nr_rejestru, :znak, :data_pisma, :data_wplywu, :dotyczy, :id_podmiot, :czy_faktura, :id_pracownik, :liczba_zalacznikow, :kwota, :nr_rejestru_faktur)";
      $this->db->query($sql);
      $this->db->bind(':nr_rejestru', $nr_rejestru);
      $this->db->bind(':znak', $data['znak']);
      $this->db->bind(':data_pisma', $data['data_pisma']);
      $this->db->bind(':data_wplywu', $data['data_wplywu']);
      $this->db->bind(':dotyczy', $data['dotyczy']);
      $this->db->bind(':id_podmiot', $id_podmiot);
      $this->db->bind(':czy_faktura', $data['czy_faktura']);
      $this->db->bind(':id_pracownik', $id_pracownik);
      $this->db->bind(':liczba_zalacznikow', $liczba_zalacznikow);
      $this->db->bind(':kwota', $kwota);
      $this->db->bind(':nr_rejestru_faktur', $nr_rejestru_faktur);

      // dodaj do bazy i zwróć tablicę z numerem rejestru i nr rejestru faktur
      // jeżeli błąd to zwróć -1 jako wartości
      $numery = [
        'nr_rejestru' => '-1',
        'nr_rejestru_faktur' => '-1'
      ];
      if ($this->db->execute()) {
        $numery['nr_rejestru'] = $nr_rejestru;
        $numery['nr_rejestru_faktur'] = $nr_rejestru_faktur;
      }
      return $numery;
    }

    public function edytujPrzychodzaca($data) {
      /*
       * Zmienia dane istniejącej korespondencji.
       * W danych wejściowych podane jest id korespondencji do zmiany.
       * W celu ułatwienia funkcja nie sprawdza które dane są faktycznie nowe
       * ale wstawia wszystkie pochodzące z formularza.
       *
       * WAŻNE:
       * Numery rejestrów nie podlegają zmianie!
       *
       * Parametry:
       *  - data => tablica zawierająca dane istniejącego pracownika
       * Zwraca:
       *  - boolean
       */

      // ustaw wartości domyślne na pozostałe pola
      // w zależności od tego czy dodawane pismo czy faktura
      if ($data['czy_faktura'] == '0') {
        $liczba_zalacznikow = $data['liczba_zalacznikow'];
        $id_pracownik = pobierzIdNazwy($data['dekretacja']);
        $kwota = 0.00;
      } else {
        $liczba_zalacznikow = 0;
        $id_pracownik = 0;
        $kwota = $data['kwota'];
      }

      $id_podmiot = pobierzIdNazwy($data['podmiot_nazwa']);

      $sql = "UPDATE przychodzace SET znak=:znak, data_pisma=:data_pisma, data_wplywu=:data_wplywu, dotyczy=:dotyczy, id_podmiot=:id_podmiot, id_pracownik=:id_pracownik, liczba_zalacznikow=:liczba_zalacznikow, kwota=:kwota WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $data['id']);
      $this->db->bind(':znak', $data['znak']);
      $this->db->bind(':data_pisma', $data['data_pisma']);
      $this->db->bind(':data_wplywu', $data['data_wplywu']);
      $this->db->bind(':dotyczy', $data['dotyczy']);
      $this->db->bind(':id_podmiot', $id_podmiot);
      $this->db->bind(':id_pracownik', $id_pracownik);
      $this->db->bind(':liczba_zalacznikow', $liczba_zalacznikow);
      $this->db->bind(':kwota', $kwota);

      // zmień dane w bazie
      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function oznaczAA($pismo, $jrwa) {
      /*
       * Oznacza istniejącą korepondencję jako ad acta.
       *
       * Parametry:
       *  - pismo => id pisma do oznaczenia ad acta
       *  - jrwa => id numeru jrwa
       * Zwraca:
       *  - boolean
       */

      $sql = "INSERT INTO przychodzace_adacta (id_przychodzace, id_jrwa) VALUES (:pismo, :jrwa)";
      $this->db->query($sql);
      $this->db->bind(':pismo', $pismo);
      $this->db->bind(':jrwa', $jrwa);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function pobierzJrwaAA($id) {
      /*
       * Pobiera obiekt jrwa pisma oznaczonego jako ad acta.
       * Jeżeli pismo nie jest oznaczone jako ad acta zwraca NULL
       *
       * Parametry:
       *  - id => id szukanego pisma
       * Zwraca:
       *  - string => numer jrwa lub -1 gdy pismo nie jest ad acta
       */

      $sql = "SELECT jrwa.* FROM przychodzace_adacta, jrwa
                     WHERE przychodzace_adacta.id_przychodzace=:id
                      AND przychodzace_adacta.id_jrwa=jrwa.id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      $row = $this->db->single();
      if ($this->db->rowCount() == 0) {
        return NULL;
      } else {
        return $row;
      }
    }

    public function czyMoznaDodacPismoDoSprawy($id) {
      /*
       * Sprawdza czy podane pismo przypisane już jest do sprawy lub oznaczone jako ad acta.
       *
       * Parametry:
       *  - id => id szukanego pisma
       * Zwraca:
       *  - boolean => true jeżeli pismo bez sprawy i nie ad acta
       */

      // ZAIMPLEMENTOWAĆ SPRAWDZANIE W AD ACTA

      $sql = "SELECT * FROM metryka WHERE rodzaj_dokumentu='1' AND id_dokument=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      $row = $this->db->single();
      return $this->db->rowCount() == 0;

    }

    public function pobierzSprawePrzychodzacego($id) {
      /*
       * Pobiera dane sprawy, do której pismo przychodzące jest przypisane.
       * Jeżeli pismo nie jest przypisane do żadnej sprawy to zwraca NULL
       *
       * Parametry:
       *  - id => id szukanego pisma
       * Zwraca:
       *  - obiekt sprawy lub NULL
       */

      $sql = "SELECT sprawy.* FROM sprawy, metryka
                     WHERE metryka.rodzaj_dokumentu='1'
                      AND metryka.id_dokument=:id
                      AND sprawy.id=metryka.id_sprawa";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      $row = $this->db->single();
      if ($this->db->rowCount() == 0) {
        return NULL;
      } else {
        return $row;
      }
    }

    private function tworzNrRejestru($data) {
      /*
       * Funkcja która tworzy następny numer rejestru w danym roku
       * Algorytm: pobiera liczbę zarejestrowanej korespondencji w danym roku -
       * liczy się data wpływu i zwiększa otrzymaną wartość o 1
       */

       list($rok, $miesiac, $dzien) = explode('-', $data);

       $sql = "SELECT COUNT(id) as total FROM przychodzace WHERE data_wplywu LIKE :rok";
       $this->db->query($sql);
       $this->db->bind(':rok', $rok . '%');
       $row = $this->db->single();

       return intval($row->total) + 1;
    }

    private function tworzNrRejestruFaktur($data) {
      /*
       * Funkcja która tworzy następny numer rejestru faktur w danym roku
       * Algorytm: pobiera liczbę zarejestrowanych faktur w danym roku -
       * liczy się data wpływu i zwiększa otrzymaną wartość o 1
       */

       list($rok, $miesiac, $dzien) = explode('-', $data);

       $sql = "SELECT COUNT(id) as total FROM przychodzace WHERE data_wplywu LIKE :rok AND czy_faktura=1";
       $this->db->query($sql);
       $this->db->bind(':rok', $rok . '%');
       $row = $this->db->single();

       return intval($row->total) + 1;
    }

    public function szukajPrzychodzace($data) {
      /*
       * Szuka korespondencji przychodzącej na podstawie określonych kryterów.
       * Nie mamy wpływu ile kryterów jest podanych i jaka kombinacja więc
       * wszystkie wyszukiwania opierają się nie na = a LIKE.
       * Dla pól nazwy i dotyczy zastosowano mechanizm %wartosc%, dla pozostałych wartosc%.
       *
       * Parametry:
       *  - data => zbiór danych z formularza, możliwe pola to:
       *             - numer rejestru
       *             - znka pisma
       *             - data pisma
       *             - data wpływu
       *             - treść dotyczy
       *             - nazwa nadawcy (podmiotu)
       * Zwraca:
       *  - set zawierający dane korespondencji posortowane rosnąco według daty utworzenia
       */

      $sql = "SELECT
              przychodzace.*,
              podmioty.nazwa,
              podmioty.adres_1,
              podmioty.adres_2
              FROM przychodzace, podmioty
              WHERE
                nr_rejestru LIKE :nr_rej
                AND znak LIKE :znak
                AND data_pisma LIKE :data_pisma
                AND data_wplywu LIKE :data_wplywu
                AND dotyczy LIKE :dotyczy
                AND podmioty.nazwa LIKE :nazwa
                AND przychodzace.id_podmiot=podmioty.id
              ORDER BY przychodzace.utworzone ASC";
      $this->db->query($sql);
      $this->db->bind(':nr_rej', $data['nr_rej'] . '%');
      $this->db->bind(':znak', $data['znak'] . '%');
      $this->db->bind(':data_pisma', $data['data_pisma'] . '%');
      $this->db->bind(':data_wplywu', $data['data_wplywu'] . '%');
      $this->db->bind(':dotyczy', '%' . $data['dotyczy'] . '%');
      $this->db->bind(':nazwa', '%' . $data['nazwa'] . '%');

      return $this->db->resultSet();
    }

  }
