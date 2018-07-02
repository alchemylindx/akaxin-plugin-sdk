<?php

use \Google\Protobuf\Internal\Message;



// 加载Google Protobuf Library
// https://packagist.org/packages/google/protobuf
//
// 如果不想使用SDK中的Google lib，比如自己产品已经有composer机制
// 可以 define("DONNOT_USE_AKAXIN_GOOGLE_PROTOBUF_LIB", true);
//
// ==========================
if (false === defined("DONNOT_USE_AKAXIN_GOOGLE_PROTOBUF_LIB") || false === DONNOT_USE_AKAXIN_GOOGLE_PROTOBUF_LIB) {
    $path = __DIR__ ."/vendor/autoload.php";
    if (file_exists($path)) {
        require_once(__DIR__ ."/vendor/autoload.php");
    }
}

// 加载AkaxinProtoSDK
// ==================
// Akaxin\Proto 有很多，在sdk-php文件夹下，具体接口使用哪一个，请参考接口文档。
function autoloadForAkaxinProtoSDK($className) {
    $className = str_replace("\\", "/", $className);
    $path = __DIR__ . "/{$className}.php";
    if (file_exists($path)) {
        require_once(__DIR__ . "/{$className}.php");
    }
}
spl_autoload_register("autoloadForAkaxinProtoSDK");


class AkaxinReferer {

    private $params = array();

    public static function getInstance() {
        return new AkaxinReferer($_SERVER["HTTP_REFERER"]);
    }

    public function __construct($url) {
        $ret = parse_url($url, PHP_URL_QUERY);
        parse_str($ret, $this->params);
    }

    // 当前是否为群聊
    public function isGroupChat() {
        return isset($this->params["page"]) && $this->params["page"] == "group_msg";
    }

    // 获取群组ID
    public function getChatGroupId() {
        if ($this->isGroupChat() && isset($this->params["site_group_id"])) {
            return $this->params["site_group_id"];
        }
        return "";
    }

    // 是否为私聊
    public function isU2Chat() {
        return isset($this->params["page"]) && $this->params["page"] == "u2_msg";
    }

    // 获取好友ID
    public function getChatFriendId() {
        if ($this->isU2Chat() && isset($this->params["site_user_id"])) {
            return $this->params["site_user_id"];
        }
        return "";
    }

    // 获取SessionID，用于向Site获取用户资料
    public function getAkaxinSessionId() {
        return isset($_COOKIE["akaxin_site_session_id"]) ? $_COOKIE["akaxin_site_session_id"] : "";
    }

    // 返回自定义启动参数
    public function getAkaxinParam() {
        return isset($this->params["akaxin_param"]) ? $this->params["akaxin_param"] : "";
    }

    public static function saveCookie() {
        $_sessionId = self::getInstance()->getAkaxinSessionId();

        if (!empty($_sessionId)) {
            setcookie("akaxin_site_session_id", $_sessionId);
        }
    }
}

// 兼容iOS内页Cookie

if(isset($_SERVER["HTTP_REFERER"])) {
    AkaxinReferer::saveCookie();
}


/**
 *
 * 用于访问InnerAPI的客户端
 *
 */
class AkaxinPluginApiClient {

    private $apiHost = "127.0.0.1"; // 对应启动服务器时的 -Dhttp.address 参数
    private $apiPort = 8280;        // 对应启动服务器时的 -Dhttp.port 参数
    private $authkey = "";          // 管理平台->扩展列表，点击相应的扩展获取。
    private $pluginId = -1;         // 管理平台->扩展列表，点击相应的扩展获取。

    private $sessionSiteUserId = "b182fa30-a9ff-4d5a-b9fd-012facb54d84";

    private $lastErrorCode = "";
    private $lastErrorInfo = "";

    /**
     * 设置InnerAPI服务器地址、authkey
     *
     * @param string $apiHost 对应启动服务器时的 -Dhttp.address 参数
     * @param string $apiPort 对应启动服务器时的 -Dhttp.port 参数
     * @param int $pluginId 管理平台->扩展列表，点击相应的扩展获取。
     * @param string $authkey 管理平台->扩展列表，点击相应的扩展获取。
     */
    public function __construct($apiHost, $apiPort, $pluginId, $authkey) {
        $this->apiHost = $apiHost;
        $this->apiPort = $apiPort;
        $this->pluginId = $pluginId;
        $this->authkey = $authkey;
    }

    // 获取用户从客户端点击到扩展页面，传递进来的业务参数
    public function getAkaxinParam() {
        return AkaxinReferer::getInstance()->getAkaxinParam();
    }

    /**
     * 执行API请求
     *
     * 请参考具体的接口文档。
     *
     * @param $actionName 接口名称
     * @param Message $requestMessage
     * @return false|string false代表失败，string代表response的二进制数据
     */
    public function request($actionName, Message $requestMessage) {

        $this->lastErrorCode = "";
        $this->lastErrorInfo = "";


        $requestPackage = new Akaxin\Proto\Core\ProxyPluginPackage();
        $data = $requestMessage->serializeToString();
        $data = base64_encode($data); //千万不要忘了这一步！
        $requestPackage->setData($data);
        $requestPackage->setPluginHeader($this->makeRequestHeader());

        $responsePackage = $this->curlRequest($actionName, $requestPackage);

        if (false === $responsePackage) {
            return false;
        }
        $errorInfo = $responsePackage->getErrorInfo();

        if ($errorInfo instanceof Akaxin\Proto\Core\ErrorInfo) {
            $this->lastErrorCode = $errorInfo->getCode();
            $this->lastErrorInfo = $errorInfo->getInfo();
        } else {
            $this->lastErrorCode = "";
            $this->lastErrorInfo = "";
        }

        // 业务逻辑，开始解析Response
        // ===============
        return base64_decode($responsePackage->getData());
    }

    /**
     * 设置当前Session用户，可以不设置
     *
     *
     * @param string $sessionSiteUserId sessionSiteUserId
     */
    public function setSessionSiteUserId($sessionSiteUserId) {
        $this->sessionSiteUserId = $sessionSiteUserId;
    }

    /**
     * 返回最近一次错误的错误码
     *
     * @param string
     */
    public function errorCode() {
        return $this->lastErrorCode;
    }

    /**
     * 返回最近一次错误的错误信息
     *
     * @param string
     */
    public function errorInfo() {
        return $this->lastErrorInfo;
    }

    private function curlRequest($actionName, Message $requestPackage) {
        $postData = openssl_encrypt($requestPackage->serializeToString() , "AES-128-ECB", $this->authkey, OPENSSL_RAW_DATA);

        $actionName = trim($actionName, "/\\ ");
        $url = "http://{$this->apiHost}:{$this->apiPort}/akaxin-plugin-api/{$actionName}";
        $ch = curl_init("http://{$this->apiHost}:{$this->apiPort}/{$actionName}");
        $httpHeader = array(
            "site-plugin-id: {$this->pluginId}", // 替换为自己的扩展ID
        );
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseInBinary = curl_exec($ch); // curl可能会出错，要容错。

        if (false === $responseInBinary) {
            $this->lastErrorCode = "curl_errno: " . curl_errno($ch);
            $this->lastErrorInfo = "curl_info: " . curl_error($ch);
            trigger_error("curl_exec errors {$url} " . curl_error($ch));
        }
        curl_close($ch);

        $responseData = openssl_decrypt($responseInBinary, "AES-128-ECB", $this->authkey, OPENSSL_RAW_DATA); // 解密可能失败
        $responsePackage = new Akaxin\Proto\Core\ProxyPluginPackage();
        $responsePackage->mergeFromString($responseData);
        return $responsePackage;
    }

    private function makeRequestHeader() {
        $requestPakcageHeader = array(
            Akaxin\Proto\Core\PluginHeaderKey::PLUGIN_TIMESTAMP => time()*1000, // 安全作用
        );

        if (!empty($this->sessionSiteUserId)) {
            $key = Akaxin\Proto\Core\PluginHeaderKey::CLIENT_SITE_USER_ID;
            $requestPakcageHeader[$key] = $this->sessionSiteUserId;
        }
        return $requestPakcageHeader;
    }
}
