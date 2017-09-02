<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 27.08.2017
 * Time: 22:24
 */

namespace app\components;


use SimpleXMLElement;
use SoapHeader;
use SoapVar;
use Yii;

class SberbankClient
{
    private $login = 'kupi-api';
    private $password = 'kupi';
    private $url = "https://3dsec.sberbank.ru/payment/webservices/merchant-ws?wsdl";

    private function getSoapHeaderWSSecurity()
    {
        //namespaces
        $nsWsse = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        $nsWsu = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';
        $passwordType = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText';

        //формируем xml
        $root = new SimpleXMLElement('<root/>');

        $security = $root->addChild('wsse:Security', null, $nsWsse);
        $security->registerXPathNamespace('wsu', $nsWsu);
        $root->registerXPathNamespace('wsse', $nsWsse);

        $usernameToken = $security->addChild('wsse:UsernameToken', null, $nsWsse);
        $usernameToken->addChild('wsse:Username', $this->login, $nsWsse);
        $usernameToken->addChild('wsse:Password', $this->password, $nsWsse)->addAttribute('Type', $passwordType);

        //берем из xml только security
        $secutiryXml = $root->xpath('/root/wsse:Security');

        //формируем заголовок
        return new SoapHeader($nsWsse, 'Security', new SoapVar($secutiryXml[0]->asXML(), XSD_ANYXML), true);
    }

    public function registerOrder($orderId, $description, $amount)
    {
        //создаем клиента для подключения к wsdl
        $client = new \SoapClient($this->url, ["cache_wsdl" => 0, "trace" => 1, "exceptions" => 0]);
        //задаем заголовок
        $client->__setSoapHeaders($this->getSoapHeaderWSSecurity());

        //формируем xml
        $root = new SimpleXMLElement('<root/>');

        //параметры берутся из документации
        $order = $root->addChild('order', null, null);
        $order->addAttribute('merchantOrderNumber', $orderId);
        $order->addAttribute('description', $description);
        $order->addAttribute('amount', $amount);
        $order->addChild('returnUrl', Yii::$app->urlManager->createAbsoluteUrl('profile/payment-done'), null);

        $orderXml = $root->xpath('/root/order');
        $orderVar = new SoapVar($orderXml[0]->asXML(), XSD_ANYXML);

        try{
            //отправляем запрос, registerOrder - метод, который предоставляет wsdl
            /**
             * @var $result \stdClass
             */
            $result = $client->registerOrder($orderVar);
//            Yii::$app->session->addFlash('warning', "Код возврата  $result->errorCode: $result->errorMessage");
            if($result->errorCode == 0){
                return $result;
            } else {
                Yii::$app->session->addFlash('error','Система вернула ошибку: ' . $result->errorCode . $result->errorMessage);
            }
        } catch (\SoapFault $ex){
            Yii::$app->session->addFlash('error','Система вернула ошибку: ' . json_encode($ex->getCode()) . $ex->getMessage());
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getStatusOrder($orderId)
    {
        //создаем клиента для подключения к wsdl
        $client = new \SoapClient($this->url, ["cache_wsdl" => 0, "trace" => 1, "exceptions" => 0]);
        //задаем заголовок
        $client->__setSoapHeaders($this->getSoapHeaderWSSecurity());

        //формируем xml
        $root = new SimpleXMLElement('<root/>');

        //параметры, которые необходимо указывать, берем из документации
        $order = $root->addChild('order', null, null);
        $order->addAttribute('orderId', $orderId);
        $order->addAttribute('language', 'ru');

        $orderXml = $root->xpath('/root/order');

        $orderVar = new SoapVar($orderXml[0]->asXML(), XSD_ANYXML);

        try{
            //отправляем запрос, getOrderStatus - метод, который предоставляет wsdl
            $result = $client->getOrderStatus($orderVar);
            return $result;
        } catch (\SoapFault $ex){
            Yii::$app->session->addFlash('error','Система вернула ошибку ' . json_encode($ex->getCode()) . ': '. $ex->getMessage());
        }

        return false;
    }
}
