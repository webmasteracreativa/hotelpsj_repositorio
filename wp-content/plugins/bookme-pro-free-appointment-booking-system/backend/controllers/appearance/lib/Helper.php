<?php
namespace BookmePro\Backend\Controllers\Appearance\Lib;

class Helper
{
    /**
     * Render editable string (single line).
     *
     * @param array $options
     */
    public static function renderString(array $options, $placement = 'top')
    {
        self::_renderEditable($options, 'span', $placement);
    }

    /**
     * Render editable label.
     *
     * @param array $options
     */
    public static function renderLabel(array $options, $placement = 'top')
    {
        self::_renderEditable($options, 'label', $placement);
    }

    /**
     * Render editable text (multi-line).
     *
     * @param string $option_name
     * @param string $codes
     * @param string $placement
     * @param string $title
     */
    public static function renderText($option_name, $codes = '', $placement = 'bottom', $title = '')
    {
        $option_value = get_option($option_name);

        printf('<span class="bookme-pro-js-editable bookme-pro-js-option %s editable-pre-wrapped" data-type="bookme_pro" data-fieldType="textarea" data-values="%s" data-codes="%s" data-title="%s" data-placement="%s">%s</span>',
            $option_name,
            esc_attr(json_encode(array($option_name => $option_value))),
            esc_attr($codes),
            esc_attr($title),
            $placement,
            esc_html($option_value)
        );
    }

    /**
     * Render editable element.
     *
     * @param array $options
     * @param string $tag
     */
    private static function _renderEditable(array $options, $tag, $placement)
    {
        $data = array();
        foreach ($options as $option_name) {
            $data[$option_name] = get_option($option_name);
        }

        printf('<%s class="bookme-pro-js-editable bookme-pro-js-option %s" data-type="bookme_pro" data-values="%s" data-placement="%s">%s</%s>',
            $tag,
            $options[0],
            esc_attr(json_encode($data)),
            $placement,
            esc_html($data[$options[0]]),
            $tag
        );
    }
}