<?php

/*
 * This file is part of the chenshuai1993/sci-uploader.
 *
 * (c) chenshuai1993 <chen.shuaishuai@scimall.org.cn>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Chenshuai1993\SciUploader\Contracts;

interface MultiPartUploadInterface
{
    /**
     * 上传分片.
     *
     * @param string $fileName   文件名
     * @param string $fileHash   文件哈希值
     * @param int    $partNumber 分片序号
     *
     * @return mixed
     */
    public function uploadPart(string $fileName, string $fileHash, int $partNumber);

    /**
     * 合并分片到一个文件.
     *
     * @param string $fileName    文件名
     * @param string $fileHash    文件哈希值
     * @param int    $totalNumber 文件分片总数
     *
     * @return mixed
     */
    public function mergePartsToFile(string $fileName, string $fileHash, int $totalNumber);

    /**
     * 完成分片上传后的操作.
     *
     * @return mixed
     */
    public function completeMultipartUpload(?\Closure $closure = null);
}
