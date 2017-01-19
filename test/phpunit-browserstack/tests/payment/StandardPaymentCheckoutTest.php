<?php
require 'vendor/autoload.php';
require 'tests/admin/ExtensionPaymentAdminUtil.php';

class StandardPaymentCheckoutTest extends BrowserStackTest {

    private static $extensionPaymentAdminUtil;

    protected function setUp() {
        self::$extensionPaymentAdminUtil = new ExtensionPaymentAdminUtil(self::$driverTestUtil);

        self::$extensionPaymentAdminUtil->updateToStandardPaymentMode();
    }

    public function testCheckout() {

        self::$driverTestUtil->get("http://localhost:8080");

        self::$driverTestUtil->waitForTitle('Your Store');

        $addCartElement = self::$driverTestUtil->getFirstElement(WebDriverBy::xpath("//button[@type='button' and contains(@onclick, 'cart.add')]"));
        $addCartElement->click();

        // wait till in cart
        self::$driverTestUtil->waitForText('Success: You have added');

        self::$driverTestUtil->findElementsBy(WebDriverBy::xpath("//span[@id='cart-total' and contains(text(), '1 item(s) ')]"));

        $checkoutElement = self::$driverTestUtil->findElementBy(WebDriverBy::xpath("//a[contains(@href, 'route=checkout/checkout')]"));
        $checkoutElement->click();

        self::$driverTestUtil->waitForTitle('Checkout');

        self::$driverTestUtil->findElementBy(WebDriverBy::xpath("//a[@href='#collapse-checkout-option']"));

        $formElement = self::$driverTestUtil->findElementBy(WebDriverBy::id("collapse-checkout-option"));

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::xpath("//input[@type='radio' and @name='account' and @value='guest']"));
        $inputElement->click();

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("button-account"));
        $inputElement->click();

        self::$driverTestUtil->findElementBy(WebDriverBy::xpath("//a[@href='#collapse-payment-address']"));

        $formElement = self::$driverTestUtil->findElementBy(WebDriverBy::id("collapse-payment-address"));

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("input-payment-firstname"));
        $inputElement->clear();
        $inputElement->sendKeys("Test");

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("input-payment-lastname"));
        $inputElement->clear();
        $inputElement->sendKeys("Test");

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("input-payment-email"));
        $inputElement->clear();
        $inputElement->sendKeys("test@test.com");

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("input-payment-telephone"));
        $inputElement->clear();
        $inputElement->sendKeys("3143143141");

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("input-payment-address-1"));
        $inputElement->clear();
        $inputElement->sendKeys("123 Test");

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("input-payment-city"));
        $inputElement->clear();
        $inputElement->sendKeys("Test");

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("input-payment-postcode"));
        $inputElement->clear();
        $inputElement->sendKeys("63333");

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("input-payment-country"));
        $optionPayment = self::$driverTestUtil->findChildElementBy($inputElement, WebDriverBy::xpath("option[text()='United States']"));
        $optionPayment->click();

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("input-payment-zone"));
        $optionPayment = self::$driverTestUtil->findChildElementBy($inputElement, WebDriverBy::xpath("option[text()='Missouri']"));
        $optionPayment->click();

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("button-guest"));
        $inputElement->click();

        self::$driverTestUtil->findElementBy(WebDriverBy::xpath("//a[@href='#collapse-payment-method']"));

        $formElement = self::$driverTestUtil->findElementBy(WebDriverBy::id("collapse-payment-method"));

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::xpath("//input[@type='checkbox' and @name='agree' and @value='1']"));
        if (!$inputElement->isSelected()) {
            $inputElement->click();
        }

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::xpath("//input[@name='payment_method' and @value='simplifycommerce']"));
        if (!$inputElement->isSelected()) {
            $inputElement->click();
        }

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("button-payment-method"));
        $inputElement->click();

        self::$driverTestUtil->findElementBy(WebDriverBy::xpath("//a[@href='#collapse-checkout-confirm']"));

        $formElement = self::$driverTestUtil->findElementBy(WebDriverBy::id("collapse-checkout-confirm"));

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("entry_name_on_card"));
        $inputElement->clear();
        $inputElement->sendKeys("Test Test");

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("entry_card_number"));
        $inputElement->clear();
        $inputElement->sendKeys("5555555555554444");

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("entry_card_month"));
        $optionPayment = self::$driverTestUtil->findChildElementBy($inputElement, WebDriverBy::xpath("option[text()='December']"));
        $optionPayment->click();

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("entry_card_year"));
        $optionsPayment = self::$driverTestUtil->findChildElementsBy($inputElement, WebDriverBy::tagName("option"));
        $optionsPayment[count($optionsPayment) - 1]->click();

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("entry_cvc"));
        $inputElement->clear();
        $inputElement->sendKeys("123");

        $inputElement = self::$driverTestUtil->findChildElementBy($formElement, WebDriverBy::id("button-pay"));
        $inputElement->click();

        self::$driverTestUtil->waitForTitle('Your order has been placed!');
        self::$driverTestUtil->waitForText('Your order has been successfully processed!');
    }
}

