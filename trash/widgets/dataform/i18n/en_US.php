<?php


$lang = array
(
  //dataedit
	'de.inserted'=> 'New record created.',
	'de.updated'=> 'The record has been updated.',
	'de.deleted'=> 'The record has been deleted.',

	'de.err_read'	=> 'Error reading record.',
	'de.err_insert'	=> 'Error creating record.',
	'de.err_update'	=> 'Error updating record.',
	'de.err_delete'	=> 'Error deleting record.',
	'de.err_unknown'=> 'Error, no record selected',
	'de.err_dup_pk' => 'Error, duplicated primary key',
	'de.err_no_model'=> 'Error, missed datamodel, use $edit->source("tablename")',
	'de.err_no_backurl'=> 'Error, you must set "back_url" property',
	
	'de.confirm_delete'=> 'Do you want to eliminate the record?',
	'de.inserted'=> 'Error, primary key not unique.',

	//field text
	'fld.delete'=> 'Erase',
	'fld.browse'=> 'Select the file:',
	'fld.modify'=> 'Modify',

	//buttons
	'btn.add'	=> 'Add',
	'btn.reset'	=> 'Reset',
	'btn.search'=>	'Search',
	'btn.modify'=>	'Modify',
	'btn.delete'=>	'Delete',

	'btn.do_delete'	=> 'Delete',
	'btn.save'	=> 'Save',
	'btn.undo'	=> 'Undo',
	'btn.back'	=> 'Back to the list',
	'btn.back_edit'	=> 'Show',
	'btn.back_error'=> 'Back',

	// validations
	'val.required'      => 'The %s field is required.',
	'val.isset'         => 'The %s field is required.',
	'val.min_length'    => 'The %s field must be at least %d characters long.',
	'val.max_length'    => 'The %s field must be %d characters or fewer.',
	'val.exact_length'  => 'The %s field must be exactly %d characters.',
	'val.matches'       => 'The %s field must match the %s field.',
	'val.valid_email'   => 'The %s field must contain a valid email address.',
	'val.in_range'      => 'The %s field must be between %s and %s .',
	'val.regex'         => 'The %s field does not match accepted input.',
	'val.unique'	    => 'The %s field must be unique, there is another field with this value.',
	'val.captcha'	    => 'The %s field does not match the captcha image, retry with the new one',
	'val.approve'	    => 'You must approve %s .',
	'val.valid_type'	=> 'The %s field can contain %s chars',

	// field types
	'val.alpha'         => 'alphabetical',
	'val.alpha_dash'    => 'alphabetical, dash, and underscore',
	'val.numeric'       => 'numeric',

	// upload errors
	'val.user_aborted'  => 'The %s file was aborted during upload.',
	'val.invalid_type'  => 'The %s file is not an allowed file type.',
	'val.max_size'      => 'The %s file you uploaded was too large. The maximum size allowed is %s.',
	'val.max_width'     => 'The %s file you uploaded was too big. The maximum allowed width is %spx.',
	'val.max_height'    => 'The %s file you uploaded was too big. The maximum allowed height is %spx.',
	'val.min_width'     => 'The %s file you uploaded was too small. The minimum allowed width is %spx.',
	'val.min_height'    => 'The %s file you uploaded was too small. The minimum allowed height is %spx.',

	// pagination
	'pag.first'         => 'First',
	'pag.last'          => 'Last',


);
