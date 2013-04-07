<?php


$lang = array
(
	//dataedit
	'de.inserted'=> 'Nouvel enregistrement créé.',
	'de.updated'=> 'Enregistrement mis à jour.',
	'de.deleted'=> 'Enregistrement effacé.',

	'de.err_read'	=> 'Erreur de lecture de l\'enregistrement.',
	'de.err_insert'	=> 'Erreur de création d\'enregistrement.',
	'de.err_update'	=> 'Erreur de mise à jour de l\'enregistrement.',
	'de.err_delete'	=> 'Erreur de suppression de l\'enregistrement.',
	'de.err_unknown'=> 'Erreur, pas d\'enregistrement sélectionné',
	'de.err_dup_pk' => 'Erreur, dupliqué clé primaire',
	'de.err_no_model'=> 'Erreur, datamodel manqués, utilisez $edit->source("tablename")',
	'de.err_no_backurl'=> 'Erreur, vous devez définir" back_url propriété',

	'de.confirm_delete'=> 'Voulez-vous réellement supprimer l\'enregistrement ?',
	'de.inserted'=> 'Erreur, la clé primaire n\'est pas unique.',

	//field text
	'fld.delete'=> 'Effacer',
	'fld.browse'=> 'Selectionner le fichier :',
	'fld.modify'=> 'Modifier',

	//buttons
	'btn.add'	=> 'Ajouter',
	'btn.reset'	=> 'r.à.z',
	'btn.search'=> 'Rechercher',
	'btn.modify'=> 'Modifier',
	'btn.delete'=> 'Supprimer',

	'btn.do_delete'	=> 'Effacer',
	'btn.save'	=> 'Sauver',
	'btn.undo'	=> 'Annuler',
	'btn.back'	=> 'Retour à la liste',
	'btn.back_edit'	=> 'Montrer',
	'btn.back_error'=> 'R',

	// validations
	'val.required'      => 'Le champ %s est requis.',
	'val.isset'         => 'Le champ %s est défini.', // ? "is required" ?
	'val.min_length'    => 'Le champ %s doit avoir une longueur d\'au moins %d charactères.',
	'val.max_length'    => 'Le champ %s ne doit pas avoir une longueur supérieure à %d caractères.',
	'val.exact_length'  => 'Le champ %s doir avoir une longueur exacte de %d caractères.',
	'val.matches'       => 'Le champ %s doit correspondre au champ %s.',
	'val.valid_email'   => 'Le champ %s doit contenir une adresse email valide.',
	'val.in_range'      => 'Le champ %s doit avoir une valeur comprise entre %s et %s .',
	'val.regex'         => 'Le champ %s ne contient pas une valeur acceptée.',
	'val.unique'	    => 'Le champ %s doit être unique, il y a déjà un autre champ avec cette valeur.',
	'val.captcha'	    => 'Le champ %s ne correspond pas à l\'image captcha. Ressayez avec une nouvelle image',
	'val.approve'	      => 'Vous devez approuver %s .',
	'val.valid_type'	  => 'Le champ %s peut contenir %s caractères.',

	// field types
	'val.alpha'         => 'alphabétique',
	'val.alpha_dash'    => 'alphabétique, dièse, et underscore',
	'val.numeric'       => 'numérique',

	// upload errors
	'val.user_aborted'  => 'Le transfert du fichier %s a été abandonné.',
	'val.invalid_type'  => 'Le fichier %s n\'est pas un type de fichier permis.',
	'val.max_size'      => 'Le fichier %s est trop volumineux. La taille maximale autorisée est %s.',
	'val.max_width'     => 'L\'image %s est trop grande. La largeur maxi. autorisée est %s pixels.',
	'val.max_height'    => 'L\'image %s est trop grande. La hauteur maxi. autorisée est %s pixels.',
	'val.min_width'     => 'L\'image %s est trop petite. La largeur mini. autorisée est %s pixels.',
	'val.min_height'    => 'L\'image %s est trop petite. La hauteur mini. autorisée est %s pixels.',

	// pagination
	'pag.first'         => 'Prem.',
	'pag.last'          => 'Dern.',


);
