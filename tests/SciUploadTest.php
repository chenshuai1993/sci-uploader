<?php

namespace Chenshuai1993\SciUploader\Tests;

use Chenshuai1993\SciUploader\MultiPartUpload;
use Chenshuai1993\SciUploader\FileStorage;
use PHPUnit\Framework\TestCase;

class SciUploadTest extends TestCase
{
    public function setUp(): void
    {
        require_once __DIR__.'/../vendor/autoload.php';
    }

    /**
     * 测试上传分片.
     *
     * @return void
     *
     * @throws \Chenshuai1993\SciUploader\Exceptions\InvalidArgumentException
     */
    public function testuploadPart()
    {
        // 由于是模拟$_FILES、所以并不会真正执行移动文件操作
        $_FILES = [
            'file' => [
                'name' => 'test.txt',
                'type' => 'text/plain',
                'tmp_name' => '/tmp/chunks/chunk_0',
                'error' => 0,
               'size' => 1000,
            ],
        ];
        $storage = new FileStorage('./uploads', './storage');
        $upload = new MultiPartUpload($storage);
        for ($i = 1; $i < 2; ++$i) {
            $upload->uploadPart('99atank.mp4', '123456', $i);
        }
    }

    /**
     * 测试合并分片.
     *
     * @return void
     *
     * @throws \Chenshuai1993\SciUploader\Exceptions\InvalidArgumentException
     */
    public function testMergePartsToFile()
    {
        // 人为吧文件放到指定目录下
        $storage = new FileStorage('.', './storage');
        $upload = new MultiPartUpload($storage);
        $upload->mergePartsToFile('99atank.mp4', '123456', 2);
    }

    /**
     * 测试计算文件分片数量.
     *
     * @return void
     *
     * @throws \Chenshuai1993\SciUploader\Exceptions\InvalidArgumentException
     */
    public function testCountParts()
    {
        $storage = new FileStorage('./', './storage');
        $upload = new MultiPartUpload($storage);
        $count = $upload->countParts('99atank.mp4', '123456');
        $this->assertEquals(2, $count);
    }

    /**
     * 测试通过文件哈希值获取文件信息.
     *
     * @return void
     */
    public function testGetFileByHash()
    {
        $storage = new FileStorage('./', './storage');
        $upload = new MultiPartUpload($storage);
        $this->assertEquals('./123456/99atank.mp4', $upload->getFileByHash('123456')['file']);
    }

    public function testDeleteFileByHash()
    {
        $storage = new FileStorage('./', './storage');
        $upload = new MultiPartUpload($storage);
        $this->assertEquals(true, $upload->deleteFileByHash('123456'));
    }
}
