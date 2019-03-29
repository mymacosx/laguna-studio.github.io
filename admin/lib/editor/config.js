CKEDITOR.editorConfig = function( config ) {
config.scayt_autoStartup = false;
config.scayt_contextCommands = 'off';
config.defaultLanguage = 'ru';
config.menu_subMenuDelay = 100;
config.enterMode = CKEDITOR.ENTER_BR;
config.image_previewText = '������-������ �� ���������� ������ � ������ ������� � ��������� ����� ������ ������. ����� �� ���� ����� ��� � ��������� ����� �� ������ ��������� �������� ��������� ������. ��������� ������ ���� ������ �� ���� ������ � ������������ �� ����� ������������ ���������. ��� ����������������� ������, � ������� �������� ����� ����������� �������� ����� � ���. ���� ���������� ���������� �� ����� ������ ��� ������� ��������, �������� ���������������� ����� �����. ������� ���� ��������� ������� ������� ������ �� ����� Lorem ipsum ������ ����� � ������� ��� ����������. ������� ������� ������������ �� � ���� �������, ����� ������ ������� � �������� ������ � �������, �� ����� �� ��� ����� ���� � �����. �� ������ ���� ����� ��������� ����, ��������� ������� �� ���� � �������� � ������. ����������� �� ������ ������� ��������� ���, ������ �� ��������� ������ �����, �� ������ ������ ������� ������ ���������, �� ��������� ������� ������� � �� ������������ ������ �������� �������. �������� ������������ ������ �������� �� ��� ���� � �� ��������� ���� ����. �� ������ �������� ����� ��������. ��� ������������ ���: � ���� ������ ��� �������������� �� ��������� ���. ������������, ��� �� ���� ��������, ��� ��������� �. ����������� �� ����� � ���� ���������� ������. �� ������������ ��������, ��� ����� ��������� ���� ����.';
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