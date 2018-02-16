(function() {
  tinymce.PluginManager.add( 'glossary_entry', function( editor, url ) {

    editor.addButton('glossary_entry', {
      title: 'Add Glossary Entry',
	  image: icon_url + 'editor-glossary-entry-icon.png',
      onclick: function( e ) {
        editor.windowManager.open({
          title: 'Add a Glossary Entry',
          body: [
            {
              type: 'textbox',
              multiline: true,
              name: 'term',
              label: 'Term',
              value: ''
            },
            {
              type: 'textbox',
              multiline: true,
              name: 'definition',
              label: 'Definition',
							value: ''
            }
          ],
          onsubmit: function( e ) {
            editor.insertContent(
							'[glossary-term]' + e.data.term + '[/glossary-term]<br/>' +
							'[glossary-definition]' + e.data.definition + '[/glossary-definition]'
            );
          }
        });
      }
    });
  });
})();
