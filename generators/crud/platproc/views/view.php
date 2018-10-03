<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator pvsaintpe\search\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use backend\helpers\Html;
use pvsaintpe\grid\widgets\DetailView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->baseSearchModelClass, '\\') ?> */

$this->title = $model->getListTitle();
$this->params['title'] = $model::getGridTitle();
$this->params['title_desc'] = <?= $generator->generateI18N('Просмотр') ?>;
$this->params['breadcrumbs'][] = ['label' => $model::getGridTitle(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model-><?= $generator->getNameAttribute() ?>;

$updateButton = Html::updateButton($model);
$indexButton = Html::indexButton($model::getGridTitle());
?>
<div class="box box-danger <?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">
    <div class="box-body">
        <p align="right">
            <?="<?=" ?> join(' ', [$updateButton, $indexButton]) ?>
        </p>
        <?="<?= "?>DetailView::widget([
            'model' => $model,
            'attributes' => $model->getListColumns(),
            'disableAttributes' => $model->getDisableAttributes(),
        ])?>
    </div>
</div>