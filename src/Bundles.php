<?php
/**
 * Bundles plugin for Craft CMS 3.x
 *
 * Craft Commerce Bundles Plugin
 *
 * @link      https://myth.digital/
 * @copyright Copyright (c) 2020 Myth Digital
 */

namespace mythdigital\bundles;

use mythdigital\bundles\services\BundleService as BundleService;
use mythdigital\bundles\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class Bundles
 *
 * @author    Myth Digital
 * @package   Bundles
 * @since     1.0.0
 *
 * @property  BundleServiceService $bundleService
 */
class Bundles extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Bundles
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'bundles' => BundleService::class
        ]);

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['bundles/'] = 'bundles/default/index';
                $event->rules['bundles/new'] = 'bundles/default/edit';
                $event->rules['bundles/<id:\d+>'] = 'bundles/default/edit';
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'bundles',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * Returns the Bundles service
     *
     * @return BundleService The variants service
     */
    public function getBundles(): BundleService
    {
        return $this->get('bundles');
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'bundles/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
