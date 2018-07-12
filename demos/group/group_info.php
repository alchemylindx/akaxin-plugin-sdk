<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 12/07/2018
 * Time: 8:33 PM
 */


$groupConfig = [
    "default" => [
        '53ae0d26-faec-4618-9237-dc72c3a0a76a,扶摇打call中',
        '',

    ],
];


function getGroupInfoConfig($configName) {
    global $groupConfig;
    return isset($groupConfig[$configName]) ? $groupConfig[$configName] : $groupConfig['default'];
}



