<?php
namespace AppBundle\AMP;

use Lullabot\AMP\AMP;

class AppAMP extends AMP
{
    public $passes = [
        'Lullabot\AMP\Pass\PreliminaryPass',
        'AppBundle\AMP\Pass\ImgTagTransformPass',
        'Lullabot\AMP\Pass\IframeSoundCloudTagTransformPass',
        'Lullabot\AMP\Pass\IframeFacebookTagTransformPass',
        'Lullabot\AMP\Pass\AudioTagTransformPass',
        'Lullabot\AMP\Pass\VideoTagTransformPass',
        'Lullabot\AMP\Pass\IframeVimeoTagTransformPass',
        'Lullabot\AMP\Pass\IframeVineTagTransformPass',
        'Lullabot\AMP\Pass\IframeDailymotionTagTransformPass',
        'Lullabot\AMP\Pass\IframeYouTubeTagTransformPass',
        'Lullabot\AMP\Pass\IframeTagTransformPass',
        'Lullabot\AMP\Pass\InstagramTransformPass',
        'Lullabot\AMP\Pass\PinterestTagTransformPass',
        'Lullabot\AMP\Pass\FacebookNonIframeTransformPass',
        'Lullabot\AMP\Pass\TwitterTransformPass',
        'Lullabot\AMP\Pass\ObjectYouTubeTagTransformPass',
        'Lullabot\AMP\Pass\ObjectVimeoTagTransformPass',
        'Lullabot\AMP\Pass\ObjectVideoTagTransformPass',
        'Lullabot\AMP\Pass\StandardScanPass',
        'Lullabot\AMP\Pass\StandardFixPass',
        'Lullabot\AMP\Pass\AmpImgFixPass',
        'Lullabot\AMP\Pass\StandardFixPassTwo',
        'Lullabot\AMP\Pass\MinimumValidFixPass',
        'Lullabot\AMP\Pass\StatisticsPass'
    ];
}