<?php

/*
 * This file is part of the chenshuai1993/sci-uploader.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Chenshuai1993\SciUploader\Exceptions;

class InvalidArgumentException extends Exception
{
    // 上传路径未配置
    const UPLOAD_PATH_NOT_CONFIG = 100100100;
    // 文件存储路径未配置
    const STORAGE_PATH_NOT_CONFIG = 100100101;
    // 文件路径为空
    const FILE_PATH_IS_EMPTY = 100100102;
    // 文件名称为空
    const FILE_NAME_IS_EMPTY = 100100103;
    // 文件hash为空
    const FILE_HASH_IS_EMPTY = 100100104;
    // 合并文件超时
    const MERGE_FILE_TIMEOUT = 100100105;
    // 移动文件失败
    const MOVE_FILE_FAIL = 100100106;

    const ERROR_CODE = [
        self::UPLOAD_PATH_NOT_CONFIG => '文件上传路径未配置',
        self::STORAGE_PATH_NOT_CONFIG => '文件存储路径未配置',
        self::FILE_PATH_IS_EMPTY => '文件路径为空',
        self::FILE_NAME_IS_EMPTY => '文件名称为空',
        self::FILE_HASH_IS_EMPTY => '文件hash为空',
        self::MERGE_FILE_TIMEOUT => '合并文件超时',
        self::MOVE_FILE_FAIL => '移动文件失败',
    ];

    public function __construct(int $code = 0, string $message = '', $previous = null)
    {
        if (is_int($code) && array_key_exists($code, self::ERROR_CODE)) {
            $message = self::ERROR_CODE[$code];
        }
        parent::__construct($code, $message, $previous);
    }
}
