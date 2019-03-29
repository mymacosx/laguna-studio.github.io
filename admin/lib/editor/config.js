CKEDITOR.editorConfig = function( config ) {
config.scayt_autoStartup = false;
config.scayt_contextCommands = 'off';
config.defaultLanguage = 'ru';
config.menu_subMenuDelay = 100;
config.enterMode = CKEDITOR.ENTER_BR;
config.image_previewText = 'Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Вдали от всех живут они в буквенных домах на берегу Семантика большого языкового океана. Маленький ручеек Даль журчит по всей стране и обеспечивает ее всеми необходимыми правилами. Эта парадигматическая страна, в которой жаренные члены предложения залетают прямо в рот. Даже всемогущая пунктуация не имеет власти над рыбными текстами, ведущими безорфографичный образ жизни. Однажды одна маленькая строчка рыбного текста по имени Lorem ipsum решила выйти в большой мир грамматики. Великий Оксмокс предупреждал ее о злых запятых, диких знаках вопроса и коварных точках с запятой, но текст не дал сбить себя с толку. Он собрал семь своих заглавных букв, подпоясал инициал за пояс и пустился в дорогу. Взобравшись на первую вершину курсивных гор, бросил он последний взгляд назад, на силуэт своего родного города Буквоград, на заголовок деревни Алфавит и на подзаголовок своего переулка Строчка. Грустный реторический вопрос скатился по его щеке и он продолжил свой путь. По дороге встретил текст рукопись. Она предупредила его: В моей стране все переписывается по несколько раз. Единственное, что от меня осталось, это приставка и. Возвращайся ты лучше в свою безопасную страну. Не послушавшись рукописи, наш текст продолжил свой путь.';
config.filebrowserBrowseUrl = '../../../../admin/?pop=1&do=browser&typ=file&mode=editor';
config.filebrowserFlashBrowseUrl = '../../../../admin/?pop=1&do=browser&typ=flash&mode=editor';
config.filebrowserImageBrowseLinkUrl = '../../../../admin/?pop=1&do=browser&typ=image&mode=editor';
config.filebrowserImageBrowseUrl = '../../../../admin/?pop=1&do=browser&typ=image&mode=editor';
config.filebrowserWindowWidth = '1000';
config.filebrowserWindowHeight = '600';
config.toolbar_Full = [
    ['Maximize','-','Source','-','Preview'],['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
    ['Form', 'Checkbox', 'Radio','TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],['Templates','ShowBlocks'],['Link','Unlink','Anchor'],
    '/',
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],['Bold','Italic','Underline','Strike'],
    ['Subscript','Superscript'],['NumberedList','BulletedList'],['Outdent','Indent','Blockquote','CreateDiv'],
    ['Image','Flash','Table','HorizontalRule','SpecialChar','PageBreak'],['Format','Font','FontSize'],['TextColor','BGColor']
];
config.toolbar_Content = [
    ['Maximize','-','Source','-','Preview'],['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
    ['Form', 'Checkbox', 'Radio','TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],['Templates','ShowBlocks'],['Link','Unlink','Anchor'],
    '/',
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],['Bold','Italic','Underline','Strike'],
    ['Subscript','Superscript'],['NumberedList','BulletedList'],['Outdent','Indent','Blockquote','CreateDiv'],
    ['Image','Flash','Table','HorizontalRule','SpecialChar','PageBreak'],['Format','Font','FontSize'],['TextColor','BGColor']
];
config.toolbar_Shop = [
    ['Maximize','-','Source','-','Preview'],['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],['Templates','ShowBlocks'],['Link','Unlink','Anchor'],
    '/',
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],['Bold','Italic','Underline','Strike'],['Subscript','Superscript'],
    ['NumberedList','BulletedList'],['Outdent','Indent','Blockquote','CreateDiv'],['Image','HorizontalRule','SpecialChar','PageBreak'],
    ['Format','Font','FontSize'],['TextColor','BGColor']
];
config.toolbar_ShopSettings = [
    ['Maximize','-','Source','-','Preview'],['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],['ShowBlocks','Link','Unlink','Anchor'],
    '/',
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],['Bold','Italic','Underline','Strike'],
    ['NumberedList','BulletedList'],['Image','Table','HorizontalRule'],['Format','Font','FontSize']
];
config.toolbar_Settings = [
    ['Maximize','-','Source','-','Preview'],['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],['ShowBlocks','Link','Unlink','Anchor'],
    '/',
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],['Bold','Italic','Underline','Strike'],
    ['NumberedList','BulletedList'],['Image','Table','HorizontalRule'],['Format','Font','FontSize']
];
config.toolbar_Basic = [
    ['Maximize','-','Source','-','Preview'],['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],['ShowBlocks','Link','Unlink','Anchor'],
    '/',
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],['Bold','Italic','Underline','Strike'],
    ['NumberedList','BulletedList'],['HorizontalRule'],['Format','Font','FontSize']
];
config.toolbar_OrderE = [
    ['Maximize','-','Source','-','Preview'],['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],['ShowBlocks','Link','Unlink','Anchor'],
    '/',
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],['Bold','Italic','Underline','Strike'],
    ['NumberedList','BulletedList'],['HorizontalRule'],['Format','Font','FontSize']
];
config.toolbar_Nothing = [
    ['Maximize','-','Source','-','Preview','-','SelectAll','Copy','-','Print']
];
};