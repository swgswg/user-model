<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/6
 * Time: 10:30
 */

namespace app\api\service;


use app\lib\cryptic\OpensslCryptic;

class Cryptic extends OpensslCryptic
{
    private $public_key;
    private $private_key;

    public function __construct()
    {
        $this->private_key = config('cryptic.private_key');
        $this->public_key  = config('cryptic.public_key');
    }


    /**
     * 私钥加密
     * @param string $data 要加密的数据
     * @return string
     * @throws \app\lib\exception\CrypticException
     */
    public function encrypt($data = '')
    {
        $encrypt = $this->privateEncrypt($data,$this->private_key);
        return $encrypt;
    }


    /**
     * 公钥解密
     * @param string $data 解密后的数据
     * @return mixed
     * @throws \app\lib\exception\CrypticException
     */
    public function decrypt($data = '')
    {
        return $this->publicDecrypt($data,$this->public_key);
    }
}