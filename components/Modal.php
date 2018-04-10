<?php

namespace pvsaintpe\search\components;

use yii\bootstrap\Modal as BootstrapModal;
use pvsaintpe\search\helpers\Html;
use Yii;

/**
 * Class Modal
 * @package pvsaintpe\search\components
 */
class Modal extends BootstrapModal
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->header = Html::tag('h4', '', ['class' => 'modal-title']);
        $this->footer = Html::tag('div', '', ['class' => 'modal-tools pull-left'])
            . Html::button(
                Html::glyphIconRemove() . ' ' . Yii::t('buttons', 'Отмена'),
                ['class' => 'btn btn-default pull-right', 'data-dismiss' => 'modal']
            );
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        parent::run();
        $id = $this->getId();
        $js = <<<JS
jQuery('body').on('click', '.btn-{$id}', function (event) {
    event.preventDefault();
    event.stopPropagation();
    var btn = jQuery(this);
    var url = btn.data('url');
    if (!url) {
        url = btn.attr('href');
    }
    if (url) {
        var modal = jQuery('#{$id}');
        modal.find('.modal-title').html('');
        var modalBody = modal.find('.modal-body').html('');
        var modalTools = modal.find('.modal-tools').html('');
        modal.modal('show');
        modalBody.load(url, function (response, status, xhr) {
            if (status == 'success') {
                var boxFooter = modalBody.find('.box-footer');
                if (boxFooter.length) {
                    boxFooter.remove();
                    modalTools.html(boxFooter.html());
                    var form = modalBody.find('form');
                    var submit = modalTools.find(':submit');
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
                    modalTools.find(':reset').click(function () {
                        form.trigger('reset');
                    });
                }
            }
        });
        return false;
    }
});
JS;
        $this->getView()->registerJs($js);
    }
}
