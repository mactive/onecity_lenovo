<?php

/**
 * SINEMALL 支付寶語言文件   * $Author: dolphin $
 * $Id: alipay.php 14709 2008-07-07 08:45:51Z dolphin $
 */

global $_LANG;

$_LANG['alipay'] = '支付寶';
$_LANG['alipay_desc'] = '支付寶，是支付寶公司針對網上交易而特別推出的安全付款服務.<br/><a href="https://www.alipay.com/himalayas/market.htm?type=from_agent_contract&id=C4335319945672464113" target="_blank"><font color="red">點此申請免費簽約接口</font></a><br/><a href="https://www.alipay.com/himalayas/market.htm?type=from_agent_contract&id=C4335319945674798119" target="_blank"><font color="red">點此申請預付費簽約接口(600包4.2萬、1800包18萬交易額度)</font></a>';
$_LANG['alipay_account'] = '支付寶帳戶';
$_LANG['alipay_key'] = '交易安全校驗碼';
$_LANG['alipay_partner'] = '合作者身份ID';
$_LANG['pay_button'] = '立即使用支付寶支付';

//$_LANG['alipay_pay_method'] = '接口類型';
//$_LANG['alipay_pay_method_desc'] = '';
//$_LANG['alipay_pay_method_range'][0] = '免費簽約接口';
//$_LANG['alipay_pay_method_range'][1] = '預付費簽約接口';

$_LANG['alipay_virtual_method'] = '選擇虛擬商品接口';
$_LANG['alipay_virtual_method_desc'] = '您可以選擇支付時採用的接口類型，不過這和支付寶的帳號類型有關，具體情況請咨詢支付寶';
$_LANG['alipay_virtual_method_range'][0] = '使用普通虛擬商品交易接口';
$_LANG['alipay_virtual_method_range'][1] = '使用即時到帳交易接口';

$_LANG['alipay_real_method'] = '選擇實體商品接口';
$_LANG['alipay_real_method_desc'] = '您可以選擇支付時採用的接口類型，不過這和支付寶的帳號類型有關，具體情況請咨詢支付寶';
$_LANG['alipay_real_method_range'][0] = '使用普通實物商品交易接口';
$_LANG['alipay_real_method_range'][1] = '使用即時到帳交易接口';

$_LANG['is_instant'] = '是否開通即時到帳';
$_LANG['is_instant_desc'] = '即時到帳功能默認未開通，當您確認您的帳號已經開通該功再選擇已開通。當你選擇未開通時，所有交易使用普通實體商品接口';
$_LANG['is_instant_range'][0] = '未開通';
$_LANG['is_instant_range'][1] = '已經開通';

?>