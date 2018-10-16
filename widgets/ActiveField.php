<?php

namespace pvsaintpe\search\widgets;

use pvsaintpe\search\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * Class ActiveField
 * @package pvsaintpe\search\widgets
 */
class ActiveField extends \kartik\form\ActiveField
{
    /**
     * Renders the hint tag.
     * @param string|bool $content the hint content.
     * If `null`, the hint will be generated via [[Model::getAttributeHint()]].
     * If `false`, the generated field will not contain the hint part.
     * Note that this will NOT be [[Html::encode()|encoded]].
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     * the attributes of the hint tag. The values will be HTML-encoded using [[Html::encode()]].
     *
     * The following options are specially handled:
     *
     * - `tag`: this specifies the tag name. If not set, `div` will be used.
     *   See also [[\yii\helpers\Html::tag()]].
     *
     * @return $this the field object itself.
     */
    public function hint($content, $options = [])
    {
        if ($content === false) {
            $this->parts['{hint}'] = '';
            return $this;
        }

        $options = array_merge($this->hintOptions, $options);
        if ($content !== null) {
            $options['hint'] = $content;
        }

        $this->parts['{hint}'] = Html::activeHint($this->model, $this->attribute, $options);

        return $this;
    }

    /**
     * @param $label
     * @return mixed
     */
    protected function getFieldLabel($label)
    {
        return $label;
    }

    /**
     * @var bool if "for" field label attribute should be skipped.
     */
    protected $_skipLabelFor = false;

    /**
     * Generates a label tag for [[attribute]].
     * @param null|string|false $label the label to use. If `null`, the label will be generated via [[Model::getAttributeLabel()]].
     * If `false`, the generated field will not contain the label part.
     * Note that this will NOT be [[Html::encode()|encoded]].
     * @param null|array $options the tag options in terms of name-value pairs. It will be merged with [[labelOptions]].
     * The options will be rendered as the attributes of the resulting tag. The values will be HTML-encoded
     * using [[Html::encode()]]. If a value is `null`, the corresponding attribute will not be rendered.
     * @return $this the field object itself.
     */
    public function label($label = null, $options = [])
    {
        if ($label === false) {
            $this->parts['{label}'] = '';
            return $this;
        }

        $options = array_merge($this->labelOptions, $options);
        if ($label !== null) {
            $options['label'] = $label;
        }

        if ($this->_skipLabelFor) {
            $options['for'] = null;
        }

        $this->parts['{label}'] = Html::activeLabel($this->model, $this->attribute, $options);

        return $this;
    }

    /**
     * Generates a toggle field (checkbox or radio)
     *
     * @param string $type the toggle input type 'checkbox' or 'radio'.
     * @param array $options options (name => config) for the toggle input list container tag.
     * @param boolean $enclosedByLabel whether the input is enclosed by the label tag
     *
     * @return ActiveField object
     */
    protected function getToggleField($type = self::TYPE_CHECKBOX, $options = [], $enclosedByLabel = true)
    {
        $this->initDisability($options);
        $inputType = 'active' . ucfirst($type);
        $disabled = ArrayHelper::getValue($options, 'disabled', false);
        $css = $disabled ? $type . ' disabled' : $type;
        $container = ArrayHelper::remove($options, 'container', ['class' => $css]);
        if ($enclosedByLabel) {
            $this->_offset = true;
            $this->parts['{label}'] = '';
            $showLabels = $this->hasLabels();
            if ($showLabels === false) {
                $options['label'] = '';
                $this->showLabels = true;
            }
        } else {
            $this->_offset = false;
            if (isset($options['label']) && !isset($this->parts['{label}'])) {
                $this->parts['label'] = $options['label'];
                if (!empty($options['labelOptions'])) {
                    $this->labelOptions = $options['labelOptions'];
                }
            }
            $options['label'] = null;
            $container = false;
            unset($options['labelOptions']);
        }
        $input = Html::$inputType($this->model, $this->attribute, $options);
        if (is_array($container)) {
            $tag = ArrayHelper::remove($container, 'tag', 'div');
            $input = Html::tag($tag, $input, $container);
        }

        $this->parts['{input}'] = $input;
        if ($type == self::TYPE_CHECKBOX) {
            $this->parts['{input}'] = '<br>' . $input;
        }
        $this->adjustLabelFor($options);
        return $this;
    }

    /**
     * Render the label parts
     *
     * @param string|null $label the label or null to use model label
     * @param array $options the tag options
     */
    protected function renderLabelParts($label = null, $options = [])
    {
        $options = array_merge($this->labelOptions, $options);
        if ($label === null) {
            if (isset($options['label'])) {
                $label = $options['label'];
                unset($options['label']);
            } else {
                $attribute = Html::getAttributeName($this->attribute);
                $label = Html::encode($this->model->getAttributeLabel($attribute));
            }
        }
        if (!isset($options['for'])) {
            $options['for'] = Html::getInputId($this->model, $this->attribute);
        }
        $this->parts['{beginLabel}'] = Html::beginTag('label', $options);
        $this->parts['{endLabel}'] = Html::endTag('label');
        if (!isset($this->parts['{labelTitle}'])) {
            $this->parts['{labelTitle}'] = $this->getFieldLabel($label);
        }
    }

    /**
     * Renders a list of checkboxes / radio buttons. The selection of the checkbox / radio buttons is taken from the
     * value of the model attribute.
     *
     * @param string $type the toggle input type 'checkbox' or 'radio'.
     * @param array $items the data item used to generate the checkbox / radio buttons. The array keys are the labels,
     * while the array values are the corresponding checkbox / radio button values. Note that the labels will NOT
     * be HTML-encoded, while the values will be encoded.
     * @param array $options options (name => config) for the checkbox / radio button list. The following options are
     * specially handled:
     *
     * - `unselect`: _string_, the value that should be submitted when none of the checkbox / radio buttons is selected. By
     *   setting this option, a hidden input will be generated.
     * - `separator`: _string_, the HTML code that separates items.
     * - `inline`: _boolean_, whether the list should be displayed as a series on the same line, default is false
     * - `disabledItems`: _array_, the list of values that will be disabled.
     * - `readonlyItems`: _array_, the list of values that will be readonly.
     * - `item: callable, a callback that can be used to customize the generation of the HTML code corresponding to a
     *   single item in $items. The signature of this callback must be:
     *
     * ~~~
     * function ($index, $label, $name, $checked, $value)
     * ~~~
     *
     * where $index is the zero-based index of the checkbox/ radio button in the whole list; $label is the label for
     * the checkbox/ radio button; and $name, $value and $checked represent the name, value and the checked status
     * of the checkbox/ radio button input.
     *
     * @param boolean $asButtonGroup whether to generate the toggle list as a bootstrap button group
     *
     * @return ActiveField object
     */
    protected function getToggleFieldList($type, $items, $options = [], $asButtonGroup = false)
    {
        $disabled = ArrayHelper::remove($options, 'disabledItems', []);
        $readonly = ArrayHelper::remove($options, 'readonlyItems', []);
        if ($asButtonGroup) {
            Html::addCssClass($options, 'btn-group');
            $options['data-toggle'] = 'buttons';
            $options['inline'] = true;
            if (!isset($options['itemOptions']['labelOptions']['class'])) {
                $options['itemOptions']['labelOptions']['class'] = 'btn btn-default';
            }
        }
        $inline = ArrayHelper::remove($options, 'inline', false);
        $inputType = "{$type}List";
        $opts = ArrayHelper::getValue($options, 'itemOptions', []);
        $this->initDisability($opts);
        $css = $this->form->disabled ? ' disabled' : '';
        $css .= $this->form->readonly ? ' readonly' : '';
        if ($inline && !isset($options['itemOptions']['labelOptions']['class'])) {
            $options['itemOptions']['labelOptions']['class'] = "{$type}-inline{$css}";
        } elseif (!isset($options['item'])) {
            $labelOptions = ArrayHelper::getValue($opts, 'labelOptions', []);
            $options['item'] = function ($index, $label, $name, $checked, $value)
            use ($type, $css, $disabled, $readonly, $asButtonGroup, $labelOptions, $opts) {
                $opts += [
                    'data-index' => $index,
                    'label' => $this->getFieldLabel($label),
                    'value' => $value,
                    'disabled' => $this->form->disabled,
                    'readonly' => $this->form->readonly,
                ];
                if ($asButtonGroup && $checked) {
                    Html::addCssClass($labelOptions, 'active');
                }
                if (!empty($disabled) && in_array($value, $disabled) || $this->form->disabled) {
                    Html::addCssClass($labelOptions, 'disabled');
                    $opts['disabled'] = true;
                }
                if (!empty($readonly) && in_array($value, $readonly) || $this->form->readonly) {
                    Html::addCssClass($labelOptions, 'disabled');
                    $opts['readonly'] = true;
                }
                $opts['labelOptions'] = $labelOptions;
                $out = Html::$type($name, $checked, $opts);
                return $asButtonGroup ? $out : "<div class='{$type}{$css}'>{$out}</div>";
            };
        }
        return parent::$inputType($items, $options);
    }
}