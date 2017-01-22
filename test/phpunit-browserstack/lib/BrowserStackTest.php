<?php

require 'vendor/autoload.php';
require 'lib/globals.php';
require 'lib/WebDriverTestUtil.php';

class BrowserStackTest extends PHPUnit_Framework_TestCase
{
    protected static $driver;
    protected static $bs_local;
    protected static $driverTestUtil;

    public static function setUpBeforeClass()
    {
        $CONFIG = $GLOBALS['CONFIG'];
        $task_id = getenv('TASK_ID') ? getenv('TASK_ID') : 0;

        $url = "https://" . $GLOBALS['BROWSERSTACK_USERNAME'] . ":" . $GLOBALS['BROWSERSTACK_ACCESS_KEY'] . "@" . $GLOBALS['BROWSERSTACK_SERVER'] ."/wd/hub";
        $caps = $CONFIG['environments'][$task_id];

        foreach ($CONFIG["capabilities"] as $key => $value) {
            if(!array_key_exists($key, $caps))
                $caps[$key] = $value;
        }

        if(array_key_exists("browserstack.local", $caps) && $caps["browserstack.local"])
        {
            $bs_local_args = array("key" => $GLOBALS['BROWSERSTACK_ACCESS_KEY']);
            self::$bs_local = new BrowserStack\Local();
            self::$bs_local->start($bs_local_args);
        }

        self::$driver = RemoteWebDriver::create($url, $caps);
        self::$driverTestUtil = new WebDriverTestUtil(self::$driver);
    }

    public static function tearDownAfterClass()
    {
        self::$driver->quit();
        if(self::$bs_local) self::$bs_local->stop();
    }
}
?>
