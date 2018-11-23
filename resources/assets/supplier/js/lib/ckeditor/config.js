/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.extraAllowedContent= 'b i';
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	config.enterMode=2;
	config.ELEMENT_MODE_REPLACE=0;
    config.shiftEnterMode = CKEDITOR.ENTER_P;	
	
	config.extraAllowedContent = 'p(*)[*]{*};div(*)[*]{*}';
	
	config.allowedContent = true;
	
	config.forceEnterMode = true;	
	
	config.removeButtons = 'Underline,Subscript,Superscript,About';
	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	config.extraPlugins = 'div,html5validation';
	
	//config.div_wrapTable = true;
	
	//config.contentsCss = 'http://localhost/gemification/assets/admin/css/frontend_style.css';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';

	
};
