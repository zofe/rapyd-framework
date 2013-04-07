<?php


$lang = array
(
  //dataedit
	'de.inserted'=> 'Il record è stato inserito correttamente.',
	'de.updated'=> 'Il record è stato modificato correttamente.',
	'de.deleted'=> 'Il record è stato eliminato correttamente.',

	'de.err_read'	=> 'Si è verificato un errore in fase di lettura del record.',
	'de.err_insert'	=> 'Si è verificato un errore in fase di inserimento del record.',
	'de.err_update'	=> 'Si è verificato un errore in fase di modifica del record.',
	'de.err_delete'	=> 'Si è verificato un errore in fase di cancellazione del record.',
	'de.err_unknown'=> 'Si è verificato un errore, nessun record su cui operare.',
	'de.err_dup_pk' => 'Si è verificato un errore, chiave primaria non univoca.',
	'de.err_no_model'=> 'Si è verificato un errore, manca un datamodel, usa $edit->source("tablename")',
	'de.err_no_backurl'=> 'Si è verificato un errore, devi impostare la proprieta\' "back_url"',
	
	'de.confirm_delete'=> 'Vuoi davvero eliminare il record corrente?',
	'de.inserted'=> 'Errore, chiave primaria non univoca.',

  //field text
	'fld.delete'=> 'Rimuovi',
	'fld.browse'=> 'Seleziona il file da inviare:',
	'fld.modify'=> 'Modifica',

  //buttons
	'btn.add'		=> 'Aggiungi',
	'btn.reset'	=> 'Resetta',
	'btn.search'=> 'Cerca',
	'btn.modify'=> 'Modifica',
	'btn.delete'=> 'Elimina',

	'btn.do_delete'	=> 'Elimina',
	'btn.save'		=> 'Salva',
	'btn.undo'		=> 'Annulla',
	'btn.back'		=> 'Torna all\'elenco',
	'btn.back_edit'	=> 'Mostra',
	'btn.back_error'=> 'Indietro',

	// validations
	'val.required'      => 'Il campo %s è obbligatorio.',
	'val.isset'         => 'Il campo %s è obbligatorio.',
	'val.min_length'    => 'Il campo %s deve essere lungo almeno %d caratteri.',
	'val.max_length'    => 'Il campo %s non deve superare i %d caratteri.',
	'val.exact_length'  => 'Il campo %s deve contenere esattamente %d caratteri.',
	'val.matches'       => 'Il campo %s deve coincidere con il campo %s.',
	'val.valid_email'   => 'Il campo %s deve contenere un indirizzo email valido.',
	'val.in_range'      => 'Il campo %s deve trovarsi negli intervalli specificati.',
	'val.regex'         => 'Il campo %s non coincide con i dati accettati.',
	'val.unique'        => 'Il campo %s deve essere univoco, esiste un\'altro record con lo stesso valore.',
	'val.captcha'       => 'Il campo %s non corrisponde, riprova con la nuova immagine.',
	'val.approve'       => 'Devi approvare: %s .',
	'val.valid_type'    => 'Il campo %s puo\' contenere caratteri %s',

	// field types
	'val.alpha'         => 'alfabetici',
	'val.alpha_dash'    => 'alfabetici, trattino e sottolineato',
	'val.numeric'       => 'numerici',

	// upload errors
	'val.user_aborted'  => 'Il caricamento del file %s è stato interrotto.',
	'val.invalid_type'  => 'Il file %s non è un tipo di file permesso.',
	'val.max_size'      => 'Il file %s è troppo grande. La massima dimensione consentita è %s.',
	'val.max_width'     => 'Il file %s deve avere una larghezza massima di %spx.',
	'val.max_height'    => 'Il file %s deve avere un\'altezza massima di %spx.',
	'val.min_width'     => 'Il file %s deve avere una larghezza massima di %spx.',
	'val.min_height'    => 'Il file %s deve avere un\'altezza massima di %spx.',

	// pagination
	'pag.first'         => 'Prima',
	'pag.last'          => 'Ultima',

    //generic 
    'no_results'        => 'nessun risultato'
);
