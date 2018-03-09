<?php
namespace AppBundle\AMP\Pass;

use Lullabot\AMP\Pass\ImgTagTransformPass;
use Lullabot\AMP\Validate\CssLengthAndUnit;
use Lullabot\AMP\Validate\Scope;
use Lullabot\AMP\Utility\ActionTakenLine;
use Lullabot\AMP\Utility\ActionTakenType;

use QueryPath\DOMQuery;


class AppImgTagTransformPass extends ImgTagTransformPass
{
    /**
     * @var boolean
     */
    protected $isHeader;

    /**
     * @var int
     */
    protected $tabIndex = 0;

    function pass()
    {
        // Always make sure we do this. Somewhat of a hack
        if ($this->context->getErrorScope() == Scope::HTML_SCOPE) {
            $this->q->find('html')->attr('amp', '');
        }
        // get first <p> in html-document
        $first_p_line = $this->q->top()->find('p:first-of-type')->get(0);
        // get all images
        $all_a = $this->q->top()->find('img:not(noscript img)');
        /** @var DOMQuery $el */
        foreach ($all_a as $el) {
            /** @var \DOMElement $dom_el */
            $dom_el = $el->get(0); 

            // set isHeader flag if curent image is child of first <p>
            $this->isHeader = false;
            $cur_parent = $dom_el->parentNode;
            while (!empty($cur_parent) && !$this->isHeader) {
                $this->isHeader = $first_p_line === $cur_parent ? true : false;
                $cur_parent = $cur_parent->parentNode;
            }

            if ($this->isSvg($dom_el)) {
                // @TODO This should be marked as a validation warning later?
                continue;
            }

            $lineno = $this->getLineNo($dom_el);
            $context_string = $this->getContextString($dom_el);
            $has_height_and_width = $this->setResponsiveImgHeightAndWidth($el);
            if (!$has_height_and_width) {
                $this->addActionTaken(new ActionTakenLine('img', ActionTakenType::IMG_COULD_NOT_BE_CONVERTED, $lineno, $context_string));
                continue;
            }
            if ($this->isPixel($el)) {
                $new_dom_el = $this->convertAmpPixel($el, $lineno, $context_string);
            } else if (!empty($this->options['use_amp_anim_tag']) && $this->isAnimatedImg($dom_el)) {
                $new_dom_el = $this->convertAmpAnim($el, $lineno, $context_string);
            } else {
                $new_dom_el = $this->convertAmpImg($el, $lineno, $context_string);
            }
            // add additional attributes to new element
            $this->setAddAttributes($new_dom_el);

            $this->context->addLineAssociation($new_dom_el, $lineno);
            $el->remove(); // remove the old img tag
        }

        return $this->transformations;
    }

    /**
     * Given an image src attribute, try to get its dimensions
     * Returns false on failure
     *
     * @param string $src
     * @param DOMQuery $el
     * @return bool|array
     */
    protected function getImageWidthHeight($src, $el = null)
    {
        $img_url = $this->getImageUrl($src);

        if ($img_url === false) {
            return false;
        }

        // Try obtaining image size without having to download the whole image
        $size = $this->fastimage->getImageSize($img_url);
        if ($size === false) {
            $size = [];
        }

        // Try obtaining image size from element inline styles
        if (isset($el)) {
            $style = $el->css();
            if (!empty($style)) {
                // XXX: Is this sufficient?
                $css = [];
                $style_array = explode(';', $style);
                foreach ($style_array as $item) {
                  $item = trim($item);

                  // Skip empty attributes.
                  if (strlen($item) == 0) continue;

                  list($css_att, $css_val) = explode(':',$item, 2);
                  $css[$css_att] = trim($css_val);
                }
                if (array_key_exists('width', $css) && array_key_exists('height', $css) && !empty($css['height']) && substr($css['height'], strlen($css['height']) - 2) == 'px' && !empty($css['width']) && substr($css['width'], strlen($css['width']) - 2) == 'px') {
                    $imageWidth = substr($css['width'], 0, strlen($css['width'] - 2));
                    $imageHeight = substr($css['height'], 0, strlen($css['height'] - 2));
                    if (!empty($imageWidth) && !empty($imageHeight)) {
                        $size['width'] = intval($imageWidth);
                        $size['height'] = intval($imageHeight);
                    }
                }
            }
        }

        return empty($size) ? false : $size;
    }
 /**
     * @param DOMQuery $el
     * @return bool
     */
    protected function setResponsiveImgHeightAndWidth(DOMQuery $el)
    {
        // Static cache
        static $image_dimensions_cache = [];
        $wcss = new CssLengthAndUnit($el->attr('width'), false);
        $hcss = new CssLengthAndUnit($el->attr('height'), false);

        if ($wcss->is_set && $wcss->is_valid && $hcss->is_set && $hcss->is_valid && $wcss->unit == $hcss->unit) {
            return true;
        }

        $src = trim($el->attr('src'));
        if (empty($src)) {
            return false;
        }

        if (isset($image_dimensions_cache[$src])) {

            $dimensions = $image_dimensions_cache[$src];
        } else {
            $dimensions = $this->getImageWidthHeight($src, $el);

        }
        if ($dimensions !== false) {
            $image_dimensions_cache[$src] = $dimensions;
            $el->attr('width', $dimensions['width']);
            $el->attr('height', $dimensions['height']);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add additional attributes to $el
     *
     * @param DOMQuery $el
     * @author Michael Strohyi
     **/
    protected function setAddAttributes($el)
    {        
        if ($this->isHeader) { 
            $el->setAttribute('sizes', '(min-width: 320px) 20vw, 60px');
        }
        else {
            $el_width = $el->getAttribute('width');
            if (empty($el_width)) {
                $el_width = 400;
            }
            $el->setAttribute('sizes', '(min-width: ' . $el_width . 'px) ' . $el_width . 'px, 100vw');
        }
        $el->setAttribute('on', 'tap:lightbox1');
        $el->setAttribute('role', 'button');
        $el->setAttribute('tabindex', $this->tabIndex++);
    }
}
