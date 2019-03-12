<?php

namespace pvsaintpe\search\traits;

use pvsaintpe\boost\db\Expression;
use pvsaintpe\search\components\ActiveQuery;

/**
 * Trait RelationTrait
 */
trait RelationTrait
{
    /**
     * @var array
     */
    protected $sort = [];

    /**
     * @var ActiveQuery
     */
    protected $query;

    protected $curAttributes = [];
    protected $curRelations = [];
    protected $curExpressionAttributes = [];

    /**
     * @return array
     */
    public function getActiveGridColumns()
    {
        return [];
    }

    /**
     * @return array
     */
    public function relations()
    {
        return [];
    }

    /**
     * Relation usage
     */
    public function useRelations()
    {
        $this->initRelations();
        $this->addExpressionsToSelect();
        $this->joinRelations();
    }

    protected function addExpressionsToSelect()
    {
        foreach ($this->curExpressionAttributes as $attribute => $expression) {
            $this->addExpressionToSelect($attribute, $expression);
        }
    }

    /**
     * Add one attribute into select
     * @param string $attribute
     * @param string $expression
     */
    protected function addExpressionToSelect($attribute, $expression)
    {
        $query = $this->query;
        if (is_null($query->select)) {
            $query->addSelect([$query->a('*')]);
        }
        $query->addSelect(new Expression("{$expression} {$attribute}"));
    }

    /**
     * Fill $curAttributes and $curRelations
     */
    protected function initRelations()
    {
        $agc = $this->getActiveGridColumns();
        foreach ($this->relations() as $relation => $e) {
            foreach ($e['attributes'] as $attribute => $config) {
                if (in_array($attribute, $agc)) {
                    $this->curAttributes[$attribute] = $relation;
                }
                if (isset($config['expression'])) {
                    $this->curExpressionAttributes[$attribute] = $this->getRealAttribute($e['alias'], $config);
                }
            }
        }
        $this->curRelations = array_unique($this->curAttributes);
    }

    /**
     * Join $curRelations
     */
    protected function joinRelations()
    {
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
     * @param $attribute
     * @return bool
     */
    protected function hasRelation($attribute)
    {
        return isset($this->curAttributes[$attribute]);
    }

    /**
     * Config of relation
     * @param $attribute
     * @return array|null
     */
    public function getRelationByAttribute(string $attribute): ?array
    {
        if ($this->hasRelation($attribute)) {
            return $this->relations()[$this->curAttributes[$attribute]];
        }
        return null;
    }

    /**
     * Column name or expression from config
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

    /**
     * Sort for relation columns
     */
    public function relationSort()
    {
        foreach ($this->relations() as $relation) {
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
    protected function getRealAttribute(string $alias, $config): string
    {
        if (isset($config['expression'])) {
            return (new \MessageFormatter('en_US', $config['expression']))->format(compact('alias'));
        } else {
            return $alias . '.' . $config['attribute'];
        }
    }
}
