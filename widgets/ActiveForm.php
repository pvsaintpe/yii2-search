<?php

namespace pvsaintpe\search\widgets;

/**
 * Class ActiveForm
 * @package pvsaintpe\search\widgets
 */
class ActiveForm extends \kartik\form\ActiveForm
{
    /**
     * @var string
     */
    public $content;

    public $enableClientValidation = false;

    public $options = [
        'class' => 'active-form'
    ];

    public function init()
    {
        parent::init();
        echo $this->content;

        $js = <<<JS
$('form.active-form').on('submit', function(e){
        var form = $(this);
        var submit = form.find(':submit');
        form.on('beforeValidate', function () {
            submit.prop('disabled', true);
        });
        form.on('afterValidate', function () {
            submit.prop('disabled', false);
        });
        form.on('beforeSubmit', function () {
            submit.prop('disabled', true);
        });
        submit.click(function () {
            form.trigger('submit');
        });
    });
JS;
        $this->getView()->registerJs($js);

    }
}