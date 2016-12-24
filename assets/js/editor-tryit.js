(function() {
  tinymce.PluginManager.add('tryit', function(editor) {

    editor.addButton('tryit', {
      text: 'Try It',
      image: false,
      onclick: function(e) {
        var selection = editor.selection.getContent();
        if (selection !== '') {
          editor.execCommand('mceReplaceContent', false, '<div class="textbox tryit">' + selection + '</div><p></p>');
        } else {
          editor.execCommand('mceInsertContent', 0, '<div class="textbox tryit"><h3 itemprop="educationalUse">Try It</h3>\n<p>Type your \'try it\' text here</p>\n<ul><li>First</li><li>Second</li></ul></div><p></p>');
        }
      }
    });
  });
})();
