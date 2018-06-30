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

class Wood
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
    public $pluginHttpDomain = "http://192.168.3.43:5188"; ////需要修改成对应的扩展服务器地址
    public static $instance = null;

    public $cssForWebmsg;

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
            self::$instance = new Wood();
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
    public function shareGameToChat($siteSessionId, $chatSessionId, $hrefType, $gameResult)
    {
        $userProfile = $this->zalyHelper->getSiteUserProfile($siteSessionId);
        if(!$userProfile) {
            return json_encode(['error_code' => 'fail', 'error_msg' => '请稍候再试！']);
        }
        $siteUserId    = $userProfile->getSiteUserId();
        $siteUserPhoto = $userProfile->getUserPhoto();
        $this->dbHelper->insertGameResult($siteUserId, $siteUserPhoto, $chatSessionId,  $gameResult);
        $text= "我在「堆木头」游戏中，得了$gameResult 分，快来挑战我吧！";
        $this->setTextMsgByApiClient($chatSessionId, $siteSessionId,$siteUserId, $text, $hrefType);
    }
    /**
     * @param $siteSessionId
     * @param $chatSessionId
     * @param $hrefType
     * @param $time
     */
    public function recordGame($siteSessionId, $chatSessionId, $gameResult)
    {
        $userProfile = $this->zalyHelper->getSiteUserProfile($siteSessionId);
        if(!$userProfile) {
            return json_encode(['error_code' => 'fail', 'error_msg' => '请稍候再试！']);
        }
        $siteUserId    = $userProfile->getSiteUserId();
        $siteUserPhoto = $userProfile->getUserPhoto();
        $this->dbHelper->insertGameResult($siteUserId, $siteUserPhoto, $chatSessionId, $gameResult);
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
    public function setTextMsgByApiClient($chatSessionId, $siteSessionId,$siteUserId, $text, $hrefType)
    {
        if($hrefType == $this->u2Type) {
            $this->zalyHelper->sendU2TextMsgByApiClient($chatSessionId, $siteSessionId,$siteUserId, $text );
            return;
        }
        $this->zalyHelper->sendGroupTextMsgByApiClient($chatSessionId, $siteSessionId,$siteUserId, $text);
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
    public function setMsgByApiClient($chatSessionId, $siteSessionId,$siteUserId, $webCode,  $hrefType, $hrefUrl, $height = 30, $width = 160)
    {
        if($hrefType == $this->u2Type) {
            $this->zalyHelper->sendU2WebMsgByApiClient($chatSessionId, $siteSessionId,$siteUserId, $webCode, $hrefUrl, $height, $width );
            return;
        }
        $this->zalyHelper->sendGroupWebMsgByApiClient($chatSessionId, $siteSessionId,$siteUserId, $webCode, $hrefUrl, $height, $width);
    }
}

$wood = Wood::getInstance();
$pageType    = isset($_GET['page_type']) ? $_GET['page_type'] : "first";
$httpReferer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$urlParams   = $wood->parseUrl($httpReferer);

if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST"){
    $data = json_decode($_POST['game_data'], true);
    $pageType  = isset($data['page_type']) ? $data['page_type'] : "share_game";
    $hrefType  = isset($data['href_type']) ? $data['href_type'] : "";
    $chatSessionId = isset($data['chat_session_id']) ? $data['chat_session_id'] :"";
    $siteSessionId = $urlParams['site_session_id'];
    $gameResult = isset($data['game_result'])?$data['game_result'] : "0";
}

switch ($pageType) {
    case "first":
        $urlParams['http_domain'] = $wood->pluginHttpDomain;
        $urlParams['suffix'] = "?".time();
        $urlParams['href_url'] = $wood->pluginHttpDomain."/index.php?chat_session_id=".$urlParams['chat_session_id']."&href_type=".$urlParams['href_type'];
        echo $wood->render("index", $urlParams);
        break;

    case "share_game":
        $wood->shareGameToChat($siteSessionId, $chatSessionId, $hrefType, $gameResult);
        break;
    case "record_game":
        $wood->recordGame($siteSessionId, $chatSessionId, $gameResult);
        break;
}