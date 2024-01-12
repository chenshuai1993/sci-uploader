<?php

/*
 * This file is part of the chenshuai1993/sci-uploader.
 *
 * (c) chenshuai1993 <chen.shuaishuai@scimall.org.cn>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Chenshuai1993\SciUploader\Exceptions;

class Exception extends \Exception
{
    public function __construct(int $code = 0, string $message = '', $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
