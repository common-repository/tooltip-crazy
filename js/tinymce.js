(function() {
    tinymce.PluginManager.add('tooltipcrazy', function(editor, url) {
        editor.addButton('tooltipcrazy', {
            title: 'Tooltip',
            icon: 'tooltipcrazy',
            onclick: function() {
                editor.windowManager.open({
                    title: 'Tooltip',
                    body: [
                        {type: 'listbox',
                            name: 'layout',
                            label: 'Layout',
                            'values': [
                                {text: 'Classic', value: 'classic'},
                                {text: 'Bloated', value: 'bloated'},
                                {text: 'Box', value: 'box'},
                                {text: 'Sharp', value: 'sharp'},
                                {text: 'Line', value: 'line'}
                            ]
                        },
                        {
                            type: 'textbox',
                            name: 'tooltip',
                            label: 'Tooltip'
                        },
                        {type: 'listbox',
                            name: 'effect',
                            label: 'Effect',
                            'values': [
                                {text: 'Fade', value: '1'},
                                {text: 'Appear', value: '2'},
                                {text: 'Flip + Fade', value: '3'},
                                {text: 'Pop', value: '4'},
                                {text: 'Flip 90°', value: '5'}
                            ]
                        }
                    ],
                    onsubmit: function(e) {
                        editor.insertContent('[tooltip layout="' + e.data.layout + '" text="' + e.data.tooltip + '" effect="' + e.data.effect + '"]' + tinyMCE.activeEditor.selection.getContent() + '[/tooltip]');
                    }
                });
            }
        });
    });
})();