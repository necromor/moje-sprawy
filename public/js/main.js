// na dzień dobry ukryj pola w zależności które radio jest zaznaczone
if ($('#radioPismo').is(':checked')) {
    $('#fakturaRow1').hide();
} else {
    $('#pismoRow1').hide();
    $('#pismoRow2').hide();
}

// obsługa zmiany opcji pismo czy faktura
$('input[name=czyFaktura]:radio').on('click', function(e) {
  if (e.target.id == 'radioFaktura') {
    // ukryj pola dotyczące pisma
    $('#pismoRow1').hide();
    $('#pismoRow2').hide();
    // pokaż pole dotyczące faktury
    $('#fakturaRow1').show();
    // ustaw domyślne wartości
    $('#liczbaZalacznikow').val('0');
    $('#dekretacja').val('');
  } else {
    // pokaż pola dotyczące pisma
    $('#pismoRow1').show();
    $('#pismoRow2').show();
    // ukryj pole dotyczące faktury
    $('#fakturaRow1').hide();
    // ustaw domyślną wartość
    $('#kwota').val('0.00');
  }
});

// obsługa zmiany opcji istniejący / nowy podmiot
$('input[name=czyNowy]:radio').on('click', function(e) {
  if (e.target.id == 'radioIstniejacy') {
    // zablokuj pola adresu i poczty
    $('#adresPodmiotu').prop('disabled', true);
    $('#pocztaPodmiotu').prop('disabled', true);
  } else {
    // odblokuj pola adresu i poczty
    $('#adresPodmiotu').prop('disabled', false);
    $('#pocztaPodmiotu').prop('disabled', false);
  }
    // wyczyść wszystkie pola
    $('#nazwaPodmiotu').val('');
    $('#adresPodmiotu').val('');
    $('#pocztaPodmiotu').val('');
});
