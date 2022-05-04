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
    public string $schemaVersion = '1.0.0';

    /**
     * @var bool
     */
    public bool $hasCpSettings = true;

    /**
     * @var bool
     */
    public bool $hasCpSection = false;

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

        Craft::info($this->name . ' plugin loaded', __METHOD__);

        Craft::$app->getView()->hook(
            'cp.layouts.base',
            static function(array &$context) {
                $siteHandle    = Craft::$app->request->getQueryParam('site');

                $section = Craft::$app->request->getSegment(1);
                $entry   = Craft::$app->request->getSegment(3);

                if ('entries' === $section && $entry) {
                    if (!$siteHandle) {
                        $site = Craft::$app->getSites()->getPrimarySite();
                    } else {
                        $site = Craft::$app->getSites()->getSiteByHandle($siteHandle);
                    }

                    array_push($context['bodyAttributes']['class'], 'site--' . $site->handle, 'site--' . $site->language);
                }
            }
        );

        $this->addIconCssToView();
    }

    // Protected Methods
    // =========================================================================

    protected function getCss(string $key): string
    {
        $icons = Craft::$app->config->getConfigFromFile('cp-site-icons');

        if (! array_key_exists('icons', $icons)) {
            Craft::$app->session->setError('Please add config/cp-site-icons.php. Read more: https://github.com/nthmedia/cp-site-icons');
            return '';
        }

        if (array_key_exists($key, $icons['icons']) === false) {
            return '';
        }

        if (is_array($icons['icons'][$key])) {
            $output = '';
            foreach ($icons['icons'][$key] as $property => $value) {
                $output .= $property . ": " . $value . ';';
            }
            return $output;
        } else {
            $output = 'content: "' . $icons['icons'][$key] .'";';
        }

        return $output ?? '';
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
                            ' . $this->getCss($site->{$key}) . '
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
    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate(
            'cp-site-icons/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}