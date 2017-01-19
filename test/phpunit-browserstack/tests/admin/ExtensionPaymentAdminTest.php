<?php
require 'vendor/autoload.php';
require 'ExtensionPaymentAdminUtil.php';

class ExtensionPaymentAdminTest extends BrowserStackTest {

    private static $extensionPaymentAdminUtil;

    protected function setUp() {
        self::$extensionPaymentAdminUtil = new ExtensionPaymentAdminUtil(self::$driverTestUtil);

        self::$extensionPaymentAdminUtil->install();
    }

    public function testStandardPaymentAdmin() {
        self::$extensionPaymentAdminUtil->login();
        self::$extensionPaymentAdminUtil->gotoExtensionEditPaymentPage();

        $editPaymentFormElement = self::$driverTestUtil->findElementBy(WebDriverBy::xpath("//form[contains(@action, 'payment/simplifycommerce')]"));
        $inputPaymentFormElement = self::$driverTestUtil->findChildElementBy($editPaymentFormElement, WebDriverBy::xpath("//input[@name='simplifycommerce_test' and @value='1']"));
        $inputPaymentFormElement->click();

        self::$extensionPaymentAdminUtil->selectStandardPaymentMode();

        $inputPaymentFormElement = self::$driverTestUtil->findChildElementBy($editPaymentFormElement, WebDriverBy::name("simplifycommerce_testsecretkey"));
        $inputPaymentFormElement->clear();
        $inputPaymentFormElement->sendKeys("JCsKX27aUPmBE5UOTJRavvWGPyeGCU5tGb4WWdicOP95YFFQL0ODSXAOkNtXTToq");

        $inputPaymentFormElement = self::$driverTestUtil->findChildElementBy($editPaymentFormElement, WebDriverBy::name("simplifycommerce_testpubkey"));
        $inputPaymentFormElement->clear();
        $inputPaymentFormElement->sendKeys("sbpb_NmQyMzU3NmYtZDUyMC00M2MyLWI3ODAtMDczZmUxNDNlODYz");

        $inputPaymentFormElement = self::$driverTestUtil->findChildElementBy($editPaymentFormElement, WebDriverBy::name("simplifycommerce_title"));
        $inputPaymentFormElement->clear();
        $inputPaymentFormElement->sendKeys("Pay by Simplify");

        $inputPaymentFormElement = self::$driverTestUtil->findChildElementBy($editPaymentFormElement, WebDriverBy::name("simplifycommerce_status"));
        $optionPayment = self::$driverTestUtil->findChildElementBy($inputPaymentFormElement, WebDriverBy::xpath("option[@value='1']"));
        $optionPayment->click();

        $inputPaymentFormElement->submit();

        self::$driverTestUtil->waitForText('Success: You have modified Simplify Commerce account details!');

        self::$extensionPaymentAdminUtil->logout();

    }

    public function testHostedPaymentAdmin() {
        self::$extensionPaymentAdminUtil->login();
        self::$extensionPaymentAdminUtil->gotoExtensionEditPaymentPage();

        $editPaymentFormElement = self::$driverTestUtil->findElementBy(WebDriverBy::xpath("//form[contains(@action, 'payment/simplifycommerce')]"));
        $inputPaymentFormElement = self::$driverTestUtil->findChildElementBy($editPaymentFormElement, WebDriverBy::xpath("//input[@name='simplifycommerce_test' and @value='1']"));
        $inputPaymentFormElement->click();

        self::$extensionPaymentAdminUtil->selectHostedPaymentMode();

        $inputPaymentFormElement = self::$driverTestUtil->findChildElementBy($editPaymentFormElement, WebDriverBy::name("simplifycommerce_testsecretkey"));
        $inputPaymentFormElement->clear();
        $inputPaymentFormElement->sendKeys("JCsKX27aUPmBE5UOTJRavvWGPyeGCU5tGb4WWdicOP95YFFQL0ODSXAOkNtXTToq");

        $inputPaymentFormElement = self::$driverTestUtil->findChildElementBy($editPaymentFormElement, WebDriverBy::name("simplifycommerce_testpubkey"));
        $inputPaymentFormElement->clear();
        $inputPaymentFormElement->sendKeys("sbpb_NmQyMzU3NmYtZDUyMC00M2MyLWI3ODAtMDczZmUxNDNlODYz");

        $inputPaymentFormElement = self::$driverTestUtil->findChildElementBy($editPaymentFormElement, WebDriverBy::name("simplifycommerce_title"));
        $inputPaymentFormElement->clear();
        $inputPaymentFormElement->sendKeys("Pay by Simplify");

        $inputPaymentFormElement = self::$driverTestUtil->findChildElementBy($editPaymentFormElement, WebDriverBy::name("simplifycommerce_status"));
        $optionPayment = self::$driverTestUtil->findChildElementBy($inputPaymentFormElement, WebDriverBy::xpath("option[@value='1']"));
        $optionPayment->click();

        $inputPaymentFormElement->submit();

        self::$driverTestUtil->waitForText('Success: You have modified Simplify Commerce account details!');

        self::$extensionPaymentAdminUtil->logout();
    }
}

