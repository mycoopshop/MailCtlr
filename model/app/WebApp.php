<?php

session_start();

require_once __BASE__.'/model/app/App.php';
require_once __BASE__.'/lib/futils/futils.php';

class WebApp extends App
{
    ##
    public $acl = [
        'public' => [
            '*' => true,
        ],
    ];

    ##
    public $user = [
        'name'  => 'undefined',
        'role'  => ['public'],
    ];

    ##
    private $menu = [];

    ##

    public function __construct($file, $php_self, $request_uri)
    {
        global $config;

        ##
        parent::__construct($file, $php_self, $request_uri);

        ## override default user with sessionUser
        if ($this->hasSessionUser()) {
            $this->user = $this->getSessionUser();
        }

        ##
        $this->theme = $config['default']['theme'];

        ##
        define('__LOGO__', __URL__.(isset($config['logo']) ? $config['logo'] : '/public/images/logo.png'));
        define('__THEME__', __BASE__.'/theme/'.$this->theme);
    }

    ##

    public function run()
    {
        $this->load();
        $this->tryAccess();
        $this->exec();
    }

    ##

    public function tryAccess()
    {
        $path = $this->request['acl'];

        if (!$this->testAcl($path, $this->acl)) {
            $this->accessDenied($path);
        }
    }

    ##

    public function testAcl($path, $acl = null)
    {
        $acl = is_null($acl) ? $this->acl : $acl;

        return Liberty::testAcl($this->user['role'], $path, $acl);
    }

    ##

    public function accessDenied($acl)
    {
        $name = $this->user['name'];
        $role = implode(',', $this->user['role']);
        $msg = "access denied for '{$name}:{$role}' in '{$acl}'";
        $err = E_USER_ERROR;
        $this->log($msg, $err);
        $this->error($msg, $err);
        exit();
    }

    ##

    public function error($msg, $err)
    {
        $this->render_theme([
            'view' => $msg,
        ]);
    }

    ##

    public function render($data = [], $action = null, $theme = null)
    {
        ## Cast data as object
        $data = (object) $data;

        ## Fetch View/Action output file
        ob_start();
        $this->render_view($data, $action);
        $data->view = ob_get_clean();

        ## Render Theme
        $this->render_theme($data, $theme);
    }

    ##

    public function render_view($data = [], $action = null)
    {

        ## Action/View
        $action = !is_null($action) ? $action : $this->request['action'];
        if (isset($this->request['path'])) {
            $viewFile = $this->request['path'].'/view/'.$this->request['name'].'/'.$action.'.phtml';

            if (!file_exists($viewFile)) {
                $msg = "view file not found: {$viewFile}";
                $err = E_USER_ERROR;
                $this->log($msg, $err);
                $this->error($msg, $err);
                exit();
            }
        } else {
            //$viewFile = $app->modules_path.'/common/view/'.$action.".phtml";
        }

        ## reference $app for theme
        $app = App::getInstance();

        ##
        $data = (object) $data;

        ##
        require_once $viewFile;
    }

    ## low-level render theme function

    public function render_theme($data = [], $theme = null)
    {
        ## Cast $data as object
        $data = (object) $data;
        ## Themes
        $theme = !is_null($theme) ? $theme : $this->theme;
        $theme_file = __BASE__.'/theme/'.$theme.'/index.phtml';
        ## reference $app for theme
        $app = App::getInstance();
        ## Render theme
        require_once $theme_file;
    }

    ## set in runtime the session user

    public function setSessionUser($id, $name, $role, $full_name = '')
    {
        $hash = md5(__FILE__);
        $role = is_array($role) && count($role) > 0 ? $role : ['public'];
        $user = [
            'name'      => $name,
            'full_name' => $full_name,
            'role'      => $role,
            'id'        => $id,
        ];
        $_SESSION[$hash]['user'] = $user;
        $this->user = $user;
    }

    ## get the session user stored

    public function getSessionUser()
    {
        $hash = md5(__FILE__);

        return $_SESSION[$hash]['user'];
    }

    ## test if have a

    public function hasSessionUser()
    {
        $hash = md5(__FILE__);

        return isset($_SESSION[$hash]['user']);
    }

    ##

    ## usefull link page url generator

    public function getUrl($path)
    {
        $url = __URL__.'/'.$path;

        return $url;
    }

    ##

    public function addMenu($slug, $item)
    {

        ##
        if (!isset($this->menu[$slug])) {
            $this->menu[$slug] = [];
        }

        $count = count($this->menu[$slug]);
        ##
        $item['id'] = isset($item['id']) ? $item['id'] : $slug.'-item-'.$count;
        $item['link'] = isset($item['link']) ? $item['link'] : 'javascript:;';
        $item['label'] = isset($item['label']) ? $item['label'] : 'label-'.$item['id'];
        $item['order'] = isset($item['order']) ? $item['order'] : $count;
        $this->menu[$slug][] = $item;
    }

    ## getNavMenu

    public function getMenu($slug)
    {

        ##
        if (!isset($this->menu[$slug])) {
            return [];
        }

        ##
        $menu = $this->menu[$slug];

        ##
        $this->walkingParentMenu($menu, $menu);
        usort($menu, 'WebApp::orderMenu');
        ##
        return $menu;
    }

    private function orderMenu($a, $b)
    {
        if ($a['order'] == $b['order']) {
            return 0;
        }

        return ($a['order'] < $b['order']) ? -1 : 1;
    }

    public function walkingParentMenu(&$menu, &$base)
    {
        foreach ($menu as $k => &$item) {
            if (isset($item['parent'])) {
                $this->appendOnParentMenu($base, $item, $menu, $k);
            } elseif (isset($item['children'])) {
                $this->walkingParentMenu($item['children'], $base);
            }
        }
    }

    ##

    public function appendOnParentMenu(&$menu, &$item, &$base, $k)
    {
        foreach ($menu as &$i) {
            if ($i['id'] == $item['parent']) {
                $i['children'][] = $item;
                unset($base[$k]);

                return true;
            } elseif (isset($i['children'])) {
                if ($this->appendOnParentMenu($i['children'], $item, $base, $k)) {
                    unset($base[$k]);

                    return true;
                }
            }
        }
    }

    public function getUrlParam($paramName)
    {
        ## return  paramenter
        return @$this->request['params'][$paramName];
    }

    public function hook($hook_name, $hook_value, $hook_order = 0)
    {
        if (!isset($this->hooks[$hook_name])) {
            $this->hooks[$hook_name] = [];
        }
        if (!isset($this->hooks[$hook_name][$hook_order])) {
            $this->hooks[$hook_name][$hook_order] = $hook_value;
        } else {
            $this->hooks[$hook_name][] = $hook_value;
        }
    }

    public function render_hook($hook_name)
    {
        if (isset($this->hooks[$hook_name])) {
            $htmls = $this->hooks[$hook_name];
            ksort($htmls);
            foreach ($htmls as $html) {
                echo $html;
            }
        }
    }

    public function appendJs($js)
    {
        $html = '<script src="'.$js.'"></script>'."\n";
        $this->hook('append-js', $html, 0);
    }

    public function prependJs($js)
    {
        $html = '<script src="'.$js.'"></script>'."\n";
        $this->hook('prepend-js', $html, 0);
    }

    public function appendCss($css)
    {
        $html = '<link rel="stylesheet" href="'.$css.'">'."\n";
        $this->hook('append-css', $html, 0);
    }

    public function prependCss($css)
    {
        $html = '<link rel="stylesheet" href="'.$css.'">'."\n";
        $this->hook('prepend-css', $html, 0);
    }

    public static function updateUrlParams($url, $params = [])
    {
        require_once __DIR__.'/Url.php';
        $u = parse_url($url);
        if (isset($u['query'])) {
            parse_str($u['query'], $p);
        } else {
            $p = [];
        }
        foreach ($params as $k => $v) {
            $p[$k] = $v;
        }
        $q = http_build_query($p);
        $n = http_build_url($url, ['query' => $q]);

        return $n;
    }

    public static function getCurrentUrl()
    {
        $pageURL = (@$_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
        if ($_SERVER['SERVER_PORT'] != '80') {
            $pageURL .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
        } else {
            $pageURL .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        }

        return $pageURL;
    }

    public function getAction()
    {
        return $this->Controller_action_name;
    }

    ##

    public function isAction($action)
    {

        ##
        return @$this->request['action'] == $action;
    }

    public function AsyncRun($url, $async = true)
    {
        $uri = $this->getUrl($url);
        $cmd = 'php '.__BASE__."/run.php $this->myself {$uri}";
        if ($async) {
            $cmd .= ' > /dev/null 2>&1 & echo $!';
        }
        if ($async) {
            exec($cmd, $out);
        } else {
            ob_start();
            system($cmd);
            $out = ob_get_clean();
        }
        if ($async) {
            return $out[0];
        } else {
            return $out;
        }
    }
}
