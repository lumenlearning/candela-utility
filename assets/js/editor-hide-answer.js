(function() {
  tinymce.PluginManager.add('hide_answer', function(editor) {

    editor.addButton('hide_answer', {
      title: 'Add Hidden Answer',
      image: icon_url + 'editor-hide-answer-icon.png',
      onclick: function( e ) {
        editor.windowManager.open({
          title: 'Setup a Hidden Answer',
          body: [
            {
              type: 'checkbox',
              name: 'latex',
              text: 'Is this a latex question?'
            },
            {
              type: 'textbox',
              name: 'revealText',
              label: 'Reveal Text',
              value: 'Show Answer'
            },
            {
              type: 'textbox',
              name: 'hiddenText',
              label: 'Hidden Text'
            }
          ],
          onsubmit: function(e) {

            // Generates Random ID to associate the reveal/hidden tags to eachother
            var qa_id = Math.floor(Math.random() * 1000000) + 1;

            // Hidden Text Value: What gets returned if latex is checked, etc.
            var hiddenText = "";

            if (e.data.latex && e.data.hiddenText) {
              hiddenText = "[latex]" + e.data.hiddenText + "[/latex]";
            }
            else if (e.data.latex && !e.data.hiddenText) {
              hiddenText = "[latex][/latex]";
            }
            else if (!e.data.latex && e.data.hiddenText) {
              hiddenText = e.data.hiddenText;
            }
            else {
              hiddenText = "Put Answer Here";
            }


            editor.insertContent(
              '[reveal-answer q="' + qa_id + '"]' + (!e.data.revealText ? 'Show Answer' : e.data.revealText) + '[/reveal-answer]' + '<br />' +
              '[hidden-answer a="' + qa_id + '"]' + hiddenText + '[/hidden-answer]'
            );
          }
        });
      }
    });
  });
})();
