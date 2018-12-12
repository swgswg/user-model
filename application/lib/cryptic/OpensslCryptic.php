<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/6
 * Time: 10:01
 */

namespace app\lib\cryptic;


use app\lib\exception\CrypticException;

class OpensslCryptic
{
    // +----------------------------------------------------------------------
    // | 注: 公钥加密一定要私钥解密, 私钥加密一定要公钥解密
    // +----------------------------------------------------------------------


    /**
     * 公钥加密
     * @param string $data 加密数据
     * @param string $public_key 公钥
     * @return string 加密后的数据
     * @throws CrypticException
     */
    protected function publicEncrypt($data = '', $public_key = '')
    {
        if(!$public_key){
            throw new CrypticException();
        }
        openssl_public_encrypt($data, $encrypt, $public_key);
        return base64_encode($encrypt);
    }


    /**
     * 私钥解密
     * @param string $data  解密数据
     * @param string $private_key 私钥
     * @return mixed 解密后的数据
     * @throws CrypticException
     */
    protected function privateDecrypt($data = '',$private_key='')
    {
        if(!$private_key){
            throw new CrypticException();
        }
        openssl_private_decrypt(base64_decode($data), $decrypt, $private_key);
        return $decrypt;
    }


    //======================================================================================


    /**
     * 私钥加密
     * @param string $data 加密数据
     * @param string $private_key 私钥
     * @return string 加密后的数据
     * @throws CrypticException
     */
    protected function privateEncrypt($data = '', $private_key = '')
    {
        if(!$private_key){
            throw new CrypticException();
        }
        $encrypt = '';
        $arr = str_split($data, 117);
        foreach ($arr as $chunk) {
            openssl_private_encrypt($chunk, $encryptData, $private_key);
            $encrypt .= $encryptData;
        }

//        openssl_private_encrypt($data, $encrypt, $private_key);
        return base64_encode($encrypt);
    }


    /**
     * 公钥解密
     * @param string $data 解密数据
     * @param string $public_key 公钥
     * @return mixed 解密后的数据
     * @throws CrypticException
     */
    protected function publicDecrypt($data = '', $public_key = '')
    {
        if(!$public_key){
            throw new CrypticException();
        }
        $decrypt = '';
        $arr = str_split(base64_decode($data),128);
        foreach ($arr as $chunk) {
            openssl_public_decrypt($chunk, $decryptData, $public_key);
            $decrypt .= $decryptData;
        }
//        openssl_public_decrypt(base64_decode($data), $decrypt, $public_key);
        return $decrypt;
    }

}