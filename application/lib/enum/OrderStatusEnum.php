<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/1
 * Time: 21:05
 */

namespace app\lib\enum;


class OrderStatusEnum
{
    // 待支付
    const UNPAIN = 1;

    // 已支付
    const PAID = 2;

    // 已发货
    const DELIVERED = 3;

    // 已支付, 但库存不足
    const PAID_BUT_OUT_OF = 4;
}