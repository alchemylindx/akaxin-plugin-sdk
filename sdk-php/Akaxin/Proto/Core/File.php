<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: core/file.proto

namespace Akaxin\Proto\Core;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 *上传站点服务端的文件
 *
 * 
 */
class File extends \Google\Protobuf\Internal\Message
{
    /**
     * 
     */
    private $file_id = '';
    /**
     * 
     */
    private $file_content = '';
    /**
     * 
     */
    private $file_type = 0;

    public function __construct() {
        \GPBMetadata\Core\File::initOnce();
        parent::__construct();
    }

    /**
     * 
     * @return string
     */
    public function getFileId()
    {
        return $this->file_id;
    }

    /**
     * 
     * @param string $var
     * @return $this
     */
    public function setFileId($var)
    {
        GPBUtil::checkString($var, True);
        $this->file_id = $var;

        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getFileContent()
    {
        return $this->file_content;
    }

    /**
     * 
     * @param string $var
     * @return $this
     */
    public function setFileContent($var)
    {
        GPBUtil::checkString($var, False);
        $this->file_content = $var;

        return $this;
    }

    /**
     * 
     * @return int
     */
    public function getFileType()
    {
        return $this->file_type;
    }

    /**
     * 
     * @param int $var
     * @return $this
     */
    public function setFileType($var)
    {
        GPBUtil::checkEnum($var, \Akaxin\Proto\Core\FileType::class);
        $this->file_type = $var;

        return $this;
    }

}

