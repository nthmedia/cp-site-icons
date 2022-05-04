<?php
/**
 * CP Site Icons plugin for Craft CMS 3.x
 *
 * Display site icon when editing entries
 *
 * @link      https://nthmedia.nl
 * @copyright Copyright (c) 2021 NTH media
 */

namespace nthmedia\cpsiteicons\assetbundles\cpsiteicons;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    NTH media
 * @package   CpSiteIcons
 * @since     1.0.0
 */
class CpSiteIconsAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@nthmedia/cpsiteicons/assetbundles/cpsiteicons/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/CpSiteIcons.js',
        ];

        $this->css = [
            'css/CpSiteIcons.css',
        ];

        parent::init();
    }
}
