<?php

namespace YOOtheme\Theme\Wordpress;

use YOOtheme\Path;
use YOOtheme\Theme\SystemCheck as BaseSystemCheck;
use function YOOtheme\trans;

class SystemCheck extends BaseSystemCheck
{
    /**
     * @inheritdoc
     */
    public function getRecommendations()
    {
        $res = [];

        if (Path::basename('~theme') !== 'yootheme') {
            $res[] = trans(
                'The YOOtheme Pro theme folder was renamed breaking essential functionality. Rename the theme folder back to <code>yootheme</code>.',
            );
        }

        return array_merge($res, parent::getRecommendations());
    }

    protected function hasApiKey()
    {
        return $this->config->get('~theme.yootheme_apikey');
    }
}
