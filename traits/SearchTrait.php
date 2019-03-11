<?php

namespace pvsaintpe\search\traits;

use yii\db\Expression;
use pvsaintpe\helpers\Url;
use pvsaintpe\search\components\ActiveQuery;
use pvsaintpe\search\components\View;
use pvsaintpe\search\helpers\Html;
use pvsaintpe\search\interfaces\ModifierInterface;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\data\Sort;
use yii\db\ActiveRecordInterface;
use yii\db\Exception;
use yii\web\Application;

/**
 * Trait SearchTrait
 *
 * @package pvsaintpe\search\traits
 *
 * @method array getGridColumns()
 * @method array getGridTitle($language = null)
 * @method array getListColumns()
 * @method array getListTitle()
 * @method array rules()
 */
trait SearchTrait
{
    /**
     * @var array
     */
    protected $disableColumns = [];

    /**
     * @return mixed
     */
    public function getDisableColumns()
    {
        return $this->disableColumns;
    }

    /**
     * @var array
     */
    protected $enableColumns = [];

    /**
     * @return mixed
     */
    public function getEnableColumns()
    {
        return $this->enableColumns;
    }

    /**
     * @var
     */
    protected $disableAttributes = [];

    /**
     * @return mixed
     */
    public function getDisableAttributes()
    {
        return $this->disableAttributes;
    }

    /**
     * @var
     */
    protected $enableAttributes = [];

    /**
     * @return mixed
     */
    public function getEnableAttributes()
    {
        return $this->enableAttributes;
    }

    /**
     * @var bool
     */
    protected $showPageSummary = true;

    /**
     * @return bool
     */
    public function isShowPageSummary()
    {
        return $this->showPageSummary;
    }

    /**
     * @return array
     */
    public function getAfterSummaryColumns()
    {
        return [];
    }

    /**
     * @var array
     */
    protected $searchFilters = [];

    /**
     * @param array $filters
     * @return $this
     */
    public function setSearchFilters($filters = [])
    {
        $this->searchFilters = $filters;
        return $this;
    }

    /**
     * @return array
     */
    public function getSearchFilters()
    {
        return $this->searchFilters;
    }

    /**
     * @var string
     */
    protected $viewPath;

    /**
     * @param $viewPath
     */
    public function setViewPath($viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * @return mixed
     */
    public function getViewPath()
    {
        return $this->viewPath;
    }

    /**
     * @var string
     */
    protected $updatePath;

    /**
     * @param $updatePath
     */
    public function setUpdatePath($updatePath)
    {
        $this->updatePath = $updatePath;
    }

    /**
     * @return mixed
     */
    public function getUpdatePath()
    {
        return $this->updatePath;
    }

    /**
     * @var View
     */
    private $view;

    /**
     * @param \yii\web\View|View $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @param array $filters
     */
    public function setFilters($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param string $attribute
     * @return mixed
     */
    public function getFilter($attribute)
    {
        $filters = static::getFilters();
        if (isset($filters[$attribute])) {
            return $filters[$attribute];
        }
        return [];
    }

    /**
     * @var string
     */
    protected $permissionPrefix;

    /**
     * @param $permissionPrefix
     */
    public function setPermissionPrefix($permissionPrefix)
    {
        $this->permissionPrefix = $permissionPrefix;
    }

    /**
     * @return mixed
     */
    public function getPermissionPrefix()
    {
        return $this->permissionPrefix;
    }

    /**
     * @var null|array
     */
    protected $defaultOrder;

    /**
     * @param $order
     * @return $this
     */
    public function setDefaultOrder($order = null)
    {
        if ($order) {
            if (preg_match('/^([-]?)(.*)/', $order, $matches)) {
                list(, $direction, $column) = $matches;
                $this->defaultOrder = [
                    $column => $direction ? SORT_DESC : SORT_ASC,
                ];
            }
        }

        return $this;
    }

    /**
     * @var string
     */
    public $providerClassName = 'pvsaintpe\search\components\ActiveDataProvider';

    /**
     * @var int
     */
    protected $defaultPageSize = 20;

    /**
     * @param int|null $pageSize
     * @return $this
     */
    public function setPageSize($pageSize = null)
    {
        if (!is_null($pageSize)) {
            $this->defaultPageSize = $pageSize;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultPageSize()
    {
        return $this->defaultPageSize;
    }

    /**
     * @var ActiveQuery
     */
    protected $query;

    /**
     * @return ActiveQuery
     */
    public function getRawQuery()
    {
        return $this->query;
    }

    /**
     * @var array
     */
    protected $sort = [];

    /**
     * @param mixed $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return array
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @var null
     */
    protected $page = null;

    /**
     * @param null|integer $page
     * @return $this
     */
    public function setPage($page = null)
    {
        if (!is_null($page)) {
            $this->page = $page;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        if (is_null($this->page) && Yii::$app instanceof Application) {
            $page = Yii::$app->getRequest()->get('page', null);
            if (!is_null($page)) {
                $this->page = $page - 1;
            }
        }

        return $this->page;
    }

    /**
     * @return string
     */
    public function formName()
    {
        return static::getFormName();
    }

    /**
     * @return string
     */
    public static function getFormName()
    {
        return 't';
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getSafeAttributes()
    {
        $attributes = array_flip(array_keys($this->getBaseModel()->getAttributes()));
        foreach ($this->rules() as $rule) {
            if (is_array($rule[0])) {
                $attributes = array_merge($attributes, array_flip($rule[0]));
            } else {
                $attributes = array_merge($attributes, [$rule[0] => $this->{$rule[0]}]);
            }
        }
        return array_keys($attributes);
    }

    /**
     * @return array
     */
    public function getPagination()
    {
        return [
            'defaultPageSize' => $this->getDefaultPageSize()
        ];
    }

    /**
     * @deprecated
     * @throws \Exception
     */
    public function getPaginationSize()
    {
        throw new Exception('Method is deprecated.');
    }

    /**
     * @return void
     */
    public function modifyQuery()
    {
    }

    /**
     * @param Sort|bool $sort
     * @return Sort
     */
    protected function modifySort($sort)
    {
        $baseSort = $this->baseSort();
        if ($baseSort) {
            // defaultOrder
            if (!array_intersect_key($baseSort, $sort->defaultOrder)) {
                $sort->defaultOrder = array_merge($baseSort, $sort->defaultOrder);
            }
            // attributes
            $baseSortKeys = array_keys($baseSort);
            foreach ($sort->attributes as $attribute => &$pattern) {
                if (!in_array($attribute, $baseSortKeys)) {
                    $pattern['asc'] = array_merge($baseSort, $pattern['asc']);
                    $pattern['desc'] = array_merge($baseSort, $pattern['desc']);
                }
            }
        }
        return $sort;
    }

    /**
     * @return array
     */
    protected function baseSort()
    {
        return [];
    }

    /**
     * @return array|null
     */
    public function modifiers()
    {
        return [];
    }

    /**
     * @return array
     */
    public function modifierRules()
    {
        $rules = [];
        $modifiers = $this->getAllModifiers();
        if (!$modifiers) {
            return $rules;
        }
        $safeAttributes = [];
        foreach ($modifiers as $modifier) {
            switch ($modifier['class']) {
                case 'pvsaintpe\search\modifiers\AmountModifier':
                    $rules[] = [
                        $modifier['attributes'],
                        'match',
                        'pattern' => '#^(!~|~|>=|<=|=|<>|!=|<|>|abs:)\d{1,15}(?:\.\d{1,8})?|^\d+|^(!~|~|>=|<=|=|<>|!=|<|>|abs:)-\d{1,15}(?:\.\d{1,8})?|^-\d+|^(\d+|-\d+|\d{1,15}(?:\.\d{1,8})?):(\d+|-\d+|\d{1,15}(?:\.\d{1,8})?)#',
                        'message' => Yii::t('model', 'Поле может содерджать только цифры и операторы сравнения (abs:,~,!~,<=,>=,=,!=,<,>,<>,-,.)')
                    ];
                    break;
            }
            $safeAttributes = array_merge($safeAttributes, $modifier['attributes']);
        }
        $rules[] = [$safeAttributes, 'safe'];
        return $rules;
    }

    /**
     * @var array
     */
    protected $modifiersCached = [];

    /**
     * @param $attribute
     * @return null
     */
    public function getModifierByAttribute($attribute)
    {
        $modifiers = $this->getAllModifiers();
        if ($modifiers) {
            foreach ($modifiers as $modifier) {
                if (in_array($attribute, $modifier['attributes'])) {
                    $class = $modifier['class'];
                    if (empty($this->modifiersCached[$class][$attribute])) {
                        $attributes = $modifier['attributes'];
                        unset($modifier['attributes']);
                        $obj = Yii::createObject($modifier);
                        $this->modifiersCached[$class] = array_fill_keys($attributes, $obj);
                    }
                    return $this->modifiersCached[$class][$attribute];
                }
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function baseModifiers()
    {
        $modifiers = [
            'date' => [
                'class' => 'pvsaintpe\search\modifiers\DateModifier',
                'attributes' => static::dateAttributes()
            ],
            'datetime' => [
                'class' => 'pvsaintpe\search\modifiers\DatetimeModifier',
                'attributes' => static::datetimeAttributes()
            ]
        ];
        return $modifiers;
    }

    /**
     * @return array
     */
    public function getAllModifiers()
    {
        return array_merge($this->baseModifiers(), $this->modifiers());
    }

    /**
     * @return array
     */
    public function relations()
    {
        return [];
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function hasRelationByAttribute($attribute)
    {
        return isset($this->curAttributes[$attribute]);
    }

    /**
     * @param $attribute
     * @return mixed
     */
    public function getRelationByAttribute($attribute)
    {
        if ($this->hasRelationByAttribute($attribute)) {
            return $this->relations()[$this->curAttributes[$attribute]];
        }
        return null;
    }

    /**
     * @param $relation
     * @return bool
     */
    protected function isRelationEnable($relation)
    {
        return $relation['enabled'] ?? true;
    }

    /**
     * @return array
     */
    protected function getFilterAttributes()
    {
        $attributes = [];

        // инициализация текущих фильтров (если не пустые)
        $columns = array_keys($this->getGridColumns());
        foreach ($columns as $attribute) {
            $value = $this->{$attribute} ?? null;
            if ($value === null || (is_string($value) && strlen($value) === 0)) {
                continue;
            }
            $attributes[$attribute] = $value;
        }

        return $attributes;
    }

    /**
     * @return $this|ActiveRecordInterface
     * @throws Exception
     */
    public function getBaseModel()
    {
        if (!$this instanceof ActiveRecordInterface) {
            throw new Exception(
                Yii::t('errors', 'Вызываемый класс должен реализовывать интерфейс: ActiveRecordInterface')
            );
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function initExtendedFilters()
    {
        $this->searchInit();
        $this->addExpressionsToSelect();
        foreach ($this->curRelations as $key) {
            $relation = $this->relations()[$key];
            $alias = isset($relation['alias']) ? $relation['alias'] : $key;
            $joinType = $relation['joinType'] ?? 'joinWith';
            $this->query->{$joinType}([
                ($key . ' ' . $alias) => function (ActiveQuery $query) use ($relation) {
                    if (($conditions = $relation['conditions'] ?? null)
                        && is_callable($conditions)
                        && $this->isRelationEnable($relation)) {
                        $conditions($query);
                    }
                }
            ]);
        }

        $model = $this->getBaseModel();
        foreach ($this->getFilterAttributes() as $attribute => $value) {
            if ($model->hasAttribute($attribute)) {
                $conditionAttribute = $this->query->a($attribute);
            } elseif ($this->hasRelationByAttribute($attribute)) {
                $conditionAttribute = $this->getRelationAttribute($attribute);
            } elseif ($this->hasExpression($attribute)) {
                $conditionAttribute = $this->getAttributeExpression($attribute);
            } else {
                Yii::$app->session->setFlash(
                    'error',
                    Yii::t('errors', 'Relation for {attribute} not defined in relations() or expressions()', [
                        'attribute' => $attribute,
                    ])
                );
            }
            if (isset($conditionAttribute)) {
                /** @var ModifierInterface $modifier */
                if (isset($conditionAttribute) && ($modifier = $this->getModifierByAttribute($attribute))) {
                    $modifier->modifyQuery($this->query, [$conditionAttribute => $value]);
                } else {
                    $this->query->andWhere([$conditionAttribute => $value]);
                }
            }
        }
    }

    /**
     * Признак таблицы, содержащей очень большие данные
     * Сортировка и группировки в такой таблице будут отключены
     * @return bool
     */
    protected function isBigTable()
    {
        if (Yii::$app instanceof Application) {
            return Yii::$app->getRequest()->get('isBigTable', false);
        }
    }

    /**
     * @var bool
     */
    private $prepareEnabled = false;

    /**
     * @param $prepareEnabled
     * @return $this
     */
    public function setPrepareEnabled($prepareEnabled)
    {
        $this->prepareEnabled = $prepareEnabled;
        return $this;
    }

    /**
     * @param array $paramOptions
     * @return ActiveDataProvider|DataProviderInterface
     */
    public function getDataProvider($paramOptions = [])
    {
        if ($this->isBigTable()) {
            $dataProvider = $this->createBigDataProvider($paramOptions);
        } else {
            $dataProvider = $this->createNormalDataProvider($paramOptions);
        }

        $this->setPagination($dataProvider);

        if ($this->prepareEnabled) {
            $dataProvider->refresh();
            $dataProvider->prepare(true);
        }

        return $dataProvider;
    }

    /**
     * @param DataProviderInterface $dataProvider
     * @return $this
     */
    private function setPagination(DataProviderInterface $dataProvider)
    {
        $pagination = $dataProvider->getPagination();
        if ($pagination && !Yii::$app instanceof Application) {
            $pagination->setPage($this->getPage());
        }
        return $this;
    }

    /**
     * @param array $paramOptions
     * @return ActiveDataProvider|DataProviderInterface
     */
    private function createBigDataProvider($paramOptions)
    {
        $this->query->orderBy = null;
        $this->query->groupBy = null;

        /** @var DataProviderInterface|ActiveDataProvider $dataProvider */
        $dataProvider = new $this->providerClassName([
            'query' => $this->query,
            'pagination' => $this->getPagination(),
            'sort' => false,
        ]);

        return $dataProvider;
    }

    /**
     * @param array $paramOptions
     * @return ActiveDataProvider|DataProviderInterface
     */
    private function createNormalDataProvider($paramOptions)
    {
        /** @var DataProviderInterface|ActiveDataProvider $dataProvider */
        $dataProvider = new $this->providerClassName([
            'query' => $this->query,
            'pagination' => $this->getPagination(),
        ]);

        $this->relationSort();
        $this->expressionSort();
        if (($customSort = static::getSort()) !== false) {
            $sort = $dataProvider->getSort();
            foreach ($customSort as $attribute => $options) {
                if (property_exists($sort, $attribute)) {
                    if (is_array($sort->{$attribute}) && is_array($options)) {
                        $sort->{$attribute} = array_merge($sort->{$attribute}, $options);
                    } else {
                        $sort->{$attribute} = $options;
                    }
                }
            }
            if ($this->defaultOrder) { // export order
                $sort->defaultOrder = $this->defaultOrder;
            } elseif (isset($customSort['defaultOrder'])) { // custom sort
                $sort->defaultOrder = $customSort['defaultOrder'];
            }
            $sort = $this->modifySort($sort);
            $dataProvider->setSort($sort);
        } else {
            $dataProvider->setSort(false);
        }

        return $dataProvider;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    protected function getGridToolbarButtons()
    {
        return [
            $this->getGridRefresh(),
            $this->getGridReset(),
        ];
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getGridToolbar()
    {
        $buttons = [];
        $customButtons = array_filter(static::getGridToolbarButtons());
        $button = ['content' => ''];
        foreach ($customButtons as $customButton) {
            $button['content'] .= $customButton['content'];
        }
        $buttons[] = $button;
        return $buttons;
    }

    /**
     * @return array
     */
    public function getGridReset()
    {
        $url = ['index'];
        foreach ($this->getPageAttributes() as $index => $attribute) {
            if (is_bool($attribute)) {
                if (!$attribute) {
                    $key = $index;
                    $url[$key] = $this->{$index};
                } else {
                    $key = $this->formName() . "[$index]";
                    $url[$key] = $this->{$index};
                }
                continue;
            }
            if (!empty($this->{$attribute})) {
                $key = $this->formName() . "[$attribute]";
                $url[$key] = $this->{$attribute};
            }
        }

        return [
            'content' => Html::a(
                '<i class="glyphicon glyphicon-trash"></i>',
                $url,
                [
                    'data-pjax' => 0,
                    'class' => 'btn btn-default btn-md',
                    'title' => Yii::t('info', 'Сбросить')
                ]
            ),
        ];
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getGridRefresh()
    {
        return [
            'content' => Html::a(
                '<i class="glyphicon glyphicon-refresh"></i>',
                Url::modify(Yii::$app->getRequest()->getUrl(), ['page', 'per-page']),
                [
                    'data-pjax' => 0,
                    'class' => 'btn btn-default btn-md',
                    'title' => Yii::t('info', 'Обновить')
                ]
            ),
        ];
    }

    /**
     * @return array
     */
    public function getPageAttributes()
    {
        return [];
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function getShortName()
    {
        $searchClass = new \ReflectionClass($this);
        return $searchClass->getShortName();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function settingsAttributes()
    {
        return $this->getBaseModel()->attributes();
    }

    /**
     * Sort for relation columns
     */
    public function relationSort()
    {
        $model = $this->getBaseModel();
        foreach ($model->relations() as $relation) {
            foreach ($relation['attributes'] as $attribute => $config) {
                $realAttribute = $this->getRealAttribute($relation['alias'], $config);
                $this->sort['attributes'][$attribute] = [
                    'asc' => [$realAttribute => SORT_ASC],
                    'desc' => [$realAttribute => SORT_DESC]
                ];
            }
        }
    }

    /**
     * @param string $alias
     * @param $config
     * @return string
     */
    public function getRealAttribute(string $alias, $config): string
    {
        if (isset($config['expression'])) {
            return (new \MessageFormatter('en_US', $config['expression']))->format(compact('alias'));
        } else {
            return $alias . '.' . $config['attribute'];
        }
    }

    /**
     * Init current attributes and current relations
     */

    protected $curAttributes = [];
    protected $curRelations = [];

    protected function searchInit()
    {
        $agc = $this->getActiveGridColumns();
        foreach ($this->relations() as $relation => $e) {
            foreach (array_keys($e['attributes']) as $attribute) {
                if (in_array($attribute, $agc)) {
                    $this->curAttributes[$attribute] = $relation;
                }
            }
        }
        $this->curRelations = array_unique($this->curAttributes);
    }

    public function getActiveGridColumns()
    {
        return [];
    }

    /**
     * @param string $attribute
     * @return null|string
     */
    public function getRelationAttribute(string $attribute): ?string
    {
        if ($relation = $this->getRelationByAttribute($attribute)) {
            if (isset($relation['attributes'][$attribute]['attribute'])) {
                return $relation['alias'] . '.' . $relation['attributes'][$attribute]['attribute'];
            } elseif (isset($relation['attributes'][$attribute]['expression'])) {
                return $relation['attributes'][$attribute]['expression'];
            }
        }
        return null;
    }

    public function expressions()
    {
        return [];
    }

    /**
     * @param string $attribute
     * @return bool
     */
    public function hasExpression(string $attribute): bool
    {
        return isset($this->expressions()[$attribute]);
    }

    /**
     * @param string $attribute
     * @return null|string
     */
    public function getAttributeExpression(string $attribute): ?string
    {
        return $this->expressions()[$attribute] ?? null;
    }

    /**
     * Sort for expression columns
     */
    public function expressionSort()
    {
        $model = $this->getBaseModel();
        foreach ($model->expressions() as $attribute => $realAttribute) {
            $this->sort['attributes'][$attribute] = [
                'asc' => [$realAttribute => SORT_ASC],
                'desc' => [$realAttribute => SORT_DESC]
            ];
        }
    }

    /**
     * Add all attributes into $query->select
     */
    public function addExpressionsToSelect()
    {
        $model = $this->getBaseModel();
        $agc = $this->getActiveGridColumns();
        foreach ($model->expressions() as $attribute => $config) {
            if (in_array($attribute, $agc)) {
                $this->addExpressionToSelect($attribute);
            }
        }
    }

    /**
     * Add one attribute into $query->select
     * @param string $attribute
     */
    public function addExpressionToSelect($attribute)
    {
        $query = $this->query;
        if (is_null($query->select)) {
            $query->addSelect([$query->a('*')]);
        }
        $expression = $this->getAttributeExpression($attribute);
        $query->addSelect(new Expression("{$expression} {$attribute}"));
    }
}
