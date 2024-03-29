<?php
/**
 * CP Site Icons plugin for Craft CMS 3.x
 *
 * Display site icon when editing entries
 *
 * @link      https://nthmedia.nl
 * @copyright Copyright (c) 2021 NTH media
 */

namespace nthmedia\cpsiteicons\models;

use craft\base\Model;

use nthmedia\cpsiteicons\CpSiteIcons;

/**
 * @author    NTH media
 * @package   CpSiteIcons
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public string $key = 'language';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['key', 'string'],
            ['key', 'default', 'value' => 'language'],
        ];
    }
}
