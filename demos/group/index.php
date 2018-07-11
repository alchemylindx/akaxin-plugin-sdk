<?php


//
// 心有灵犀 演示代码
//
//
// 这只是一个Demo代码，正式产品请勿参考此代码的组织结构。
//

require_once(__DIR__ . "/../../sdk-php/AkaxinPluginApiClient.php");

require_once(__DIR__ . "/config.php");
//require_once("/akaxin/guessNum/config.php");
require_once(__DIR__ . "/dbHelper.php");
require_once(__DIR__ . "/../helper/zalyHelper.php");


class Group
{

    public $db;
    public $hrefUrl;
    public $u2Type     = "u2_msg";
    public $groupType  = "group_msg";
    public $tableName  = "heart_and_soul";
    public $siteAddress  = "";//需要修改对应的站点
    public $u2HrefUrl    = "zaly://SiteAddress/goto?page=plugin_for_u2_chat&site_user_id=chatSessionId&plugin_id=PluginId&akaxin_param=";
    public $groupHrefUrl = "zaly://SiteAddress/goto?page=plugin_for_group_chat&site_group_id=chatSessionId&plugin_id=PluginId&&akaxin_param=";

    public $akaxinApiClient;
    public $pluginHttpDomain = ""; ////需要修改成对应的扩展服务器地址
    public static $instance = null;

    public $cssForWebmsg;

    public $dbHelper;
    public $zalyHelper;
    public $pluginId;

    /**
     * @return GuessNum|null
     *
     * @author 尹少爷 2018.6.13
     */
    public static function getInstance($configName)
    {
        if(!self::$instance) {
            self::$instance = new Group($configName);
        }
        return self::$instance;
    }

    private function __construct($configName)
    {

        $config = getConf($configName);
        $this->pluginId = $config['plugin_id'];
        $this->siteAddress = $config['site_address'];
        $this->pluginHttpDomain = $config['plugin_http_domain'];
        $this->dbHelper   = DBHelper::getInstance($config);
        $this->zalyHelper = ZalyHelper::getInstance($config);
        ////

        $this->cssForWebmsg = <<<eot
            <link rel="stylesheet" href="{$this->pluginHttpDomain}/Public/css/zaly.css" />
eot;
    }

    /**
     * 渲染页面
     *
     * @author 尹少爷 2018.6.11
     *
     */
    public function render($fileName, $params = [])
    {
        ob_start();
        $path = dirname(__DIR__)."/".basename(__DIR__).'/Views/'.$fileName.'.html';

        if ($params) {
            extract($params, EXTR_SKIP);
        }

        include($path);
        $var = ob_get_contents();
        ob_end_clean();
        return  $var;
    }


    /**
     * 返回跟随的referer
     * @param $url
     * @return mixed
     *
     * @author 尹少爷 2018.6.11
     */
    public function parseUrl($url)
    {
        $akaxinReferer = new AkaxinReferer($url);
        $akaxinReferer->isGroupChat();

        if($akaxinReferer->isU2Chat()){
            $chatSessionId = $akaxinReferer->getChatFriendId();
            $hrefType = "u2_msg";
        } elseif($akaxinReferer->isGroupChat()){
            $chatSessionId = $akaxinReferer->getChatGroupId();
            $hrefType = "group_msg";
        } else {
            $chatSessionId = "";
            $hrefType = "session";
        }
        return ['chat_session_id' => $chatSessionId, 'href_type' => $hrefType, 'akaxin_param' => $akaxinReferer->getAkaxinParam()];
    }

    public function getGroupLists($page = 1, $pageSize = 15)
    {
        $result = $this->zalyHelper->getGroupLists($page, $pageSize);
        $result['loading'] = $result['total_num'] >= $pageSize ? true : false;
        return $result;
    }

}

$configName = isset($_GET['config_name']) ? $_GET['config_name'] : "default";
if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST"){
    $configName = isset($_POST['config_name']) ? $_POST['config_name'] : "default";
}
$group =  Group::getInstance($configName);

$pageType  = isset($_GET['page_type']) ? $_GET['page_type'] : "first";
$hrefType  = isset($_GET['href_type']) ? $_GET['href_type'] : "";
$chatSessionId = isset($_GET['chat_session_id']) ? $_GET['chat_session_id'] :"";
////如果是下载图片，则直接返回数据
if($pageType == 'imageDownload') {
    $gameSiteUserId = isset($_GET['game_site_user_id']) ? $_GET['game_site_user_id'] : "";
    $userAvatar     = $group->zalyHelper->getUserAvatar($gameSiteUserId);
    header('Content-Type: image/png');
    echo $userAvatar;
    return false;
}

/////默认第四步骤是post请求
if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST"){
    $pageType  = isset($_POST['page_type']) ? $_POST['page_type'] : "four";
    $hrefType  = isset($_POST['href_type']) ? $_POST['href_type'] : "";
    $chatSessionId = isset($_POST['chat_session_id']) ? $_POST['chat_session_id'] :"";
}

$httpCookie = isset($_COOKIE) ?  $_COOKIE : "";
$siteSessionId = $httpCookie;
$siteSessionId = isset($siteSessionId['akaxin_site_session_id']) ? $siteSessionId['akaxin_site_session_id'] : '';

$httpReferer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
////第一次进来需要处理chatSession, 以及hrefType, akaxin_param其他的时候
$urlParams   = $group->parseUrl($httpReferer);

if(isset($urlParams['akaxin_param']) && $urlParams['akaxin_param']) {
    $params = json_decode($urlParams['akaxin_param'], true);
    $pageType  = isset($params['page_type']) ? $params['page_type'] : "third" ;
    $hrefType  = $urlParams['href_type'];
    $chatSessionId = $urlParams['chat_session_id'];
}

switch ($pageType) {
    case "first":
        $urlParams['http_domain'] = $group->pluginHttpDomain;
        $urlParams['href_url'] = $group->pluginHttpDomain."/index.php?config_name=".$configName."&is_sponsor=1&page_type=second&chat_session_id=".$urlParams['chat_session_id']."&href_type=".$urlParams['href_type'];
        $groupLists = $group->getGroupLists();
        $urlParams = array_merge($urlParams, $groupLists);
        echo $group->render("index", $urlParams);
        break;

}
