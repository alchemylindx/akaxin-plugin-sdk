<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 28/06/2018
 * Time: 10:31 AM
 */
require_once(__DIR__ . "/../../sdk-php/AkaxinPluginApiClient.php");

require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/dbHelper.php");
require_once(__DIR__ . "/../helper/zalyHelper.php");

class Poke
{
    public $db;
    public $hrefUrl;
    public $u2Type     = "u2_msg";
    public $groupType  = "group_msg";
    public $tableName  = "heart_and_soul";
    public $siteAddress  = "192.168.3.43:2022";//需要修改对应的站点
    public $u2HrefUrl    = "zaly://SiteAddress/goto?page=plugin_for_u2_chat&site_user_id=chatSessionId&plugin_id=PluginId&akaxin_param=";
    public $groupHrefUrl = "zaly://SiteAddress/goto?page=plugin_for_group_chat&site_group_id=chatSessionId&plugin_id=PluginId&&akaxin_param=";

    public $akaxinApiClient;
    public $pluginHttpDomain = "http://192.168.3.43:5199"; ////需要修改成对应的扩展服务器地址
    public static $instance = null;

    public $dbHelper;
    public $zalyHelper;
    public $pluginId;
    public function __construct()
    {
        $this->dbHelper   = DBHelper::getInstance();
        $this->zalyHelper = ZalyHelper::getInstance();
        $config = getConf();
        $this->pluginId = $config['plugin_id'];
        $this->siteAddress = $config['site_address'];
        $this->pluginHttpDomain = $config['plugin_http_domain'];
    }

    /**
     * @return GuessNum|null
     *
     * @author 尹少爷 2018.6.13
     */
    public static function getInstance()
    {
        if(!self::$instance) {
            self::$instance = new Poke();
        }
        return self::$instance;
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
        } else {
            $chatSessionId = $akaxinReferer->getChatGroupId();
            $hrefType = "group_msg";
        }
        $siteSessionId = $akaxinReferer->getAkaxinSessionId();
        return ['chat_session_id' => $chatSessionId, 'href_type' => $hrefType, 'akaxin_param' => $akaxinReferer->getAkaxinParam(), "site_session_id" => $siteSessionId];
    }

    /**
     * @param $siteSessionId
     * @param $chatSessionId
     * @param $hrefType
     * @param $time
     */
    public function shareGameToChat($siteSessionId, $chatSessionId, $hrefType, $gameUseTime, $gameResult, $gameAccuracy)
    {
        $userProfile = $this->zalyHelper->getSiteUserProfile($siteSessionId);
        if(!$userProfile) {
            return json_encode(['error_code' => 'fail', 'error_msg' => '请稍候再试！']);
        }
        $siteUserId    = $userProfile->getSiteUserId();
        $siteUserPhoto = $userProfile->getUserPhoto();
        $this->dbHelper->insertGameResult($siteUserId, $siteUserPhoto, $chatSessionId, $gameResult, $gameUseTime, $gameAccuracy);
        $text = "我在{$gameUseTime}秒 戳了{$gameResult}个泡泡，快来挑战我吧";
        $this->sendTextMsgByApiClient($chatSessionId, $siteSessionId, $siteUserId, $hrefType, $text);
    }

    /**
     * @param $siteSessionId
     * @param $chatSessionId
     * @param $hrefType
     * @param $time
     */
    public function recordGameResult($siteSessionId, $chatSessionId, $hrefType, $gameUseTime, $gameResult, $gameAccuracy)
    {
        $userProfile = $this->zalyHelper->getSiteUserProfile($siteSessionId);
        if(!$userProfile) {
            return json_encode(['error_code' => 'fail', 'error_msg' => '请稍候再试！']);
        }
        $siteUserId    = $userProfile->getSiteUserId();
        $siteUserPhoto = $userProfile->getUserPhoto();
        $this->dbHelper->insertGameResult($siteUserId, $siteUserPhoto, $chatSessionId, $gameResult, $gameUseTime, $gameAccuracy);
    }

    /**
     * plugin 发送web消息
     *
     * @param $chatSessionId
     * @param $siteSessionId
     * @param $siteUserId
     * @param $webCode
     * @param $hrefType
     * @param $hrefUrl
     * @param int $height
     * @param int $width
     *
     * @author 尹少爷 2018.6.11
     */
    public function sendTextMsgByApiClient($chatSessionId, $siteSessionId, $siteUserId, $hrefType, $text)
    {
        if($hrefType == $this->u2Type) {
            $this->zalyHelper->sendU2TextMsgByApiClient($chatSessionId, $siteSessionId, $siteUserId, $text );
            return;
        }
        $this->zalyHelper->sendGroupTextMsgByApiClient($chatSessionId, $siteSessionId, $siteUserId, $text);
    }
}

$poke = Poke::getInstance();
$pageType    = isset($_GET['page_type']) ? $_GET['page_type'] : "first";
$httpReferer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$urlParams = $poke->parseUrl($httpReferer);
if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST"){
    $pageType      = isset($_POST['page_type']) ? $_POST['page_type'] : "share_fail";
    $hrefType      = isset($_POST['href_type']) ? $_POST['href_type'] : "";
    $gameUseTime   = isset($_POST['game_use_time']) ? $_POST['game_use_time']:"";
    $gameResult    = isset($_POST['game_result'])?$_POST['game_result'] :"0";
    $gameAccuracy  = isset($_POST['game_accuracy'])?$_POST['game_accuracy'] :"0";
    $chatSessionId = isset($_POST['chat_session_id']) ? $_POST['chat_session_id'] :"";
    $siteSessionId = $urlParams['site_session_id'];
    error_log("gameAccuracy ==".$gameAccuracy);
    error_log("gameResult ==".$gameResult);
    error_log("gameUseTime ==".$gameUseTime);
}

switch ($pageType) {
    case "first":
        $urlParams['http_domain'] = $poke->pluginHttpDomain;
        $urlParams['suffix'] = "?".time();
        $urlParams['href_url'] = $poke->pluginHttpDomain."/index.php?chat_session_id=".$urlParams['chat_session_id']."&href_type=".$urlParams['href_type'];
        echo $poke->render("index", $urlParams);
        break;

    case "share_game":
        $poke->shareGameToChat($siteSessionId, $chatSessionId, $hrefType, $gameUseTime, $gameResult, $gameAccuracy);
        break;
    case "game_record":
        $poke->recordGameResult($siteSessionId, $chatSessionId, $hrefType, $gameUseTime, $gameResult, $gameAccuracy);
        break;
}