<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator pvsaintpe\search\generators\crud\Generator */

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
<div class="box box-danger <?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">
    <div class="box-body">
        <?= "<?= " ?>GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'disableColumns' => $searchModel->getDisableColumns(),
            'columns' => $searchModel->getGridColumns(),
            'toolbar' => $searchModel->getGridToolbar(),
        ]) ?>
    </div>
</div>