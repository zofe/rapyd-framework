<?php


$lang = array
(
  //dataedit
	'de.inserted'=> 'Vytvořen nový záznam.',
	'de.updated'=> 'Záznam byl změněn.',
	'de.deleted'=> 'Záznam byl smazán.',

	'de.err_read'	=> 'Chyba při čtení záznamu.',
	'de.err_insert'	=> 'Chyba při vytváření záznamu.',
	'de.err_update'	=> 'Chyba při změně záznamu.',
	'de.err_delete'	=> 'Chyba při mazání záznamu.',
	'de.err_unknown'=> 'Chyba, nebyl vybrán žádný záznam.',
	'de.err_dup_pk' => 'Chyba, duplicitní primární klíč.',
	'de.err_no_model'=> 'Chyba, chybný nebo chybějící datový model, použijte $edit->source("tablename")',
	'de.err_no_backurl'=> 'Chyba, musíte nastavit hodnotu "back_url"',
	
	'de.confirm_delete'=> 'Opravdu si přejete smazat vybraný záznam?',
	'de.inserted'=> 'Chyba, není jedinečný primární klíč.',

  //field text
	'fld.delete'=> 'Smazat',
	'fld.browse'=> 'Vyberte soubor:',
	'fld.modify'=> 'Upravit',

  //buttons
	'btn.add'		=> 'Přidat',
	'btn.reset'	=> 'Obnovit',
	'btn.search'=> 'Vyhledat',
	'btn.modify'=> 'Upravit',
	'btn.delete'=> 'Smazat',

	'btn.do_delete'	=> 'Smazat',
	'btn.save'		=> 'Uložit',
	'btn.undo'		=> 'Zrušit změnu',
	'btn.back'		=> 'Zpět na seznam',
	'btn.back_edit'	=> 'Ukaž',
	'btn.back_error'=> 'Zpět',

	// validations
	'val.required'      => 'Položka "%s" je požadovaná.',
	'val.isset'         => 'Položka "%s" je požadovaná.',
	'val.min_length'    => 'Položka "%s" musí mít alespoň %d znaků.',
	'val.max_length'    => 'Položka "%s" nesmí být delší než %d znaků.',
	'val.exact_length'  => 'Tato položka "%s" musí obsahovat přesně %d znaků.',
	'val.matches'       => 'Položka musí být stejná s %s - %s položkou',
	'val.valid_email'   => 'Položka "%s" musí obsahovat platnou e-mailovou adresu.',
	'val.in_range'      => 'Položka "%s" musí být v uvedeném rozsahu.',
	'val.regex'         => 'Položka "%s" neodpovídá požadovanému obsahu.',
	'val.unique'        => 'Položka "%s" musí být unikátní. Je zde další položka se stejnou hodnotou.',
	'val.captcha'       => 'Položka "%s" neodpovídá obsahu obzázku, zkuste to znovu s novým obrázkem.',
	'val.approve'       => 'Musíte potvrdit: %s.',
	'val.valid_type'    => 'Položka "%s" může obsahovat %s znaky.',

	// field types
	'val.alpha'         => 'písmena',
	'val.alpha_dash'    => 'písmena, pomlčka a podtržítko',
	'val.numeric'       => 'čísla',

	// upload errors
	'val.user_aborted'  => 'Soubor "%s" byl zrušen během nahrávání.',
	'val.invalid_type'  => 'Soubor "%s" není povoleného typu.',
	'val.max_size'      => 'Soubor "%s" je příliš velký. Maximální povolená velikost je %s.',
	'val.max_width'     => 'Obrázek v souboru "%s" musí mít maximální šířku %spx.',
	'val.max_height'    => 'Obrázek v souboru "%s" musí mít maximální výšku %spx.',
	'val.min_width'     => 'Obrázek v souboru "%s" musí mít minimální šířku %spx.',
	'val.min_height'    => 'Obrázek v souboru "%s" musí mít minimální výšku %spx.',

	// pagination
	'pag.first'         => 'První',
	'pag.last'          => 'Poslední',

    //generic 
    'no_results'        => 'no results'
);