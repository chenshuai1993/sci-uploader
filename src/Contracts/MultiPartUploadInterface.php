<?php

namespace Chenshuai1993\SciUploader\Contracts;

interface MultiPartUploadInterface
{
    /**
     * 上传分片
     * @param string $fileName 文件名
     * @param string $fileHash 文件哈希值
     * @param int $partNumber 分片序号
     * @return mixed
     */
    public function uploadPart(string $fileName, string $fileHash, int $partNumber);

    /**
     * 合并分片到一个文件
     * @param string $fileName 文件名
     * @param string $fileHash 文件哈希值
     * @param int $totalNumber 文件分片总数
     * @return mixed
     */
    public function mergePartsToFile(string $fileName, string $fileHash, int $totalNumber);

    /**
     * 完成分片上传后的操作
     * @param \closure|null $closure
     * @return mixed
     */
    public function completeMultipartUpload(?\closure $closure = null);
}