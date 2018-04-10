<?php
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\StringHelper;
use yii\db\Schema;

/* @var $this yii\web\View */
/* @var $generator backend\templates\generators\crud\Generator */

$baseSearchModelClass = StringHelper::basename($generator->baseSearchModelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($baseSearchModelClass === $searchModelClass) {
    $baseSearchModelAlias = $baseSearchModelClass . 'Model';
}
$rules = $generator->generateSearchRules();
$labels = $generator->generateSearchLabels();
$searchAttributes = $generator->getSearchAttributes();
$searchConditions = $generator->generateSearchConditions();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->searchModelClass, '\\')) ?>;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use <?= ltrim($generator->baseSearchModelClass, '\\') . (isset($baseSearchModelAlias) ? " as $baseSearchModelAlias" : "") ?>;
use yii\helpers\ArrayHelper;

/**
 * <?= $searchModelClass ?> represents the model behind the search form about `<?= $generator->modelClass ?>`.
 */
class <?= $searchModelClass ?> extends <?= isset($baseSearchModelAlias) ? $baseSearchModelAlias : $baseSearchModelClass ?>

{
    /**
     * @return array
     */
    public function getDisableColumns()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getGridColumns()
    {
        return [
            /*
            'id' => [
                'class' => 'backend\components\grid\IdColumn',
                'permissionPrefix' => $this->getPermissionPrefix(),
            ],
            */<?php

            $tableSchema = $generator->getTableSchema();

            $columns = [];
            $to_end = [];
            $generator->generateTimestampAttributes($generator->getTableSchema());
            $generator->generateStatusAttributes($generator->getTableSchema());
            $end_sort = array_merge($generator->getDatetimeAttributes(), $generator->getStatusAttributes());
            $count = 0;
            if (($tableSchema = $generator->getTableSchema()) === false) {
                foreach ($generator->getColumnNames() as $name) {
                    if (in_array($name, $end_sort)) {
                        $to_end[$name] = $name;
                    } else {
                        $columns[] = $name;
                    }
                }
            } else {
                $__relations = array();
                foreach ($tableSchema->foreignKeys as $i => $key) {
                    $__relationTable = array_shift($key);
                    $__key = array_keys($key);
                    $__relationKey = $__key[0];
                    $__relations[$__relationKey] = lcfirst(\yii\helpers\Inflector::camelize($__relationTable));
                }

                foreach ($tableSchema->columns as $column) {
                    if (in_array($column->name, $end_sort)) {
                        $to_end[$column->name] = $column;
                    } else {
                        $columns[] = $column;
                    }
                }
            }
            $count = count($columns) + count($to_end);
            foreach ($columns as $index => $column) {
                echo $generator->generateColumn($tableSchema, $column, $index * 2 >= $count);
            }
            if (!empty($to_end)) {
                foreach ($end_sort as $name) {
                    if (isset($to_end[$name])) {
                        echo $generator->generateColumn($tableSchema, $to_end[$name], true);
                    }
                }
            }
            ?>

            'action' => [
                'class' => 'backend\components\grid\ActionColumn',
                'permissionPrefix' => $this->getPermissionPrefix(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params = null)
    {
        if (!empty($params)) {
            $this->load($params);
        }

        $this->query = <?= $baseSearchModelClass ?>::find();
        <?= implode("\n        ", $searchConditions) ?>
        <?php
            $__classModel = ltrim($generator->modelClass, '\\');
            /** @var \yii\db\ActiveRecord $__className */
            $__className = new $__classModel();
            $__relations = array();

            $keys = $__className->getTableSchema()->foreignKeys;
            $columns = $__className->getTableSchema()->columns;

            $joinWith = [];
            $innerJoinWith = [];

            foreach ($keys as $i => $key) {
                $relationTable = array_shift($key);
                $column = array_intersect_key($columns, $key);
                $columnOptions = array_shift($column);

                foreach ($__className::singularRelations() as $relation => $relationOptions) {
                    if (in_array($columnOptions->name, $relationOptions['link'])) {
                        if ($columnOptions->allowNull) {
                            $joinWith[] = $relation . ' ' . $relation;
                        } else {
                            $innerJoinWith[] = $relation . ' ' . $relation;
                        }
                        break;
                    }
                }
            }

            if (count($innerJoinWith) > 0) {
                echo "\n        \$this->query->innerJoinWith([\n            '" . join("',\n            '", $innerJoinWith) . "'\n        ]);";
            }
            if (count($joinWith) > 0) {
                echo "\n\n        \$this->query->joinWith([\n            '" . join("',\n            '", $joinWith) . "'\n        ]);\n";
            }
        ?>
        
        return $this->getDataProvider();
    }
}