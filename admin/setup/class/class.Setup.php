<?php
########################################################################
# ******************  SX CONTENT MANAGEMENT SYSTEM  ****************** #
# *       Copyright © Alexander Voloshin * All Rights Reserved       * #
# ******************************************************************** #
# *  http://sx-cms.ru   *  cms@sx-cms.ru  *   http://www.status-x.ru * #
# ******************************************************************** #
########################################################################
if (!defined('SX_DIR')) {
    header('Refresh: 0; url=/index.php?p=notfound', true, 404); exit;
}

class Setup {

    /* Метод */
    public static function get() {
        SX::getCheckInstall();
        View::get()->assign('title', SX::$lang['NameSite']);
        View::get()->assign('content', View::get()->fetch(THEME . '/welcome.tpl'));
        View::get()->display(THEME . '/main.tpl');
    }

    /* Метод */
    public static function getStep1() {
        $params = array();
        $params['php'] = SX::checkVersion() ? '0' : '1';
        $params['safemode'] = ini_get('safe_mode') == 1 ? '1' : '0';
        $params['magic_quotes_gpc'] = ini_get('magic_quotes_gpc') == 1 ? '1' : '0';
        $params['magic_quotes_runtime'] = ini_get('magic_quotes_runtime') == 1 ? '1' : '0';
        $params['magic_quotes_sybase'] = ini_get('magic_quotes_sybase') == 1 ? '1' : '0';
        $params['memory_limit'] = ini_get('memory_limit');
        $params['iconv'] = extension_loaded('iconv') ? '1' : '0';
        $params['mysqli'] = extension_loaded('mysqli') ? '1' : '0';
        $params['gd'] = extension_loaded('gd') ? '1' : '0';
        $params['mbstring'] = extension_loaded('mbstring') ? '1' : '0';
        $params['zlib'] = extension_loaded('zlib') ? '1' : '0';
        $params['spl'] = extension_loaded('spl') ? '1' : '0';
        $params['session'] = SX::checkSession() ? '1' : '0';
        View::get()->assign('params', $params);
        View::get()->assign('title', SX::$lang['NameSite']);
        View::get()->assign('content', View::get()->fetch(THEME . '/step1.tpl'));
        View::get()->display(THEME . '/main.tpl');
    }

    /* Метод */
    public static function getStep2() {
        SX::getCheckInstall();
        $error_path = false;
        $writeable = array();
        $writeable[] = '/config/db.config.php';
        $writeable[] = '/temp/cache/';
        $writeable[] = '/temp/private/';
        $writeable[] = '/temp/compiled/';
        $writeable[] = '/temp/compiled/1/';
        $writeable[] = '/temp/compiled/2/';
        $writeable[] = '/temp/compiled/3/';
        $writeable[] = '/temp/compiled/1/main/';
        $writeable[] = '/temp/compiled/2/main/';
        $writeable[] = '/temp/compiled/3/main/';
        $writeable[] = '/temp/compiled/1/admin/';
        $writeable[] = '/temp/compiled/2/admin/';
        $writeable[] = '/temp/compiled/3/admin/';
        $writeable[] = '/uploads/';
        $writeable[] = '/uploads/articles/';
        $writeable[] = '/uploads/forum/';
        $writeable[] = '/uploads/attachments/';
        $writeable[] = '/uploads/avatars/';
        $writeable[] = '/uploads/cheats/';
        $writeable[] = '/uploads/cheats_files/';
        $writeable[] = '/uploads/content/';
        $writeable[] = '/uploads/downloads/';
        $writeable[] = '/uploads/downloads_files/';
        $writeable[] = '/uploads/galerie/';
        $writeable[] = '/uploads/galerie_icons/';
        $writeable[] = '/uploads/screenshots/';
        $writeable[] = '/uploads/links/';
        $writeable[] = '/uploads/manufacturer/';
        $writeable[] = '/uploads/media/';
        $writeable[] = '/uploads/partner/';
        $writeable[] = '/uploads/products/';
        $writeable[] = '/uploads/videos/';
        $writeable[] = '/uploads/audios/';
        $writeable[] = '/uploads/shop/';
        $writeable[] = '/uploads/shop/customerfiles/';
        $writeable[] = '/uploads/shop/files/';
        $writeable[] = '/uploads/shop/icons/';
        $writeable[] = '/uploads/shop/icons_categs/';
        $writeable[] = '/uploads/shop/navi_categs/';
        $writeable[] = '/uploads/shop/payment_icons/';
        $writeable[] = '/uploads/shop/shipper_icons/';
        $writeable[] = '/uploads/user/';
        $writeable[] = '/uploads/user/gallery/';

        $error_not_writables = array();
        foreach ($writeable as $must_writeable) {
            if (!is_writable(SX_DIR . $must_writeable)) {
                chmod(SX_DIR . $must_writeable, 0777);
            }
            if (!is_writable(SX_DIR . $must_writeable)) {
                $error_path = true;
                $error_not_writables[] = $must_writeable;
            }
        }

        View::get()->assign('errors_path', $error_path);
        View::get()->assign('error_not_writables', $error_not_writables);
        View::get()->assign('title', SX::$lang['NameSite'] . ' - ' . SX::$lang['Step1']);
        View::get()->assign('content', View::get()->fetch(THEME . '/step2.tpl'));
        View::get()->display(THEME . '/main.tpl');
    }

    /* Метод */
    public static function getStep3() {
        $error = false;
        if (empty($_POST['dbhost']) || empty($_POST['dbuser']) || empty($_POST['dbname']) || empty($_POST['dbprefix'])) {
            $error = true;
        }
        if (!$error) {
            $config = array();
            $config['dbhost'] = Tool::cleanAllow($_POST['dbhost'], '.');
            $config['dbuser'] = Tool::cleanAllow($_POST['dbuser'], '.');
            $config['dbpass'] = Tool::cleanAllow($_POST['dbpass'], '.');
            $config['dbname'] = Tool::cleanAllow($_POST['dbname'], '.');
            $config['dbprefix'] = Tool::cleanAllow($_POST['dbprefix'], '.');
            $config['dbport'] = (!empty($_POST['dbport'])) ? (int) $_POST['dbport'] : 3306;
            switch ($_POST['type_sess']) {
                case 'file';
                    $config['type_sess'] = 'file';
                    break;
                case 'base';
                    $config['type_sess'] = 'base';
                    break;
                default:
                case 'auto';
                    $config['type_sess'] = SX::checkSession() ? 'base' : 'file';
                    break;
            }
            SX::getCreateBase($config);

            if (!SX::getConnect($config)) {
                $error = true;
            } else {
                $fp = fopen(SX_DIR . '/config/db.config.php', 'w+');
                fwrite($fp, "<?php
########################################################################
# ******************  SX CONTENT MANAGEMENT SYSTEM  ****************** #
# *       Copyright © Alexander Voloshin * All Rights Reserved       * #
# ******************************************************************** #
# *  http://sx-cms.ru   *  cms@sx-cms.ru  *   http://www.status-x.ru * #
# ******************************************************************** #
########################################################################
if (!defined('SX_DIR')) {
    header('Refresh: 0; url=/index.php?p=notfound', true, 404); exit;
}
\$config['dbhost']   = '" . $config['dbhost'] . "'; // Адрес хоста базы MySQL
\$config['dbport']   = '" . $config['dbport'] . "'; // Порт базы MySQL
\$config['dbuser']   = '" . $config['dbuser'] . "'; // Пользователь базы MySQL
\$config['dbpass']   = '" . $config['dbpass'] . "'; // Пароль базы MySQL
\$config['dbname']   = '" . $config['dbname'] . "'; // Название базы MySQL
\$config['dbprefix'] = '" . $config['dbprefix'] . "'; // Префикс базы MySQL
\$config['dbcharset']  = 'utf8'; // Кодировка базы MySQL
\$config['dbsesslife'] = '7200'; // Время хранения сессии в секундах в базе MySQL
\$config['type_sess']  = '" . $config['type_sess'] . "'; // Способ хранения сессий, в базе - base, на сервере - file
");
            }
        }

        if ($error) {
            View::get()->assign('db_no_connection', 1);
            View::get()->assign('title', SX::$lang['NameSite'] . ' - ' . SX::$lang['Step1']);
            View::get()->assign('content', View::get()->fetch(THEME . '/step2.tpl'));
        } else {
            SX::set('database', SX::getConfig('db.config'));
            SX::getCheckInstall();

            $do = explode('__SX_CMS__', Structure::getData());
            foreach ($do as $dbin) {
                if (!empty($dbin)) {
                    $dbin = str_replace('sx_', PREFIX . '_', trim($dbin));
                    $dbin = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $dbin);
                    DB::get()->query($dbin);
                }
            }
            View::get()->assign('title', SX::$lang['NameSite'] . ' - ' . SX::$lang['Step2']);
            View::get()->assign('content', View::get()->fetch(THEME . '/step3.tpl'));
        }
        View::get()->display(THEME . '/main.tpl');
    }

    /* Метод */
    public static function getStep4() {
        SX::getCheckInstall();
        $do = explode('__SX_CMS__', Tables::getData());
        foreach ($do as $dbin) {
            if (!empty($dbin)) {
                $dbin = str_replace('sx_', PREFIX . '_', trim($dbin));
                $dbin = str_replace('__FIRST__', DB::get()->escape($_POST['first']), $dbin);
                $dbin = str_replace('__NAME__', DB::get()->escape($_POST['first']) . ' ' . DB::get()->escape($_POST['last']), $dbin);
                $dbin = str_replace('__LAST__', DB::get()->escape($_POST['last']), $dbin);
                $dbin = str_replace('__USERNAME__', DB::get()->escape($_POST['username']), $dbin);
                $dbin = str_replace('__PASS__', md5(md5(preg_replace('/[^\w-]/iu', '', $_POST['pass']))), $dbin);
                $dbin = str_replace('__MAIL__', DB::get()->escape($_POST['email']), $dbin);
                $dbin = str_replace('__PHONE__', DB::get()->escape($_POST['phone']), $dbin);
                $dbin = str_replace('__FAX__', DB::get()->escape($_POST['fax']), $dbin);
                $dbin = str_replace('__WEBSITENAME__', DB::get()->escape($_POST['websitename']), $dbin);
                $dbin = str_replace('__COMPANY__', DB::get()->escape($_POST['company']), $dbin);
                $dbin = str_replace('__TOWN__', DB::get()->escape($_POST['town']), $dbin);
                $dbin = str_replace('__ZIP__', DB::get()->escape($_POST['zip']), $dbin);
                $dbin = str_replace('__STREET__', DB::get()->escape($_POST['street']), $dbin);
                $dbin = str_replace('__TIME__', mktime(0, 0, 01, date('m'), date('d'), date('Y')), $dbin);
                DB::get()->query($dbin);
            }
        }

        setcookie('login_email', $_POST['email'], time() + (3600 * 24 * 365), BASE_PATH);
        setcookie('login_pass', md5(md5(preg_replace('/[^\w-]/iu', '', $_POST['pass']))), time() + (3600 * 24 * 365), BASE_PATH);
        View::get()->assign('title', SX::$lang['NameSite']);
        View::get()->assign('content', View::get()->fetch(THEME . '/final.tpl'));
        View::get()->display(THEME . '/main.tpl');
        self::getCreateHtaccess();
    }

    /* Метод создает корневой хтагес */
    protected static function getCreateHtaccess() {
        $tpl = 'AddDefaultCharset UTF-8' . PE;
        $tpl .= 'DirectoryIndex index.php' . PE;
        $tpl .= 'Options -Indexes +FollowSymLinks' . PE;
        $tpl .= 'ErrorDocument 404 ' . BASE_PATH . 'index.php?p=notfound' . PE;
        $tpl .= 'ErrorDocument 403 ' . BASE_PATH . PE;
        $tpl .= 'ErrorDocument 401 ' . BASE_PATH . PE . PE;

        if (Tool::apacheModul('mod_expires')) {
            $tpl .= 'FileETag MTime Size' . PE;
            $tpl .= '<IfModule mod_expires.c>' . PE;
            $tpl .= '  ExpiresActive on' . PE;
            $tpl .= '  ExpiresByType image/gif A2592000' . PE;
            $tpl .= '  ExpiresByType image/jpeg A2592000' . PE;
            $tpl .= '  ExpiresByType image/png A2592000' . PE;
            $tpl .= '  ExpiresByType image/x-icon A2592000' . PE;
            $tpl .= '  ExpiresByType text/css A2592000' . PE;
            $tpl .= '  ExpiresByType text/x-js A2592000' . PE;
            $tpl .= '  ExpiresByType text/javascript A2592000' . PE;
            $tpl .= '  ExpiresByType application/javascript A2592000' . PE;
            $tpl .= '  ExpiresByType application/x-javascript A2592000' . PE;
            $tpl .= '  ExpiresByType application/x-shockwave-flash A2592000' . PE;
            $tpl .= '</IfModule>' . PE . PE;
        }

        if (Tool::apacheModul('mod_headers')) {
            $tpl .= '<IfModule mod_headers.c>' . PE;
            $tpl .= '  <FilesMatch "\.(gif|jpg|jpeg|png|ico|flv|swf)$">' . PE;
            $tpl .= '    Header set Cache-Control "max-age=2592000"' . PE;
            $tpl .= '  </FilesMatch>' . PE;
            $tpl .= '  <FilesMatch "\.(js|css|pdf|txt)$">' . PE;
            $tpl .= '    Header set Cache-Control "max-age=604800"' . PE;
            $tpl .= '  </FilesMatch>' . PE;
            $tpl .= '  <FilesMatch "\.(html|htm)$">' . PE;
            $tpl .= '    Header set Cache-Control "max-age=600"' . PE;
            $tpl .= '  </FilesMatch>' . PE;
            $tpl .= '  <FilesMatch "\.(php)$">' . PE;
            $tpl .= '    Header unset Cache-Control' . PE;
            $tpl .= '    Header unset Expires' . PE;
            $tpl .= '    Header unset Last-Modified' . PE;
            $tpl .= '    FileETag None' . PE;
            $tpl .= '    Header unset Pragma' . PE;
            $tpl .= '  </FilesMatch>' . PE;
            $tpl .= '</IfModule>' . PE . PE;
        }

        if (Tool::apacheModul('mod_rewrite')) {
            $tpl .= '<IfModule mod_rewrite.c>' . PE;
            $tpl .= 'RewriteEngine on' . PE;
            $tpl .= 'RewriteBase ' . BASE_PATH . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^HTTPClient [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^Drip [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^EirGrabber [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^ExtractorPro [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^EyeNetIE [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^FlashGet [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^GetRight [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^Gets [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^Go!Zilla [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^Go-Ahead-Got-It [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^Grafula [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^IBrowse [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^InterGET [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^JetCar [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^JustView [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^NearSite [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^NetSpider [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^Offline\ Explorer [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^PageGrabber [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^Pockey [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^ReGet [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^Slurp [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^SpaceBison [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^SuperHTTP [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^Teleport [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^WebAuto [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^WebCopier [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^WebFetch [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^WebReaper [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^WebSauger [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^WebStripper [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^WebWhacker [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^WebZIP [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^Webster [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^Wget [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^eCatch [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^ia_archiver [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^libwww-perl [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^libwwwperl [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^httplib [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^httpfetcher [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^httpscraper [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^hloader [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^curl [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^Python [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^PHP [OR]' . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^Perl' . PE;
            $tpl .= 'RewriteRule ^.* - [F]' . PE;
            $tpl .= '</IfModule>';
        }

        $fp = fopen(SX_DIR . '/.htaccess', 'wb+');
        fwrite($fp, $tpl);
        fclose($fp);
        chmod(SX_DIR . '/.htaccess', 0644);
    }

}
