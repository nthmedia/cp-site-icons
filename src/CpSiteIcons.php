<?php
/**
 * CP Site Icons plugin for Craft CMS 3.x
 *
 * Display site icon when editing entries
 *
 * @link      https://nthmedia.nl
 * @copyright Copyright (c) 2021 NTH media
 */

namespace nthmedia\cpsiteicons;

use craft\events\TemplateEvent;
use craft\web\View;
use nthmedia\cpsiteicons\assetbundles\cpsiteicons\CpSiteIconsAsset;
use nthmedia\cpsiteicons\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;

use yii\base\Event;

/**
 * Class CpSiteIcons
 *
 * @author    NTH media
 * @package   CpSiteIcons
 * @since     1.0.0
 *
 */
class CpSiteIcons extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var CpSiteIcons
     */
    public static self $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

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
                'cp-site-icons',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );

        $this->addIconCssToView();
    }

    // Protected Methods
    // =========================================================================

    protected function getIcon(string $key): string
    {
        $icons = Craft::$app->config->getConfigFromFile('cp-site-icons');

        return $icons[$key] ?? '';
    }

    protected function addIconCssToView(): void
    {
        if (!Craft::$app->getRequest()->getIsCpRequest()) {
            return;
        }

        Event::on(
            View::class,
            View::EVENT_BEFORE_RENDER_PAGE_TEMPLATE,
            function (TemplateEvent $event) {
                $view = Craft::$app->getView();
                $view->registerAssetBundle(CpSiteIconsAsset::class);

                $key = $this->getSettings()->key;

                foreach (Craft::$app->getRequest()->sites->allSites as $site) {
                    $view->registerCss('
                        /** Icon for ' . $key . ': ' . $site->{$key} .' */
                        .site--' . $site->handle . ' #header > .flex > h1::before {
                            content: "' . $this->getIcon($site->{$key}) . '";
                        }
                    ');
                }
            }
        );
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'cp-site-icons/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
