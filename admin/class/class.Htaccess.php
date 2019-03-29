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

class Htaccess extends Magic {

    protected $_options = array();

    public function __construct() {
        $this->_options = SX::get('htaccess');
    }

    /* Метод инициализации записи реврайта */
    public function get($sql_data) {
        if (!is_writable(SX_DIR . '/.htaccess')) {
            chmod(SX_DIR . '/.htaccess', 0777);
        }
        if (!is_writable(SX_DIR . '/.htaccess')) {
            SX::output('<pre><strong>Ошибка:</strong> В файл <i>.htaccess</i> не удается произвести запись, установите соответствующие права!</pre>');
        } else {
            $this->load($sql_data);
        }
    }

    /* Метод записи правил реврайта */
    protected function load($sql_data) {
        if ($this->_options['rewrite'] == 1) {
            $rew = array();
            $load = SX::get('modules');
            foreach (SX::get('langs') as $lang) {
                if (is_file(LANG_DIR . '/' . $lang . '/rewrite.txt')) {
                    $this->_view->configLoad(LANG_DIR . '/' . $lang . '/rewrite.txt');

                    // Загрузка ленгов реврайта активных внешних модулей
                    foreach ($load as $modul) {
                        if (is_file(MODUL_DIR . '/' . $modul . '/lang/' . $lang . '/rewrite.txt')) {
                            $this->_view->configLoad(MODUL_DIR . '/' . $modul . '/lang/' . $lang . '/rewrite.txt');
                        }
                    }
                    $arr = $this->_view->getConfigVars();

                    // Загрузка файлов с реплейзами внешних модулей
                    foreach ($load as $modul) {
                        if (is_file(MODUL_DIR . '/' . $modul . '/main/htaccess.php')) {
                            include MODUL_DIR . '/' . $modul . '/main/htaccess.php';
                        }
                    }

                    // Реплейзы встроенных модулей
                    if (get_active('forums')) {
                        $rew[] = 'RewriteRule ^' . $arr['forumshelp'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=forum&action=help&hid=$1&sub=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['forumshelp'] . '/([^/]*)$ index.php?p=forum&action=help [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['forumpage'] . '/([0-9]+)/(.*)/([_a-zA-Z]+)/([_a-zA-Z]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=showforum&fid=$1&period=$2&sortby=$3&sort=$4&pp=$5&page=$6 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['forumpage'] . '/([0-9]*)/([-_A-Za-z0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=showforum&fid=$1&sortby=$2&sort=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['postprint'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=showtopic&print_post=$1&toid=$2&t=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['topic'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=showtopic&toid=$1&fid=$2&page=$3&t=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['topic'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=showtopic&toid=$1&fid=$2&t=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['topic'] . '/([0-9]+)/([^/]*)$ index.php?p=showtopic&toid=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['newforum'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=showtopic&toid=$1&pp=$2&page=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['forums'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=showforums&cid=$1&t=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['forum'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=showforum&fid=$1&t=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['last24'] . '/([^/]*)$ index.php?p=forum&action=show&unit=h&period=24 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['search_mask'] . '/([0-9]+)/([^/]*)$ index.php?p=forum&action=search_mask&fid=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['search_mask'] . '/([^/]*)$ index.php?p=forum&action=search_mask [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['userforum'] . '/' . $arr['delpost'] . '/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=forums&action=delpost&pid=$1&toid=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['userforum'] . '/' . $arr['movepost'] . '/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=forums&action=movepost&pid=$1&fid=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['userforum'] . '/' . $arr['newtopic'] . '/([0-9]+)/([^/]*)$ index.php?p=forums&action=newtopic&fid=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['userforum'] . '/' . $arr['complaint'] . '/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=forums&action=complaint&fid=$1&pid=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['userforum'] . '/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=forums&action=$1&pid=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['forumsemptytopics'] . '/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=forum&action=print&what=topicsempty&page=$1&pp=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['forumsemptytopics'] . '/([^/]*)$ index.php?p=forum&action=print&what=topicsempty [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['userposting'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=forum&action=print&what=posting&id=$1&page=$2&pp=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['userposting'] . '/([0-9]+)/([^/]*)$ index.php?p=forum&action=print&what=posting&id=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['subscription'] . '/([0-9]+)/([^/]*)$ index.php?p=forum&action=print&what=subscription&id=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['subscriptions'] . '/([^/]*)$ index.php?p=forum&action=print&what=subscription [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['forumslastposts'] . '/([^/]*)$ index.php?p=forum&action=print&what=lastposts [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['markread'] . '/([^/]*)$ index.php?p=forum&action=markread&what=forum&ReadAll=1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['markread'] . '/([0-9]+)/([^/]*)$ index.php?p=forum&action=markread&what=forum&id=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['related'] . '/([0-9]+)/([^/]*)$ index.php?p=forum&action=related&t_id=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['friendsend'] . '/([0-9]+)/([^/]*)$ index.php?p=forum&action=friendsend&t_id=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['addsubscription'] . '/([0-9]+)/([^/]*)$ index.php?p=forum&action=addsubscription&t_id=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['unsubscription'] . '/([0-9]+)/([^/]*)$ index.php?p=forum&action=unsubscription&t_id=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['editpost'] . '/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=newpost&action=edit&pid=$1&toid=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['newpost'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=newpost&toid=$1&pp=$2&num_pages=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['newquote'] . '/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=newpost&action=quote&pid=$1&toid=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['newpost'] . '/([0-9]+)/([^/]*)$ index.php?p=newpost&toid=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['getfile'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=forum&action=getfile&id=$1&f_id=$2&t_id=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['getimage'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=forum&action=getimage&id=$1&f_id=$2&t_id=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['forums'] . '/([^/]*)$ index.php?p=showforums [NC,L]';
                    }

                    $rew[] = 'RewriteRule ^' . $arr['users'] . '/([-_A-Za-z0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=members&ud=$1&selby=$2&pp=$3&page=$4 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['users'] . '/([-_A-Za-z0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=members&ud=$1&pp=$2&page=$3 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['users'] . '/([0-9]+)/([^/]*)$ index.php?p=members&area=$1 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['users'] . '/([^/]*)$ index.php?p=members [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['userprofile'] . '/([0-9]+)/([0-9]+)/all/([^/]*)$ index.php?p=user&id=$1&area=$2&friends=all#friends [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['userprofile'] . '/([_A-Za-z0-9-]+)/([_A-Za-z0-9-]+)/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=user&action=$1&do=$2&id=$3&area=$4&image=$5 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['userprofile'] . '/([_A-Za-z0-9-]+)/([_A-Za-z0-9-]+)/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=user&action=$1&do=$2&id=$3&area=$4&page=$5 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['userprofile'] . '/([_A-Za-z0-9-]+)/([_A-Za-z0-9-]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=user&action=$1&do=$2&id=$3&area=$4 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['userprofile'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=user&id=$1&area=$2&page=$3 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['userprofile'] . '/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=user&id=$1&area=$2 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['userprofile'] . '/([0-9]+)/([^/]*)$ index.php?p=user&id=$1 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['editprofile'] . '/([^/]*)$ index.php?p=useraction&action=profile [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['deleteaccount'] . '/([^/]*)$ index.php?p=useraction&action=deleteaccount [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['changepass'] . '/([^/]*)$ index.php?p=useraction&action=changepass [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['ignore'] . '/([add|del]*)/([0-9]+)/([^/]*)$ index.php?p=forum&action=ignorelist&sub=$1&id=$2 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['ignorelist'] . '/([^/]*)$ index.php?p=forum&action=ignorelist [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['ajaxlogin'] . '/([^/]*)$ index.php?p=userlogin&action=ajaxlogin [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['userlogin'] . '/([^/]*)$ index.php?p=userlogin [NC,L]';

                    if (get_active('pn')) {
                        $rew[] = 'RewriteRule ^' . $arr['pn'] . '/' . $arr['inbox'] . '/([0-9]+)/([^/]*)$ index.php?p=pn&action=message&id=$1&goto=inbox [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['pn'] . '/' . $arr['outbox'] . '/([0-9]+)/([^/]*)$ index.php?p=pn&action=message&id=$1&goto=outbox [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['pn'] . '/' . $arr['inbox'] . '/([^/]*)$ index.php?p=pn&goto=inbox [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['pn'] . '/' . $arr['outbox'] . '/([^/]*)$ index.php?p=pn&goto=outbox [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['pn'] . '/' . $arr['new'] . '/(.*)/([^/]*)$ index.php?p=pn&action=new&to=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['pn'] . '/' . $arr['new'] . '/([^/]*)$ index.php?p=pn&action=new [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['pn'] . '/([^/]*)$ index.php?p=pn [NC,L]';
                    }

                    if (get_active('calendar')) {
                        $rew[] = 'RewriteRule ^' . $arr['calendar'] . '/' . $arr['calendar_events'] . '/([public|private]+)/([0-9]+)/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=calendar&action=events&show=$1&month=$2&year=$3&day=$4&area=$5 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['calendar'] . '/' . $arr['birthdays'] . '/([public|private]+)/([0-9]+)/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=calendar&action=birthdays&show=$1&month=$2&year=$3&day=$4&area=$5 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['calendar'] . '/' . $arr['calendar_week'] . '/([public|private]+)/([-0-9]+)/([-0-9]+)/([0-9]+)/([^/]*)$ index.php?p=calendar&show=$1&action=week&weekstart=$2&weekend=$3&area=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['calendar'] . '/' . $arr['calendar_year'] . '/([0-9]+)/([public|private]+)/([0-9]+)/([^/]*)$ index.php?p=calendar&area=$1&action=displayyear&show=$2&year=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['calendar'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([public|private]+)/([^/]*)$ index.php?p=calendar&month=$1&year=$2&area=$3&show=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['calendar'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=calendar&month=$1&year=$2&area=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['calendar'] . '/([-_.A-Za-z0-9-]+)/([-_.A-Za-z0-9-]+)/([0-9]+)/([0-9]+)/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=calendar&action=$1&show=$2&month=$3&year=$4&day=$5&id=$6&area=$7 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['calendar'] . '/' . $arr['calendar_newevent'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([0-9]+)/([public|private]+)/([^/]*)$ index.php?p=calendar&action=newevent&day=$1&month=$2&year=$3&area=$4&show=$5 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['calendar'] . '/' . $arr['calendar_newevent'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=calendar&area=$1&action=newevent&month=$2&year=$3&area=$4&show=$5 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['calendar'] . '/' . $arr['calendar_myevents'] . '/([0-9]+)/([^/]*)$ index.php?p=calendar&area=$1&action=myevents [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['calendar'] . '/([0-9]+)/([^/]*)$ index.php?p=calendar&area=$1 [NC,L]';
                    }

                    if (get_active('shop')) {
                        $rew[] = 'RewriteRule ^(.*)/([0-9]+)/([0-9]+)/([.0-9]+)/([.0-9]+)/([-_.A-Za-z0-9-]+)/([0-9]+)/([0-9]+)/([^/]*)$ $1&page=$2&limit=$3&pf=$4&pt=$5&list=$6&s=$7&avail=$8 [NC]';
                        $rew[] = 'RewriteRule ^(.*)/([0-9]+)/([0-9]+)/([.0-9]+)/([.0-9]+)/' . $arr['shop_topseller_products'] . '/([^/]*)$ $1&page=$2&limit=$3&pf=$4&pt=$5&topseller=6 [NC]';
                        $rew[] = 'RewriteRule ^(.*)/([0-9]+)/([0-9]+)/([.0-9]+)/([.0-9]+)/' . $arr['shop_lowamount_products'] . '/([^/]*)$ $1&page=$2&limit=$3&pf=$4&pt=$5&lowamount=1 [NC]';
                        $rew[] = 'RewriteRule ^(.*)/([0-9]+)/([0-9]+)/([.0-9]+)/([.0-9]+)/' . $arr['shop_offer_products'] . '/([^/]*)$ $1&page=$2&limit=$3&pf=$4&pt=$5&offers=1 [NC]';
                        $rew[] = 'RewriteRule ^(.*)/([0-9]+)/([0-9]+)/([.0-9]+)/([.0-9]+)/([-_.A-Za-z0-9-]+)/' . $arr['shop_topseller_products'] . '/([^/]*)$ $1&page=$2&limit=$3&pf=$4&pt=$5&list=$6&topseller=1 [NC]';
                        $rew[] = 'RewriteRule ^(.*)/([0-9]+)/([0-9]+)/([.0-9]+)/([.0-9]+)/([-_.A-Za-z0-9-]+)/' . $arr['shop_lowamount_products'] . '/([^/]*)$ $1&page=$2&limit=$3&pf=$4&pt=$5&list=$6&lowamount=1 [NC]';
                        $rew[] = 'RewriteRule ^(.*)/([0-9]+)/([0-9]+)/([.0-9]+)/([.0-9]+)/([-_.A-Za-z0-9-]+)/' . $arr['shop_offer_products'] . '/([^/]*)$ $1&page=$2&limit=$3&pf=$4&pt=$5&list=$6&offers=1 [NC]';
                        $rew[] = 'RewriteRule ^(.*)/([0-9]+)/([0-9]+)/([.0-9]+)/([.0-9]+)/([-_.A-Za-z0-9-]+)/([0-9]+)/([^/]*)$ $1&page=$2&limit=$3&pf=$4&pt=$5&list=$6&s=$7 [NC]';
                        $rew[] = 'RewriteRule ^(.*)/([0-9]+)/([0-9]+)/([.0-9]+)/([.0-9]+)/([-_.A-Za-z0-9-]+)/([^/]*)$ $1&page=$2&limit=$3&pf=$4&pt=$5&list=$6 [NC]';
                        $rew[] = 'RewriteRule ^(.*)/([0-9]+)/([0-9]+)/([.0-9]+)/([.0-9]+)/([^/]*)$1&page=$2&limit=$3&pf=$4&pt=$5 [NC]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/' . $arr['search'] . '/([^/]+)/([0-9]+)/([^/]*)$ index.php?shop_q=$1&man=$2&p=shop&action=showproducts&cid=$3 [NC,L]';

                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_lowamount_products'] . '/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&lowamount=1&cid=$2&list=$3&limit=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_offer_products'] . '/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&offers=1&cid=$2&list=$3&limit=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_topseller_products'] . '/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&topseller=1&cid=$2&list=$3&limit=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&cid=$1&page=$2&limit=$3&t=$4&list=$5 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&cid=$1&page=$2&limit=$3&t=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_product'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=shop&area=$1&action=showproduct&id=$2&cid=$3&pname=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_product'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/1/([^/]*)$ index.php?p=shop&action=showproduct&id=$1&cid=$2&pname=$3&blanc=1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_product'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=shop&action=showproduct&id=$1&cid=$2&pname=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_product'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=shop&action=showproduct&id=$1&cid=$2&pname=$3&artpage=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^([0-9]+)/([0-9]+)/([0-9]+)/' . $arr['shop'] . '/' . $arr['search'] . '/([^/]*)$ index.php?exts=$1&s=$2&area=$3&p=shop&action=showproducts [NC,L]';
                        $rew[] = 'RewriteRule ^([0-9]+)/([0-9]+)/' . $arr['shop'] . '/' . $arr['search'] . '/([^/]*)$ index.php?s=$2&area=$3&p=shop&action=showproducts [NC,L]';
                        $rew[] = 'RewriteRule ^([-_A-Za-z0-9]+)/([0-9]+)/' . $arr['shop'] . '/([^/]*)$ index.php?p=shop&area=$2&start=1&name=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/start/([^/]*)$ index.php?p=shop&start=1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shippingcost'] . '/([^/]*)$ index.php?p=shop&action=shippingcost [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['showbasket'] . '/([^/]*)$ index.php?p=shop&action=showbasket [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['showsavedbaskets'] . '/([^/]*)$ index.php?p=shop&action=showsavedbaskets [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['mylist'] . '/del/([0-9]+)/([^/]*)$ index.php?p=shop&action=mylist&subaction=del_list&id=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['mylist'] . '/([0-9]+)/([^/]*)$ index.php?p=shop&action=mylist&subaction=load_list&id=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['mylist'] . '/([^/]*)$ index.php?p=shop&action=mylist [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['mydownloads'] . '/([^/]*)$ index.php?p=shop&action=mydownloads [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['myorders'] . '/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=shop&action=myorders&show=$1&page=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['myorders'] . '/-/([0-9]+)/([^/]*)$ index.php?p=shop&action=myorders&page=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['myorders'] . '/([^/]*)$ index.php?p=shop&action=myorders [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['prais'] . '/([0-9]+)/([^/]*)$ index.php?p=shop&action=prais&page=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['prais'] . '/([^/]*)$ index.php?p=shop&action=prais [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shoporder'] . '/([^/]*)$ index.php?p=shop&action=shoporder&step=2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shoporder'] . '/' . $arr['step'] . '/([0-9]+)/([^/]*)$ index.php?p=shop&action=shoporder&subaction=step$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['search'] . '/([0-9]+)/([^/]*)$ index.php?p=shop&s=1&action=showproducts&limit=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['search'] . '/([^/]*)$ index.php?p=shop&s=1&action=showproducts [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['myorders'] . '/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=shop&action=myorders&show=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shoporder'] . '/([^/]*)$ index.php?p=shop&action=shoporder&step=2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_download'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=shop&action=mydownloads&sub=showfile&Id=$1&FileId=$2&getId=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_new_products'] . '/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&cid=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_offer_products'] . '/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&offers=1&cid=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_topseller_products'] . '/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&topseller=1&cid=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_lowamount_products'] . '/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&lowamount=1&cid=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/' . $arr['shop_topseller_products'] . '/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&topseller=1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_new_products'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&cid=$2&limit=$3&list=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_new_products'] . '/([0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&cid=$2&limit=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_offer_products'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&offers=1&cid=$2&limit=$3&list=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_offer_products'] . '/([0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&offers=1&cid=$2&limit=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_topseller_products'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&topseller=1&cid=$2&limit=$3&list=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_topseller_products'] . '/([0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&topseller=1&cid=$2&limit=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_lowamount_products'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&lowamount=1&cid=$2&limit=$3&list=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_lowamount_products'] . '/([0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&lowamount=1&cid=$2&limit=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/' . $arr['shop_offer_products'] . '/([0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&offers=1&cid=0&limit=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/' . $arr['shop_topseller_products'] . '/([0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&topseller=1&limit=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/' . $arr['shop_lowamount_products'] . '/([0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&lowamount=1&limit=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_new_products'] . '/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&cid=$2&list=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_offer_products'] . '/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&offers=1&cid=$2&list=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_topseller_products'] . '/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&topseller=1&cid=$2&list=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/([0-9]+)/' . $arr['shop_lowamount_products'] . '/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&lowamount=1&cid=$2&list=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_products'] . '/([0-9]+)/' . $arr['shop_topseller_products'] . '/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&page=$1&topseller=1&list=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_manufacturer'] . '/([0-9]+)/([^/]*)$ index.php?p=shop&action=showproducts&man=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['showseenproducts'] . '/([0-9]+)/([^/]*)$ index.php?p=shop&area=$1&action=showseenproducts [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['shop_agb'] . '/([^/]*)$ index.php?p=shop&action=agb [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['misc'] . '/' . $arr['shippingcost'] . '/([^/]*)$ index.php?p=misc&do=shippingcost [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['privacy'] . '/([^/]*)$ index.php?p=shop&action=privacy [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/' . $arr['refusal'] . '/([^/]*)$ index.php?p=shop&action=refusal [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/([0-9]+)/([^/]*)$ index.php?p=shop&area=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['shop'] . '/([^/]*)$ index.php?p=shop [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['currency'] . '/([0-9]+)/([^/]*)$ index.php?p=shop&currency=$1 [NC,L]';
                    }

                    if (get_active('gallery')) {
                        $rew[] = 'RewriteRule ^' . $arr['gallery'] . '/' . $arr['gallerydiashow'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=gallery&action=showimage&id=$1&galid=$2&blanc=1&first_id=$3&ascdesc=$4&categ=$5 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['gallery'] . '/' . $arr['galleryimage'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=gallery&action=showimage&id=$1&galid=$2&ascdesc=$3&categ=$4&area=$5&page=$6 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['gallery'] . '/' . $arr['galleryimage'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=gallery&action=showimage&id=$1&galid=$2&ascdesc=$3&categ=$4&area=$5 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['gallery'] . '/([^/]+)/([-_A-Za-z0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=gallery&q=$1&searchtype=$2&ascdesc=$3&page=$4&area=$5 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['gallery'] . '/' . $arr['galleryimages'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([0-9]+)/' . $arr['galleryfavs'] . '/([0-9]+)/([^/]*)$ index.php?p=gallery&action=showgallery&id=$1&categ=$2&name=$3&ascdesc=$4&pp=$5&page=$6&favorites=1&area=$7 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['gallery'] . '/' . $arr['galleryimages'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=gallery&action=showgallery&id=$1&categ=$2&name=$3&ascdesc=$4&pp=$5&page=$6&area=$7 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['gallery'] . '/' . $arr['galleryimages'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/' . $arr['galleryfavs'] . '/([0-9]+)/([^/]*)$ index.php?p=gallery&action=showgallery&id=$1&categ=$2&name=$3&favorites=1&area=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['gallery'] . '/' . $arr['galleryimages'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=gallery&action=showgallery&id=$1&categ=$2&name=$3&area=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['gallery'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([^/]+)/([-_A-Za-z0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=gallery&action=showincluded&categ=$1&name=$2&q=$3&searchtype=$4&page=$5&sort=$6&area=$7 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['gallery'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=gallery&action=showincluded&categ=$1&name=$2&area=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['gallery'] . '/([0-9]+)/([^/]*)$ index.php?p=gallery&area=$1 [NC,L]';
                    }

                    if (get_active('poll')) {
                        $rew[] = 'RewriteRule ^' . $arr['poll'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=poll&id=$1&name=$2&page=$3&area=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['poll'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=poll&id=$1&name=$2&area=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['pollarchive'] . '/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=poll&action=archive&page=$1&area=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['pollarchive'] . '/([0-9]+)/([^/]*)$ index.php?p=poll&action=archive&area=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['poll'] . '/([0-9]+)/([^/]*)$ index.php?p=poll&area=$1 [NC,L]';
                    }

                    if (get_active('News')) {
                        $rew[] = 'RewriteRule ^' . $arr['news'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=news&area=$1&newsid=$2&name=$3&artpage=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['news'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=news&newsid=$1&name=$2&page=$3&area=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['news'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=news&area=$1&newsid=$2&name=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['newsarchive'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=newsarchive&area=$1&catid=$2&name=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['newsarchive'] . '/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=newsarchive&area=$1&catid=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['newsrss'] . '/([0-9]+)/([0-9]+)/([^/]*)$ index.php?area=$1&p=newsarchive&catid=$2&t=1&mode=rss [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['newsrss'] . '/([0-9]+)/([^/]*)$ index.php?p=newsarchive&area=$1&mode=rss [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['newsarchive'] . '/([0-9]+)/([^/]*)$ index.php?p=newsarchive&area=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['newsarchive'] . '/([^/]*)$ index.php?p=newsarchive [NC,L]';
                    }

                    if (get_active('newsletter')) {
                        $rew[] = 'RewriteRule ^' . $arr['newsletter'] . '/([0-9]+)/([^/]*)$ index.php?p=newsletter&area=$1 [NC,L]';
                    }

                    if (get_active('articles')) {
                        $rew[] = 'RewriteRule ^' . $arr['articles_archive'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([^/]+)/([0-9]+)/([^/]*)$ index.php?p=articles&area=$1&catid=$2&page=$3&q_news=$4&limit=$5 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['articles_archive'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=articles&area=$1&catid=$2&page=$3&limit=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['articles_archive'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=articles&area=$1&catid=$2&name=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['articles'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=articles&area=$1&action=displayarticle&id=$2&name=$3&artpage=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['articles'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=articles&area=$1&action=displayarticle&id=$2&name=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['articles'] . '/' . $arr['articles_previews'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=articles&area=$1&type=previews&catid=$2&name=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['articles'] . '/' . $arr['articles_reviews'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=articles&area=$1&type=reviews&catid=$2&name=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['articles'] . '/' . $arr['articles_specials'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$  index.php?p=articles&area=$1&type=specials&catid=$2&name=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['articles'] . '/' . $arr['articles_previews'] . '/([0-9]+)/([^/]*)$ index.php?p=articles&area=$1&type=previews [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['articles'] . '/' . $arr['articles_reviews'] . '/([0-9]+)/([^/]*)$ index.php?p=articles&area=$1&type=reviews [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['articles'] . '/' . $arr['articles_specials'] . '/([0-9]+)/([^/]*)$ index.php?p=articles&area=$1&type=specials [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['articles_rss'] . '/([0-9]+)/([^/]*)$ index.php?p=articles&area=$1&mode=rss [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['articles'] . '/([0-9]+)/([^/]*)$ index.php?p=articles&area=$1 [NC,L]';
                    }

                    if (get_active('links')) {
                        $rew[] = 'RewriteRule ^' . $arr['links'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=links&action=showdetails&categ=$1&id=$2&name=$3&page=$4&area=$5 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['links'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=links&action=showdetails&area=$1&categ=$2&id=$3&name&name=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['links'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=links&area=$1&categ=$2&name=$3&page=$4&sort=$5 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['links'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=links&area=$1&categ=$2&name=$3&page=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['links'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=links&area=$1&categ=$2&name=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['links'] . '/' . $arr['links_search'] . '/([^/]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?ql=$1&action=search&p=links&area=$2&page=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['links'] . '/' . $arr['links_search'] . '/([0-9]+)/([^/]*)$ index.php?p=links&action=search&area=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['links'] . '/' . $arr['links_sent'] . '/([0-9]+)/([^/]*)$ index.php?p=links&action=links_sent&area=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['links'] . '/([0-9]+)/([^/]*)$ index.php?p=links&area=$1 [NC,L]';
                    }

                    if (get_active('downloads')) {
                        $rew[] = 'RewriteRule ^' . $arr['downloads'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=downloads&action=showdetails&categ=$1&id=$2&name=$3&page=$4&area=$5 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['downloads'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=downloads&action=showdetails&area=$1&categ=$2&id=$3&name&name=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['downloads'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=downloads&area=$1&categ=$2&name=$3&page=$4&sort=$5 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['downloads'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=downloads&area=$1&categ=$2&name=$3&page=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['downloads'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=downloads&area=$1&categ=$2&name=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['downloads'] . '/' . $arr['downloads_search'] . '/([^/]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?ql=$1&action=search&p=downloads&area=$2&page=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['downloads'] . '/' . $arr['downloads_search'] . '/([0-9]+)/([^/]*)$ index.php?p=downloads&action=search&area=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['downloads'] . '/([0-9]+)/([^/]*)$ index.php?p=downloads&area=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['downloads_getfile'] . '/([0-9]+)/([^/]*)$ index.php?p=downloads&action=getfile&id=$1 [NC,L]';
                    }

                    if (get_active('cheats')) {
                        $rew[] = 'RewriteRule ^' . $arr['cheats'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=cheats&action=showcheat&area=$1&plattform=$2&id=$3&name=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['cheats'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/' . $arr['page'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=cheats&area=$1&plattform=$2&name=$3&page=$4&sort=$5 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['cheats'] . '/' . $arr['cheats_search'] . '/([^/]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?ql=$1&action=search&p=cheats&area=$2&page=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['cheats'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=cheats&area=$1&plattform=$2&name=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['cheats'] . '/' . $arr['cheats_search'] . '/([0-9]+)/([^/]*)$ index.php?p=cheats&action=search&area=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['cheats'] . '/([0-9]+)/([^/]*)$ index.php?p=cheats&area=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['cheats_getfile'] . '/([0-9]+)/([^/]*)$ index.php?p=cheats&action=getfile&id=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['cheats'] . '/([^/]*)$ index.php?p=cheats [NC,L]';
                    }

                    if (get_active('products')) {
                        $rew[] = 'RewriteRule ^' . $arr['products'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/' . $arr['page'] . '/([0-9]+)/([^/]*)$ index.php?p=products&area=$1&action=showproduct&id=$2&name=$3&artpage=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['products'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=products&area=$1&action=showproduct&id=$2&name=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['products'] . '/' . $arr['page'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=products&area=$1&page=$2&sort=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['products'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]+)/([^/]*)$ index.php?p=products&area=$1&page=$2&sort=$3&q=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['products'] . '/([0-9]+)/([^/]*)$ index.php?p=products&area=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['products'] . '/([^/]*)$ index.php?p=products [NC,L]';
                    }

                    if (get_active('manufacturer')) {
                        $rew[] = 'RewriteRule ^' . $arr['manufacturer'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=manufacturer&area=$1&action=showdetails&id=$2&name=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['manufacturer'] . '/' . $arr['page'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=manufacturer&area=$1&page=$2&sort=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['manufacturer'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]+)/([^/]*)$ index.php?p=manufacturer&area=$1&page=$2&sort=$3&q=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['manufacturer'] . '/([0-9]+)/([^/]*)$ index.php?p=manufacturer&area=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['manufacturer'] . '/([^/]*)$ index.php?p=manufacturer [NC,L]';
                    }

                    if (get_active('guestbook')) {
                        $rew[] = 'RewriteRule ^' . $arr['guestbook'] . '/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=guestbook&page=$2&area=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['guestbook'] . '/([0-9]+)/([^/]*)$ index.php?p=guestbook&area=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['guestbook'] . '/([^/]*)$ index.php?p=guestbook [NC,L]';
                    }

                    if (get_active('faq')) {
                        $rew[] = 'RewriteRule ^' . $arr['faq'] . '/' . $arr['show'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=faq&action=faq&fid=$1&area=$2&name=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['faq'] . '/' . $arr['quest'] . '/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=faq&action=mail&faq_id=$1&area=$2 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['faq'] . '/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=faq&action=display&faq_id=$1&area=$2&name=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['faq'] . '/([0-9]+)/([^/]*)$ index.php?p=faq&area=$1 [NC,L]';
                    }

                    if (get_active('roadmap')) {
                        $rew[] = 'RewriteRule ^' . $arr['roadmap'] . '/([0-9]+)/([^/]*)$ index.php?p=roadmap&area=$1 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['roadmap'] . '/' . $arr['roadmap_etap'] . '/([0-9]+)/([0-9]+)/([0-9]+)/([-_A-Za-z0-9]+)/([^/]*)$ index.php?p=roadmap&action=display&rid=$1&closed=$2&area=$3&name=$4 [NC,L]';
                    }

                    if (get_active('content')) {
                        $rew[] = 'RewriteRule ^' . $arr['content'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/' . $arr['page'] . '/([0-9]+)/([^/]*)$ index.php?p=content&id=$1&name=$2&page=$4&area=$3 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['content'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([0-9]+)/([^/]*)$ index.php?p=content&id=$1&name=$2&area=$3&artpage=$4 [NC,L]';
                        $rew[] = 'RewriteRule ^' . $arr['content'] . '/([0-9]+)/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=content&id=$1&name=$2&area=$3 [NC,L]';
                    }

                    $rew[] = 'RewriteRule ^([^/]+)/([-_A-Za-z0-9]+)/search/([^/]*)$ index.php?q=$1&where=$2&p=search [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['sitemap'] . '/([0-9]+)/([^/]*)$ index.php?p=sitemap&area=$1 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['sitemap'] . '/' . $arr['full'] . '/([0-9]+)/([^/]*)$ index.php?p=sitemap&action=full&area=$1 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['pwlost'] . '/([^/]*)$ index.php?p=pwlost [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['banned'] . '/([^/]*)$ index.php?p=banned [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['userlist'] . '/([^/]*)$ index.php?p=userlist [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['register'] . '/([0-9]+)/([^/]*)$ index.php?p=register&area=$1 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['misc'] . '/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=misc&do=$1&id=$2 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['usercontact'] . '/([-_A-Za-z0-9]+)/([0-9]+)/([^/]*)$ index.php?p=misc&do=$1&uid=$2 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['rss'] . '/([0-9]+)/([A-Za-z0-9-]*)/([news|articles|forum]+).xml([^/]*)$ index.php?p=rss&area=$1&action=$3&charset=$2 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['rss'] . '/([0-9]+)/([news|articles|forum]+).xml([^/]*)$ index.php?p=rss&area=$1&action=$2 [NC,L]';
                    $rew[] = 'RewriteRule ^([0-9]+)/([A-Za-z0-9-]*)/rss.xml([^/]*)$ index.php?p=rss&area=$1&charset=$2 [NC,L]';
                    $rew[] = 'RewriteRule ^([0-9]+)/rss.xml([^/]*)$ index.php?p=rss&area=$1 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['startindex'] . '/([0-9]+)/rss.xml([^/]*)$ index.php?area=$1&mode=rss [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['startindex'] . '/([0-9]+)/([^/]*)$ index.php?area=$1 [NC,L]';
                    $rew[] = 'RewriteRule ^' . $arr['imprint'] . '/([^/]*)$ index.php?p=imprint [NC,L]';
                    $rew[] = 'RewriteRule ^(.*)temp/cache/sx_cms_php_([a-z0-9]+).([css|js]*)$ $1temp/cache/sx_cms_php_$2.php [NC]';
                    unset($arr);
                }
            }
        }
        $tpl = 'DirectoryIndex index.php' . PE;
        $tpl .= 'AddDefaultCharset ' . CHARSET . PE;
        $tpl .= 'Options -Indexes +FollowSymLinks' . PE;
        $tpl .= 'ErrorDocument 404 ' . BASE_PATH . 'index.php?p=notfound' . PE;
        $tpl .= 'ErrorDocument 403 ' . BASE_PATH . 'index.php?p=noperm' . PE;
        $tpl .= 'ErrorDocument 401 ' . BASE_PATH . PE . PE;

        if ($this->_options['expires'] == 1) {
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

        if ($this->_options['headers'] == 1) {
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

        if ($this->_options['rewrite'] == 1) {
            $tpl .= '<IfModule mod_rewrite.c>' . PE;
            $tpl .= 'RewriteEngine on' . PE;
            $tpl .= 'RewriteBase ' . BASE_PATH . PE . PE;
            $tpl .= 'RewriteCond %{HTTP_USER_AGENT} ^.*internal\ dummy\ connection.*$ [NC]' . PE;
            $tpl .= 'RewriteRule .* - [F,L]' . PE . PE;
            $tpl .= 'RewriteCond %{REQUEST_URI} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]' . PE;
            $tpl .= 'RewriteCond %{REQUEST_URI} _REQUEST(=|\[|\%[0-9A-Z]{0,2}) [OR]' . PE;
            $tpl .= 'RewriteCond %{REQUEST_URI} base64_encode.*\(.*\) [NC,OR]' . PE;
            $tpl .= 'RewriteCond %{REQUEST_URI} base64_decode.*\(.*\) [NC,OR]' . PE;
            $tpl .= 'RewriteCond %{REQUEST_METHOD} ^(TRACE|DELETE|TRACK) [NC]' . PE;
            $tpl .= 'RewriteRule .* - [F,L]' . PE;
            $tpl .= PE;
            $tpl .= '<FilesMatch "(\.(htaccess|htpasswd|bak|config|sql|fla|ini|log|sh|inc|swp|dist|svn|tpl)|~)$">' . PE;
            $tpl .= 'Order allow,deny' . PE;
            $tpl .= 'Deny from all' . PE;
            $tpl .= 'Satisfy All' . PE;
            $tpl .= '</FilesMatch>' . PE;
            $tpl .= PE;
            $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
            if ($host != 'localhost') {
                if ($this->_options['www'] != '0') {
                    if ($this->_options['www'] == '1') {
                        $tpl .= 'RewriteCond %{HTTP_HOST} ^' . $host . '$ [NC]' . PE;
                        $tpl .= 'RewriteRule ^(.*)$ ' . SHEME_URL . 'www.' . $host . '/$1 [R=301,L]' . PE . PE;
                    } elseif ($this->_options['www'] == '2') {
                        $tpl .= 'RewriteCond %{HTTP_HOST} ^www.' . $host . '$ [NC]' . PE;
                        $tpl .= 'RewriteRule ^(.*)$ ' . SHEME_URL . $host . '/$1 [R=301,L]' . PE . PE;
                    }
                }
                if ($this->_options['lich'] == '1' && !empty($this->_options['exts'])) {
                    $tpl .= 'RewriteCond %{HTTP_REFERER} !^$' . PE;
                    $tpl .= 'RewriteCond %{HTTP_REFERER} !^http(s)?://(.*)?' . $host . ' [NC]' . PE;
                    $tpl .= 'RewriteCond %{HTTP_REFERER} !^http(s)?://(.*)?google.(com|ru)? [NC]' . PE;
                    $tpl .= 'RewriteCond %{HTTP_REFERER} !^http(s)?://(.*)?yandex.(com|ru)? [NC]' . PE;
                    $tpl .= 'RewriteCond %{HTTP_REFERER} !^http(s)?://(.*)?yahoo.(com|ru)? [NC]' . PE;
                    $tpl .= 'RewriteCond %{HTTP_REFERER} !^http(s)?://(.*)?bing.(com|ru)? [NC]' . PE;
                    $tpl .= 'RewriteRule .*.(' . implode('|', $this->_options['exts']) . ')$ uploads/other/hotlink.png [NC]' . PE . PE;
                }
            }
            $tpl .= 'RewriteCond %{REQUEST_URI} (.*/[^/.&#%]+)($|\?)' . PE;
            $tpl .= 'RewriteRule .* %1/ [R=301,L,QSA]' . PE;
            $tpl .= 'RewriteCond %{REQUEST_URI} (.*)/([^/.]+\.[^/.]+)/($|\?)' . PE;
            $tpl .= 'RewriteRule .* %1/%2 [R=301,L,QSA]' . PE . PE;
            $rew = array_unique($rew);
            foreach ($rew as $r) {
                $tpl .= $r . PE;
            }
            $tpl .= PE;
            $tpl .= '</IfModule>' . PE;
        }

        SX::save('system', array('Seo_Sprachen' => implode(';', $sql_data)));
        File::set(SX_DIR . '/.htaccess', $tpl);
        return true;
    }

}