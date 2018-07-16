# Wymagania

* Apache HTTP Server z możliwością konfiguracji w plikach .htaccess
* MySql lub MariaDB
* PHP 5.6+


# Połączenie z bazą danych

W ramach niniejszych instrukcji zakładam, że czytelnik posiada dostęp do bazy danych MySql oraz zna nazwę użytkownika i hasło dostępu, a także posiada dostęp do bazy danych przy pomocy Phpmyadmin.

## Utworzenie bazy danych:
Pierwszą konieczną rzeczą jest utworzenie bazy danych. Nazwa bazy jest dowolna. (Użyta w programie to moje_sprawy)

## Populacja bazy danych tabelami:
W pliku *moje_sprawy.sql* znajdują się wszystkie potrzebne tabele systemu.
Po zalogowaniu się do panelu phpmyadmin należy wybrać utworzoną bazę danych, a następnie skorzystać z opcji Import i zaimportować plik *moje_sprawy.sql*.
Pomyślny import powinien utworzyć puste tabele w wybranej bazie danych.

## Konfiguracja połączenia z bazą danych:

W pliku *app/config/config.php* należy uzupełnić stałe:
* DB_HOST - host bazy danych - najczęściej localhost więc można pozostawić bez zmian
* DB_USER - nazwa użytkownika, który posiada dostęp do bazy danych
* DB_PASS - hasło użytkownika, który posidada dostęp do bazy danych
* DB_NAME - nazwa utworzonej bazy danych

**Uwaga:**
Jeżeli to jest możliwe to polecam utworzenie osobnego użytkownika dla połączenia z systemem **bez** prawa do usuwania (DELETE) rekordów.


# Konfiguracja stałych adresów URL

## Konfiguracja PHP:
W pliku *app/config/config.php* należy zmienic stałą URLROOT.
W obecnej konfiguracji występuje tam http://ms.test (bez slasza na końcu) co powoduje że aplikacja dostępna jest pod lokalnym adresem (z uwagi na konfigurację hostów mojego komputera) http://ms.test
Przykładowy adres dla dodawania korespondencji przychodzącej wygląda tak http://ms.test/przychodzace/dodaj

Załóżmy, że chcesz aby Twoja aplikacja dostępna była pod adresem: https://mojadomena.pl/moje-sprawy/ a adres dla dodawania konrespondencji przychodzącej był https://mojadomena.pl/moje-sprawy/przychodzace/dodaj
W tym celu w zmiennej URLROOT wpiszesz wartość https://mojadomena.pl/moje-sprawy (pamiętaj, bez slasza na końcu).

## Konfiguracja .htaccess
W pliku *public/.htaccess* należy zmienić linię 4 *Rewrite Base /public*.
Nawiązując do adresu wymienionego wyżej w tym miejscu powinno być *Rewrite Base /moje-sprawy/public*

## Konfiguracja javascript:
W pliku *public/js/main.js* należy zmienić stałą URLROOT. Jeżeli nic się nie zmieniło to znajduje się ona w drugiej linii tego pliku.
Wpisujesz tutaj taki sam adres jak w punkcie powyżej ale tym razem z końcowym slaszem!


# Pierwsze kroki w systemie

## Utworzenie konta admina:
Jeżeli wszystko przebiegło bez błędów po wpisaniu adresu serwisu powinnieneś zostać przeniesiony na stronę */pracownicy/zaloguj*.
Przed zalogowaniem konieczne jest jednak utworzenie konta administratora systemu. W tym celu udaj się pod adres */pracownicy/dodaj_admin*.
Utworzenie konta admina to operacja jednorazowa, która polega jedynie na podaniu hasła i jego powtórzeniu.
**W systemie może istnieć tylko jedno konto admina.**

W przypadku zgubienia/zapomnienia hasła admina jedyną opcją jest usunięcie całego wiersza z bazy danych z tabeli admin. Operacja taka nie naruszy danych zarejestrowanych pracowników.

## Utworzenie kont użytkowników:
Po zalogowaniu się jako admin (login admin, hasło to które ustaliłeś) pierwszym krokiem jest utworzenie kont pracowników.
Przynajmniej jedno kont musi mieć poziom dostępu *sekretariat*, które pozwala rejestrować korespondencję przychodzącą.
Przywileje można w każdej chwili zmieniać.

System nie umożliwia usuwania kont pracowników. Jeżeli konto danego pracownika nie będzie już potrzebne to zamiast zmieniać jego dane na dane innego pracownika zalecam oznaczenie go jako konta nieaktywnego.
Pracownik nieaktywny nie ma możliwości zalogowania się do systemu i nie można mu przypisać żadnych pism przychodzących.

## Dodanie numerów z jednolitego rzeczowego wykazu akt:
Jeżeli system ma służyć jednie do rejestracji korespondencji przychodzącej to można nie dodawać żadnych pozycji jrwa. Jeżeli jednak chesz mieć możliwość rejestracji pism wychodzących to musisz dodać pozycje jrwa.
Dodawanie pism wychodzących odbywa się tylko w ramach sprawy, a sprawy zakłada się w ramach pozycji z jednolitego rzeczowego wykazu akt.

Pozycje jrwa można dodawać pojedynczo lub (co jest bardziej prawdopodobne przy pierwszym dodawaniu) grupowo.
Niestety nie jestem w stanie dostosować grupowego dodawania do dowolnego formatu w jakim posiadasz swoje numery jrwa, dlatego też zastosowałem taki, który będzie łatwy do przetworzenia w systemie, ale również nie powinien sprawiać trudności w stworzeniu przy wykorzystaniu jakiegoś skryptu zewnętrznego.





