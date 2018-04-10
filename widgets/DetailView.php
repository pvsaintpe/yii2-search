<?php

namespace pvsaintpe\search\widgets;

use yii\helpers\ArrayHelper;
use kartik\detail\DetailView as KartikDetailView;

/**
 * Class DetailView
 * @package pvsaintpe\search\widgets
 */
class DetailView extends KartikDetailView
{
    public $hideIfEmpty = true;
    public $notSetIfEmpty = true;

    /**
     * @param array $config
     * @return string
     */
    public static function widget($config = [])
    {
        $config = ArrayHelper::merge([
            'template' => function ($attribute, $index, $widget) {
                if (is_callable($attribute['value'])) {
                    $attribute['value'] = call_user_func($attribute['value'], $widget->model, $index, $widget);
                }

                $value = $widget->formatter->format($attribute['value'], $attribute['format']);

                return strtr("<tr><th>{label}</th><td>{value}</td></tr>", [
                    '{label}' => $attribute['label'],
                    '{value}' => $value,
                ]);
            },
            'condensed' => true,
            'enableEditMode' => false,
            'hover' => true,
            'mode' => static::MODE_VIEW,
            'fadeDelay' => 0,
            'hideIfEmpty' => false,
        ], $config);

        if (isset($config['disableAttributes']) && sizeof($config['disableAttributes']) > 0) {
            foreach ($config['attributes'] as $columnId => $gridColumnName) {
                if (isset($gridColumnName['attribute'])
                    && in_array($gridColumnName['attribute'], $config['disableAttributes'])
                ) {
                    unset($config['attributes'][$columnId]);
                    continue;
                }

                if (in_array($gridColumnName, $config['disableAttributes'])) {
                    unset($config['attributes'][$columnId]);
                    continue;
                }
            }
        }
        unset($config['disableAttributes']);
        return parent::widget($config);
    }
}
