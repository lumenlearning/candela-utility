(function () {
    tinymce.PluginManager.add('ohm_question', function (editor) {
        editor.addButton('ohm_question', {
            text: 'OHM',
            icon: false,
            onclick: function (e) {
                editor.windowManager.open({
                    title: 'Setup an OHM embed',
                    body: [
                        {
                            type: 'textbox',
                            name: 'question_ids',
                            label: 'Question ID(s) like: 1234 or 345-6643-1231'
                        },
                        {
                            type: 'checkbox',
                            name: 'hide_question_numbers',
                            text: 'Hide question numbers?'
                        },
                        {
                            type: 'checkbox',
                            name: 'sameseed',
                            text: 'Use same seed?'
                        },
                        {
                            type: 'textbox',
                            name: 'frame_id',
                            label: 'Frame ID (blank is fine)',
                            value: ''
                        },
                        {
                            type: 'textbox',
                            name: 'height',
                            label: 'Default Height (blank is fine)'
                        }
                    ],
                    onsubmit: function (e) {

                        var options = '';

                        if (e.data.sameseed) {
                            options = options + " sameseed=1";
                        }

                        if (e.data.frame_id) {
                            options = options + ' frame_id="' + e.data.frame_id + '"';
                        }

                        if (e.data.height) {
                            options = options + ' height="' + e.data.height + '"';
                        }

                        if (e.data.hide_question_numbers) {
                            options = options + " hide_question_numbers=1";
                        }

                        editor.insertContent('[ohm_question' + options + ']' + e.data.question_ids + '[/ohm_question]');
                    }
                });
            }
        });
    });
})();
