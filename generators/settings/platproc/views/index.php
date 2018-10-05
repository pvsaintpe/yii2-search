<?php

/* @var $this yii\web\View */
/* @var $generator pvsaintpe\search\generators\settings\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use pvsaintpe\search\helpers\Html;
use pvsaintpe\grid\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel <?= ltrim($generator->searchModelClass, '\\') ?> */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $permissionPrefix */

$this->title = $searchModel::getGridTitle();
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-danger <?= \yii\helpers\Inflector::camel2id(\yii\helpers\StringHelper::basename($generator->modelClass)) ?>-index">
    <div class="box-body">
        <?php echo "<?= "; ?>GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'disableColumns' => $searchModel->getDisableColumns(),
            'columns' => $searchModel->getGridColumns(),
            'toolbar' => $searchModel->getGridReset(),
            'id' => 'main-grid',
            'pjaxSettings' => [
                'options' => [
                    'formSelector' => '#main-grid form[data-pjax]:not(\'.pjax-partial\')'
                ]
            ],
        ]) ?>
    </div>
</div>