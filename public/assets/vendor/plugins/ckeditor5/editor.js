import {
    ClassicEditor,
    AccessibilityHelp,
    Autoformat,
    AutoImage,
    Autosave,
    Bold,
    Code,
    Essentials,
    FontBackgroundColor,
    FontColor,
    FontFamily,
    FontSize,
    FullPage,
    GeneralHtmlSupport,
    Highlight,
    HtmlComment,
    HtmlEmbed,
    ImageBlock,
    ImageCaption,
    ImageInline,
    Link,
    // AutoLink,
    ImageInsertViaUrl,
    ImageResize,
    ImageStyle,
    ImageTextAlternative,
    ImageToolbar,
    Italic,
    List,
    ListProperties,
    MediaEmbed,
    Paragraph,
    PasteFromOffice,
    RemoveFormat,
    SelectAll,
    SourceEditing,
    SpecialCharacters,
    SpecialCharactersArrows,
    SpecialCharactersCurrency,
    SpecialCharactersEssentials,
    SpecialCharactersLatin,
    SpecialCharactersMathematical,
    SpecialCharactersText,
    Strikethrough,
    Table,
    TableCaption,
    TableCellProperties,
    TableColumnResize,
    TableProperties,
    TableToolbar,
    TextTransformation,
    TodoList,
    Underline,
    Undo
} from 'ckeditor5';

const editorConfig = {
    toolbar: {
        items: [
            'undo',
            'redo',
            '|',
            'sourceEditing',
            '|',
            'fontSize',
            'fontFamily',
            'fontColor',
            'fontBackgroundColor',
            '|',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            'code',
            'removeFormat',
            '|',
            'specialCharacters',
            'Link',
            // 'AutoLink',
            'insertImageViaUrl',
            'mediaEmbed',
            'insertTable',
            'highlight',
            'htmlEmbed',
            '|',
            'bulletedList',
            'numberedList',
            'todoList'
        ],
        shouldNotGroupWhenFull: false
    },
    plugins: [
        AccessibilityHelp,
        Autoformat,
        AutoImage,
        Autosave,
        Bold,
        Code,
        Essentials,
        FontBackgroundColor,
        FontColor,
        FontFamily,
        FontSize,
        FullPage,
        GeneralHtmlSupport,
        Highlight,
        HtmlComment,
        HtmlEmbed,
        ImageBlock,
        ImageCaption,
        ImageInline,
        Link,
        // AutoLink,
        ImageInsertViaUrl,
        ImageResize,
        ImageStyle,
        ImageTextAlternative,
        ImageToolbar,
        Italic,
        List,
        ListProperties,
        MediaEmbed,
        Paragraph,
        PasteFromOffice,
        RemoveFormat,
        SelectAll,
        SourceEditing,
        SpecialCharacters,
        SpecialCharactersArrows,
        SpecialCharactersCurrency,
        SpecialCharactersEssentials,
        SpecialCharactersLatin,
        SpecialCharactersMathematical,
        SpecialCharactersText,
        Strikethrough,
        Table,
        TableCaption,
        TableCellProperties,
        TableColumnResize,
        TableProperties,
        TableToolbar,
        TextTransformation,
        TodoList,
        Underline,
        Undo
    ],
    fontFamily: {
        supportAllValues: true
    },
    fontSize: {
        options: [10, 12, 14, 'default', 18, 20, 22],
        supportAllValues: true
    },
    htmlSupport: {
        allow: [
            {
                name: /^.*$/,
                styles: true,
                attributes: true,
                classes: true
            }
        ]
    },
    image: {
        toolbar: [
            'toggleImageCaption',
            'imageTextAlternative',
            '|',
            'imageStyle:inline',
            'imageStyle:wrapText',
            'imageStyle:breakText',
            '|',
            'resizeImage'
        ]
    },
   list: {
        properties: {
            styles: true,
            startIndex: true,
            reversed: true
        }
    },
    placeholder: 'Type or paste your content here!',
    table: {
        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
    }
};




// ClassicEditor.create(document.querySelector('#productDescription'), editorConfig).then(newEditor => {
//     productDescriptionEditor = newEditor;
// });


if (document.querySelector('#productDescription')) {
    ClassicEditor
        .create(document.querySelector('#productDescription'), editorConfig)
        .then(newEditor => {
            productDescriptionEditor = newEditor;
        })
        .catch(error => {
            console.error(error);
        });
}

if (document.querySelector('#description')) {

ClassicEditor
    .create(document.querySelector('#description'), editorConfig)
    .then(newEditor => {
        description = newEditor;
    })
    .catch(error => {
        console.error(error);
    });

}


if (document.querySelector('#r_newsletterMessage')) {
    ClassicEditor
        .create(document.querySelector('#r_newsletterMessage'), editorConfig)
        .then(newEditor => {
            r_newsletterMessageEditor = newEditor;
        })
        .catch(error => {
            console.error(error);
        });
}

if (document.querySelector('#r_eventFormMessage')) {
    ClassicEditor
        .create(document.querySelector('#r_eventFormMessage'), editorConfig)
        .then(newEditor => {
            r_eventFormMessageEditor = newEditor;
        })
        .catch(error => {
            console.error(error);
        });
}

if (document.querySelector('#r_submitMessageMessage')) {
    ClassicEditor
        .create(document.querySelector('#r_submitMessageMessage'), editorConfig)
        .then(newEditor => {
            r_submitMessageMessageEditor = newEditor;
        })
        .catch(error => {
            console.error(error);
        });
}

if (document.querySelector('#r_requestOfferMessage')) {
    ClassicEditor
        .create(document.querySelector('#r_requestOfferMessage'), editorConfig)
        .then(newEditor => {
            r_requestOfferMessageEditor = newEditor;
        })
        .catch(error => {
            console.error(error);
        });
}

if (document.querySelector('#r_completeBookingMessage')) {
    ClassicEditor
        .create(document.querySelector('#r_completeBookingMessage'), editorConfig)
        .then(newEditor => {
            r_completeBookingMessageEditor = newEditor;
        })
        .catch(error => {
            console.error(error);
        });
}

if (document.querySelector('#r_completeEdietBookingMessage')) {
    ClassicEditor
        .create(document.querySelector('#r_completeEdietBookingMessage'), editorConfig)
        .then(newEditor => {
            r_completeEdietBookingMessageEditor = newEditor;
        })
        .catch(error => {
            console.error(error);
        });
}
