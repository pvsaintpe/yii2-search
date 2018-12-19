<?php

namespace pvsaintpe\search\traits;

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
    use FiltersTrait;

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
        if (!($modifiers = $this->modifiers()) || empty($modifiers)) {
            return $rules;
        }
        $safeAttributes = [];
        foreach ($this->modifiers() as $modifier) {
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
     * @param $attribute
     * @return null
     */
    public function getModifierByAttribute($attribute)
    {
        if (($modifiers = $this->modifiers())) {
            foreach ($modifiers as $modifier) {
                if (in_array($attribute, $modifier['attributes'])) {
                    return $modifier['class'];
                }
            }
        }
        return null;
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
        if (($relations = $this->relations())) {
            foreach ($relations as $relation) {
                if (isset($relation['attributes']) && array_key_exists($attribute, $relation['attributes'])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $attribute
     * @return mixed
     */
    public function getRelationByAttribute($attribute)
    {
        if (($relations = $this->relations())) {
            foreach ($relations as $relation) {
                if (isset($relation['attributes'])
                    && array_key_exists($attribute, $relation['attributes'])
                ) {
                    return [
                        'class' => $relation['class'],
                        'alias' => $relation['alias'],
                        'attribute' => $relation['attributes'][$attribute]['attribute'],
                    ];
                }
            }
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
        if (($relations = $this->relations())) {
            foreach ($relations as $key => $relation) {
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
        }

        foreach ($this->getFilterAttributes() as $attribute => $value) {
            if (!$this->getBaseModel()->hasAttribute($attribute)
                && !$this->hasRelationByAttribute($attribute)
            ) {
                Yii::$app->session->setFlash(
                    'error',
                    Yii::t('errors', 'Relation for {attribute} not defined in relations()', [
                        'attribute' => $attribute,
                    ])
                );
            }

            if ($this->hasRelationByAttribute($attribute)) {
                $relation = $this->getRelationByAttribute($attribute);
                $relationAttribute = $relation['alias'] . '.' . $relation['attribute'];
            } else {
                $relationAttribute = $this->query->a($attribute);
            }

            /** @var ModifierInterface $modifier */
            if (($modifier = $this->getModifierByAttribute($attribute))) {
                $modifier::modifyQuery($this->query, [$relationAttribute => $value]);
            } else {
                $this->query->andWhere([$relationAttribute => $value]);
            }
        }
    }

    /**
     * @param array $options
     * @return ActiveDataProvider|DataProviderInterface
     */
    public function getDataProvider($options = [])
    {
        /** @var DataProviderInterface|ActiveDataProvider $dataProvider */
        $dataProvider = new $this->providerClassName([
            'query' => $this->query,
            'pagination' => $this->getPagination(),
        ]);

        $pagination = $dataProvider->getPagination();
        if ($pagination && !Yii::$app instanceof Application) {
            $pagination->setPage($this->getPage());
        }

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

        $dataProvider->refresh();
        $dataProvider->prepare(true);

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
}
