<?php

namespace tests;

use alexantr\elfinder\ElFinder;
use alexantr\elfinder\ElFinderAsset;
use Yii;

class ElFinderTest extends TestCase
{
    /**
     * @expectedException \yii\base\InvalidConfigException
     */
    public function testEmptyConnectorRoute()
    {
        $view = $this->mockView();

        ElFinder::widget([
            'view' => $view,
            'id' => 'test',
        ]);
    }

    public function testRender()
    {
        $view = $this->mockView();

        $out = ElFinder::widget([
            'view' => $view,
            'id' => 'test',
            'connectorRoute' => '/elfinder/connector',
        ]);
        $expected = '<div id="test"></div>';

        $this->assertEqualsWithoutLE($expected, $out);
    }

    public function testAssetsAndLangParamFromApp()
    {
        Yii::$app->language = 'be-BY';
        $view = $this->mockView();

        $out = $view->renderFile('@alexantr/elfinder/views/elfinder.php', [
            'connectorRoute' => '/elfinder/connector',
            'settings' => [],
        ]);

        $this->assertContains('"customData":{"_csrf":"', $out);

        $this->assertContains('/js/i18n/elfinder.ru.js', $out);
        $this->assertContains('"lang":"ru"', $out); // has lang param
    }

    public function testLangParamJa()
    {
        $view = $this->mockView();

        $out = $view->renderFile('@alexantr/elfinder/views/elfinder.php', [
            'connectorRoute' => '/elfinder/connector',
            'settings' => [
                'lang' => 'ja',
            ],
        ]);

        $this->assertContains('/js/i18n/elfinder.jp.js', $out);
        $this->assertContains('"lang":"jp"', $out);
    }

    public function testLangParamPt()
    {
        $view = $this->mockView();

        $out = $view->renderFile('@alexantr/elfinder/views/elfinder.php', [
            'connectorRoute' => '/elfinder/connector',
            'settings' => [
                'lang' => 'pt',
            ],
        ]);

        $this->assertContains('/js/i18n/elfinder.pt_BR.js', $out);
        $this->assertContains('"lang":"pt_BR"', $out);
    }

    public function testLangParamUg()
    {
        $view = $this->mockView();

        $out = $view->renderFile('@alexantr/elfinder/views/elfinder.php', [
            'connectorRoute' => '/elfinder/connector',
            'settings' => [
                'lang' => 'ug',
            ],
        ]);

        $this->assertContains('/js/i18n/elfinder.ug_CN.js', $out);
        $this->assertContains('"lang":"ug_CN"', $out);
    }

    public function testLangParamZh()
    {
        $view = $this->mockView();

        $out = $view->renderFile('@alexantr/elfinder/views/elfinder.php', [
            'connectorRoute' => '/elfinder/connector',
            'settings' => [
                'lang' => 'zh',
            ],
        ]);

        $this->assertContains('/js/i18n/elfinder.zh_CN.js', $out);
        $this->assertContains('"lang":"zh_CN"', $out);
    }

    public function testLangParamZhTw()
    {
        $view = $this->mockView();

        $out = $view->renderFile('@alexantr/elfinder/views/elfinder.php', [
            'connectorRoute' => '/elfinder/connector',
            'settings' => [
                'lang' => 'zh-TW',
            ],
        ]);

        $this->assertContains('/js/i18n/elfinder.zh_TW.js', $out);
        $this->assertContains('"lang":"zh_TW"', $out);
    }

    public function testDisabledLangParam()
    {
        $view = $this->mockView();

        $out = $view->renderFile('@alexantr/elfinder/views/elfinder.php', [
            'connectorRoute' => '/elfinder/connector',
            'settings' => [
                'lang' => false,
            ],
        ]);

        $this->assertNotContains('/js/i18n/elfinder', $out); // no lang file
        $this->assertNotContains('"lang":', $out); // no lang param
    }

    public function testUnknownLangParam()
    {
        $view = $this->mockView();

        $out = $view->renderFile('@alexantr/elfinder/views/elfinder.php', [
            'connectorRoute' => '/elfinder/connector',
            'settings' => [
                'lang' => 'xx',
            ],
        ]);

        $this->assertNotContains('/js/i18n/elfinder', $out); // no lang file
        $this->assertNotContains('"lang":', $out); // no lang param
    }

    public function testDefaultHeightParam()
    {
        $view = $this->mockView();

        $out = $view->renderFile('@alexantr/elfinder/views/elfinder.php', [
            'connectorRoute' => '/elfinder/connector',
            'settings' => [],
        ]);

        $this->assertRegExp('/(' . preg_quote('"height":"100%"') . ')|(' . preg_quote('jQuery(window).height() - 2') . ')/', $out);
    }

    public function testDisabledHeightParam()
    {
        $view = $this->mockView();

        $out = $view->renderFile('@alexantr/elfinder/views/elfinder.php', [
            'connectorRoute' => '/elfinder/connector',
            'settings' => [
                'height' => false,
            ],
        ]);

        $this->assertNotContains('"height":', $out);
    }

    public function testButtonNoConflictEnabled()
    {
        $view = $this->mockView();

        $content = ElFinder::widget([
            'view' => $view,
            'id' => 'test',
            'connectorRoute' => '/elfinder/connector',
            'buttonNoConflict' => true,
        ]);

        $out = $view->renderFile('@tests/data/views/layout.php', [
            'content' => $content,
        ]);

        $this->assertContains('if (jQuery.fn.button.noConflict) { jQuery.fn.btn = jQuery.fn.button.noConflict(); }', $out);
    }

    public function testPaths()
    {
        $view = $this->mockView();

        $bundle = ElFinderAsset::register($view);

        $out = $view->renderFile('@alexantr/elfinder/views/elfinder.php', [
            'connectorRoute' => '/elfinder/connector',
            'settings' => [],
        ]);

        $this->assertContains('"baseUrl":"' . $bundle->baseUrl, $out);
        $this->assertContains('"soundPath":"' . $bundle->baseUrl . '/sounds"', $out);
    }
}
