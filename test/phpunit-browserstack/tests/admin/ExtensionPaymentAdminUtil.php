<?php
require 'vendor/autoload.php';

class ExtensionPaymentAdminUtil {

    private $driverTestUtil;

    public function __construct($driverTestUtil) {
        $this->driverTestUtil = $driverTestUtil;
    }

    public function updateToStandardPaymentMode() {
        $this->updatePaymentMode('0');
    }

    public function updateToHostedPaymentMode() {
        $this->updatePaymentMode('1');
    }

    private function updatePaymentMode($value) {
        $this->login();

        $this->gotoExtensionEditPaymentPage();

        // check form is available
        $paymentFormElement = $this->driverTestUtil->findElementBy(WebDriverBy::xpath("//form[contains(@action, 'payment/simplifycommerce')]"));

        $this->selectPaymentMode($value);


        $paymentFormElement->submit();

        $this->driverTestUtil->waitForText('Success: You have modified Simplify Commerce account details!');

        $this->logout();

    }

    function selectStandardPaymentMode() {
        $this->selectPaymentMode('0');
    }

    function selectHostedPaymentMode() {
        $this->selectPaymentMode('1');
    }

    private function selectPaymentMode($value) {

        $inputPaymentFormElement = $this->driverTestUtil->findElementBy(WebDriverBy::xpath("//input[@name='simplifycommerce_payment_mode' and @value='{$value}']"));
        $inputPaymentFormElement->click();

    }

    function install() {
        $this->driverTestUtil->get("http://localhost:8080/install");

        $this->driverTestUtil->findElementBy(WebDriverBy::xpath("//input[@type='submit' and @value='Continue']"));

        if ($this->driverTestUtil->isElementPresent(WebDriverBy::xpath("//h3[text()='Upgrade Progress']"))) {
            return;
        }

        $this->driverTestUtil->waitForText('License agreement');
        $formElement = $this->driverTestUtil->findElementBy(WebDriverBy::xpath("//form[contains(@action, 'route=install/step_1')]"));
        $formElement->submit();

        $this->driverTestUtil->waitForText('Pre-Installation');
        $formElement = $this->driverTestUtil->findElementBy(WebDriverBy::xpath("//form[contains(@action, 'route=install/step_2')]"));
        $this->driverTestUtil->findChildElementBy($formElement, WebDriverBy::xpath("//input[@type='submit' and @value='Continue']"));
        $formElement->submit();

        $this->driverTestUtil->waitForText('Configuration');
        $formElement = $this->driverTestUtil->findElementBy(WebDriverBy::xpath("//form[contains(@action, 'route=install/step_3')]"));
        $this->driverTestUtil->findChildElementBy($formElement, WebDriverBy::xpath("//input[@type='submit' and @value='Continue']"));

        $inputElement = $this->driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("input-db-hostname"));
        $inputElement->clear();
        $inputElement->sendKeys("mysql");

        $inputElement = $this->driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("input-db-password"));
        $inputElement->clear();
        $inputElement->sendKeys("rootpwd");

        $inputElement = $this->driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("input-db-database"));
        $inputElement->clear();
        $inputElement->sendKeys("opencart");

        $inputElement = $this->driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("input-password"));
        $inputElement->clear();
        $inputElement->sendKeys("admin");

        $inputElement = $this->driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("input-email"));
        $inputElement->clear();
        $inputElement->sendKeys("fuelable@cubecookie.com");

        $inputElement->submit();

        $this->driverTestUtil->waitForText('Installation complete');
//        $this->driverTestUtil->findElementBy("//a[text()='Login to your Administration'");
    }

    function login() {
        $this->driverTestUtil->get("http://localhost:8080/admin");

        $this->driverTestUtil->waitForText('Please enter your login details.');

        $userNameElement = $this->driverTestUtil->findElementBy(WebDriverBy::id("input-username"));
        $passwordElement = $this->driverTestUtil->findElementBy(WebDriverBy::id("input-password"));

        $userNameElement->clear();
        $userNameElement->sendKeys("admin");
        $passwordElement->clear();
        $passwordElement->sendKeys("admin");
        $passwordElement->submit();

        $this->driverTestUtil->waitForText('Dashboard');
    }

    function gotoExtensionEditPaymentPage() {
        $menuExtensionsElement = $this->driverTestUtil->findElementBy(WebDriverBy::id("menu-extension"));
        $menuExtensionsElement->click(); // open menu
        $menuExtensionsElement = $this->driverTestUtil->findElementBy(WebDriverBy::xpath("//a[text()='Extensions']"));
        $menuExtensionsElement->click();

        $this->driverTestUtil->waitForText('Extension List');

        $selectExtensionElement = $this->driverTestUtil->findElementBy(WebDriverBy::name("type"));
        $optionPayment = $this->driverTestUtil->findChildElementBy($selectExtensionElement, WebDriverBy::xpath("option[contains(@value,'payment')]"));
        $optionPayment->click();

        $this->driverTestUtil->waitForText('Payment Method');

        try {
            if (!$this->driverTestUtil->isElementPresent(WebDriverBy::xpath("//a[contains(@href, 'payment/simplifycommerce')]"))) {
                $extensionPaymentInstallElement = $this->driverTestUtil->findElementBy(WebDriverBy::xpath("//a[contains(@href, 'extension=simplifycommerce') and contains(@href, 'payment/install')]"));
                $extensionPaymentInstallElement->click();
            }
        } catch(TimeOutException $e) {
            echo 'Caught TimeOutException: ',  $e->getMessage(), "\n";
            echo 'Simplify Commerce Payment Extension must have been installed already';
        }

        $extensionPaymentEditElement = $this->driverTestUtil->findElementBy(WebDriverBy::xpath("//a[contains(@href, 'payment/simplifycommerce')]"));
        $extensionPaymentEditElement->click();

        $this->driverTestUtil->waitForText('Edit Simplify Commerce');
    }

    function logout() {
        $logoutElement = $this->driverTestUtil->findElementBy(WebDriverBy::xpath("//a[contains(@href, 'route=common/logout')]"));
        $logoutElement->click();
        $this->driverTestUtil->waitForText('Please enter your login details.');
    }
}

