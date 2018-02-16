(function() {
  tinymce.PluginManager.add( 'glossary_page', function( editor, url ) {

    editor.addButton('glossary_page', {
      title: 'Add Glossary Page',
	  image: icon_url + 'editor-glossary-page-icon.png',
      onclick: function( e ) {
        editor.insertContent(
					'[glossary-page]<br/><br/>[/glossary-page]'
        );
      }
    });
  });
})();
