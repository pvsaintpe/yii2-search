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
}