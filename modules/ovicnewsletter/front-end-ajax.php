<?php
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
require_once(dirname(__FILE__).'/ovicnewsletter.php');
$action = Tools::getValue('action');
OvicNewsletterFrontEndAjax::$action();
class OvicNewsletterFrontEndAjax{
    public static function cancelRegisNewsletter(){
        $persistent = Tools::getValue('persistent', $persistent);
        Context::getContext()->cookie->__set('persistent', 1);
        die(Tools::jsonEncode("1"));
    }
    public static function regisNewsletter(){
       
        $email = Tools::getValue('email', '');
        $persistent = Tools::getValue('persistent', 0);
        $result = array();
        if($email != ''){
            $check = DB::getInstance()->getValue("Select id From "._DB_PREFIX_."newsletter Where email = '$email'");
            if($check){
                if($persistent == 1){
                    Context::getContext()->cookie->__set('persistent', 1);
                    //setcookie('ovicnewsletter-persistent', 1, time() + (86400 * 30 * 30), _PS_BASE_URL_.__PS_BASE_URI__);
                    //$_SESSION['persistent'] = 1;
                }
                $result['status'] = false;
                    $result['msg'] = 'This email address is already registered.';
            }else{
                                    
                $sql = 'INSERT INTO '._DB_PREFIX_.'newsletter (id_shop, id_shop_group, email, newsletter_date_add, ip_registration_newsletter, http_referer, active)
				VALUES
				('.Context::getContext()->shop->id.',
				'.Context::getContext()->shop->id_shop_group.',
				\''.pSQL($email).'\',
				NOW(),
				\''.pSQL(Tools::getRemoteAddr()).'\',
				(
					SELECT c.http_referer
					FROM '._DB_PREFIX_.'connections c
					WHERE c.id_guest = '.(int)Context::getContext()->customer->id.'
					ORDER BY c.date_add DESC LIMIT 1
				),
				'.(int)$persistent.'
				)';
                
                if (DB::getInstance()->execute($sql)){
                    if($persistent == 1){
                        //$context = Context::getContext();
                        Context::getContext()->cookie->__set('persistent', 1); 
                        //setcookie('ovicnewsletter-persistent', 1, time() + (86400 * 30 * 30), _PS_BASE_URL_.__PS_BASE_URI__);
                        //$_SESSION['persistent'] = 1;
                    }
                    $result['status'] = true;
                    $result['msg'] = 'You have successfully subscribed to this newsletter.';
                }else{
                    $result['status'] = false;
                    $result['msg'] = 'An error occurred during the subscription process.';
                }
                
            }
        }
        die(Tools::jsonEncode($result));     
            
    }   
}
