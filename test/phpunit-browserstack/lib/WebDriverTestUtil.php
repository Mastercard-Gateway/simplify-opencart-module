<?php

require 'vendor/autoload.php';

class WebDriverTestUtil {

    private $webDriver;
    private $defaultTimeout;
    private $defaultInterval;

    public function __construct(WebDriver $driver, $defaultTimeout = 0, $defaultInterval = 0) {
        $this->webDriver = $driver;
        $this->defaultTimeout = $defaultTimeout;
        $this->defaultInterval = $defaultInterval;
        $this->webDriver->manage()->timeouts()->implicitlyWait = 120;
        $this->webDriver->manage()->timeouts()->pageLoadTimeout = 120;
    }

    function get($url) {
        return $this->webDriver->get($url);
    }
    function getFirstElement(WebDriverBy $webDriverBy) {
        $elements = $this->findElementsBy($webDriverBy);
        return $elements[0];
    }

    function waitForTitle($text) {
        $this->doWait()->until(function($driver) use ($text) {
            try {
                return $driver->getTitle() === $text;
            } catch(Exception $e) {
                return FALSE;
            }
        }, "Timeout waiting for title: " . $text);
    }

    function waitForText($text) {
        $this->doWait()->until(function($driver) use ($text) {
            try {
                $element = $driver->findElement(WebDriverBy::tagName("body"));
                return strpos($element->getText(), $text) !== FALSE;
            } catch(Exception $e) {
                return FALSE;
            }
        }, "Timeout waiting for text: " . $text);
    }

    function isElementPresent(WebDriverBy $webDriverBy) {
        try {
            return $this->webDriver->findElement($webDriverBy) !== NULL;
        } catch(Exception $e) {
            echo 'Caught exception in isElementPresent: ',  $e->getMessage(), "\n";
            return FALSE;
        }
    }

    function findElementsBy(WebDriverBy $webDriverBy) {
        $elements = NULL;
        $this->doWait()->until(function($driver) use ($webDriverBy, &$elements) {
            try {
                $elements = $driver->findElements($webDriverBy);
                return TRUE;
            } catch(Exception $e) {
                echo 'Caught exception in findElementsBy: ',  $e->getMessage(), "\n";
                return FALSE;
            }
        }, "Timeout waiting to find elements by: {$webDriverBy->getMechanism()} value: {$webDriverBy->getValue()}");
        return $elements;
    }

    function findChildElementsBy(WebDriverElement $parentElement, WebDriverBy $webDriverBy) {
        $elements = NULL;
        $this->doWait()->until(function($driver) use ($parentElement, $webDriverBy, &$elements) {
            try {
                $elements = $parentElement->findElements($webDriverBy);
                return TRUE;
            } catch(Exception $e) {
                echo 'Caught exception in findChildElementsBy: ',  $e->getMessage(), "\n";
                return FALSE;
            }
        }, "Timeout waiting to find elements by: {$webDriverBy->getMechanism()} value: {$webDriverBy->getValue()}");
        return $elements;
    }

    function findElementBy(WebDriverBy $webDriverBy) {
        $element = NULL;
        $this->doWait()->until(function($driver) use ($webDriverBy, &$element) {
            try {
                $element = $driver->findElement($webDriverBy);
                return TRUE;
            } catch(Exception $e) {
                echo 'Caught exception in findElementBy: ',  $e->getMessage(), "\n";
                return FALSE;
            }
        }, "Timeout waiting to find element by: {$webDriverBy->getMechanism()} value: {$webDriverBy->getValue()}");
        return $element;
    }

    function findChildElementBy(WebDriverElement $parentElement, WebDriverBy $webDriverBy) {
        $element = NULL;
        $this->doWait()->until(function($driver) use ($parentElement, $webDriverBy, &$element) {
            try {
                $element = $parentElement->findElement($webDriverBy);
                return TRUE;
            } catch(Exception $e) {
                echo 'Caught exception in findChildElementBy: ',  $e->getMessage(), "\n";
                return FALSE;
            }
        }, "Timeout waiting to find element by: {$webDriverBy->getMechanism()} value: {$webDriverBy->getValue()}");
        return $element;
    }

    function doWait() {
        return $this->webDriver->wait($this->defaultTimeout, $this->defaultInterval);
    }
}

