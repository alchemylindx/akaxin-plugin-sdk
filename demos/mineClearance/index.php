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

class MineClearance
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
    public $pluginHttpDomain = "http://192.168.3.43:5166"; ////需要修改成对应的扩展服务器地址
    public static $instance = null;

    public $dbHelper;
    public $zalyHelper;
    public $pluginId;
    public function __construct($configName)
    {
        $config = getConf($configName);
        $this->pluginId = $config['plugin_id'];
        $this->siteAddress = $config['site_address'];
        $this->pluginHttpDomain = $config['plugin_http_domain'];
        $this->dbHelper   = DBHelper::getInstance($config);
        $this->zalyHelper = ZalyHelper::getInstance($config);
    }

    /**
     * @return GuessNum|null
     *
     * @author 尹少爷 2018.6.13
     */
    public static function getInstance()
    {
        if(!self::$instance) {
            self::$instance = new MineClearance();
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
        $path = dirname(__DIR__)."/".basename(__DIR__).'/views/'.$fileName.'.html';
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
        $siteSessionId = $akaxinReferer->getAkaxinSessionId();
        return ['chat_session_id' => $chatSessionId, 'href_type' => $hrefType, 'akaxin_param' => $akaxinReferer->getAkaxinParam(), "site_session_id" => $siteSessionId];
    }

    /**
     * @param $siteSessionId
     * @param $chatSessionId
     * @param $hrefType
     * @param $time
     */
    public function shareGameToChat($siteSessionId, $chatSessionId, $hrefType, $time, $gameType)
    {
        $userProfile = $this->zalyHelper->getSiteUserProfile($siteSessionId);
        error_log("session id ==" . $siteSessionId);
        if(!$userProfile) {
            return json_encode(['error_code' => 'fail', 'error_msg' => '请稍候再试！']);
        }
        $siteUserId    = $userProfile->getSiteUserId();
        $siteUserPhoto = $userProfile->getUserPhoto();
        $hrefUrl = $this->getHrefUrl($chatSessionId, $siteUserId, $hrefType);
        switch ($gameType) {
            case "share_fail":
                $this->dbHelper->insertGameResult($siteUserId, $siteUserPhoto, $chatSessionId, "fail", $time);
                error_log("site user id = ".$siteUserId);
                $this->sendPluginFailMsg($chatSessionId, $siteSessionId, $siteUserId, $hrefType, $time);
                break;
            case "share_success":
                $this->dbHelper->insertGameResult($siteUserId, $siteUserPhoto, $chatSessionId, "success", $time);
                $this->sendPluginSuccessMsg($chatSessionId, $siteSessionId, $siteUserId, $hrefType, $hrefUrl, $time);
                break;
            default:
                $this->dbHelper->insertGameResult($siteUserId, $siteUserPhoto, $chatSessionId, "unkonw", $time);
                $this->sendPluginMsg($chatSessionId, $siteSessionId, $siteUserId, $hrefType, $hrefUrl);

        }
    }



    /**
     * 得到hrefUrl
     *
     * @param $chatSessionId
     * @param $gameNum
     * @param $gameType
     * @param $hrefType
     * @return mixed|string
     *
     */
    protected  function getHrefUrl($chatSessionId, $siteUserId, $hrefType)
    {
        if($hrefType == $this->u2Type) {
            $hrefUrl = str_replace(["SiteAddress", "chatSessionId", "PluginId"], [$this->siteAddress, $siteUserId, $this->pluginId], $this->u2HrefUrl);
        } else {
            $hrefUrl = str_replace(["SiteAddress", "chatSessionId", "PluginId"], [$this->siteAddress, $chatSessionId, $this->pluginId], $this->groupHrefUrl);
        }
        return $hrefUrl;
    }


    /**
     * @param $chatSessionId
     * @param $siteSessionId
     * @param $gameNum
     * @param $hrefType
     *
     * @author 尹少爷 2018.6.11
     */
    public function sendPluginMsg($chatSessionId, $siteSessionId, $siteUserId, $hrefType, $hrefUrl)
    {
        $webCode = <<<eot
        <!DOCTYPE html><html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
            <style>
                .wrapper {
                    height: 100%;
                    width: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .zaly-btn, .zaly-btn:hover,.zaly-btn:active, .zaly-btn:focus, .zaly-btn:active:focus, .zaly-btn:active:hover {
                    width:209px; height:46px;
                    background:rgba(226,130,179,1);
                    box-shadow:0px 8px 4px -8px rgba(242,234,165,1);
                    border-radius:4px; border:4px solid rgba(188,83,131,1);
                }
                .zaly-btn-font{
                    font-size:12px; font-family:PingFangSC-Regular;
                    color:rgba(255,255,255,1);
                    line-height:20px;
                }

            </style>
        </head>
        <body>
        <div class="wrapper">
            <div>
                <div style="text-align: center; margin: 16px auto 10px auto; color:rgba(188,83,131,1); font-weight: bold;">
                   前方发现地雷，请保护自己。
                </div>
                <div>
                    <button type="button" class="btn zaly-btn zaly-btn-font">来一起加入挑战吧!</button>
                </div>
            </div>
        </div></body></html>
eot;
        $this->sendWebMsgByApiClient($chatSessionId, $siteSessionId, $siteUserId, $webCode, $hrefType, $hrefUrl, 120, 300);
    }

    /**
     * @param $chatSessionId
     * @param $siteSessionId
     * @param $gameNum
     * @param $hrefType
     *
     * @author 尹少爷 2018.6.11
     */
    public function sendPluginFailMsg($chatSessionId, $siteSessionId, $siteUserId, $hrefType, $text)
    {
        $text = " /(ㄒoㄒ)/~~扫雷失败了，用时{$text}！";
        $this->sendTextMsgByApiClient($chatSessionId, $siteSessionId, $siteUserId, $hrefType, $text);
    }
    /**
     * @param $chatSessionId
     * @param $siteSessionId
     * @param $gameNum
     * @param $hrefType
     *
     * @author 尹少爷 2018.6.11
     */
    public function sendPluginSuccessMsg($chatSessionId, $siteSessionId, $siteUserId, $hrefType, $hrefUrl, $text)
    {
        $webCode = <<<eot
        <!DOCTYPE html><html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
            <style>
                .wrapper {
                    height: 100%;
                    width: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .zaly-btn, .zaly-btn:hover,.zaly-btn:active, .zaly-btn:focus, .zaly-btn:active:focus, .zaly-btn:active:hover {
                    width:209px; height:46px;
                    background:rgba(226,130,179,1);
                    box-shadow:0px 8px 4px -8px rgba(242,234,165,1);
                    border-radius:4px; border:4px solid rgba(188,83,131,1);
                }
                .zaly-btn-font{
                    font-size:12px; font-family:PingFangSC-Regular;
                    color:rgba(255,255,255,1);
                    line-height:20px;
                }

            </style>
        </head>
        <body>
        <div class="wrapper">
            <div>
                <div style="text-align: center; margin: 16px auto 10px auto; color:rgba(188,83,131,1); font-weight: bold;">
                    傲娇的完成扫雷任务，用时{$text}！
                </div>
                <div>
                    <button type="button" class="btn zaly-btn zaly-btn-font">来一起加入挑战吧!</button>
                </div>
            </div>
        </div></body></html>
eot;
        $this->sendWebMsgByApiClient($chatSessionId, $siteSessionId, $siteUserId, $webCode, $hrefType, $hrefUrl, 120, 300);
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
    public function sendWebMsgByApiClient($chatSessionId, $siteSessionId,$siteUserId, $webCode,  $hrefType, $hrefUrl, $height = 30, $width = 160)
    {
        if($hrefType == $this->u2Type) {
            $this->zalyHelper->sendU2WebMsgByApiClient($chatSessionId, $siteSessionId,$siteUserId, $webCode, $hrefUrl, $height, $width );
            return;
        }
        $this->zalyHelper->sendGroupWebMsgByApiClient($chatSessionId, $siteSessionId,$siteUserId, $webCode, $hrefUrl, $height, $width);
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
            $this->zalyHelper->sendU2TextMsgByApiClient($chatSessionId, $siteSessionId,$siteUserId,$text );
            return;
        }
        $this->zalyHelper->sendGroupTextMsgByApiClient($chatSessionId, $siteSessionId,$siteUserId,$text);
    }
}

$configName = isset($_GET['config_name']) ? $_GET['config_name'] : "default";
if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST"){
    $configName = isset($_POST['config_name']) ? $_POST['config_name'] : "default";
}

$mineClearance = MineClearance::getInstance($configName);
$pageType    = isset($_GET['page_type']) ? $_GET['page_type'] : "first";
$httpReferer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

$urlParams = $mineClearance->parseUrl($httpReferer);
if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST"){
    $pageType  = isset($_POST['page_type']) ? $_POST['page_type'] : "share_fail";
    $hrefType  = isset($_POST['href_type']) ? $_POST['href_type'] : "";
    $chatSessionId = isset($_POST['chat_session_id']) ? $_POST['chat_session_id'] :"";
    $siteSessionId = $urlParams['site_session_id'];
    $time = isset($_POST['use_time']) ? $_POST['use_time']:"";
    $gameType = isset($_POST['game_type'])?$_POST['game_type'] : "unknow";
}
switch ($pageType) {
    case "first":
        $urlParams['http_domain'] = $mineClearance->pluginHttpDomain;
        $urlParams['suffix'] = "?".time();
        $urlParams['config_name'] = $configName;
        $urlParams['href_url'] = $mineClearance->pluginHttpDomain."/index.php?&configName=".$configName."&chat_session_id=".$urlParams['chat_session_id']."&href_type=".$urlParams['href_type'];
        echo $mineClearance->render("index", $urlParams);
        break;

    case "share_fail":
    case "share_success":
        $mineClearance->shareGameToChat($siteSessionId, $chatSessionId, $hrefType, $time, $gameType);
        break;
}