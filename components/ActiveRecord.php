<?php

namespace pvsaintpe\search\components;

use yii\base\Exception;
use pvsaintpe\boost\db\ActiveRecord as BoostActiveRecord;
use Yii;

/**
 * Class ActiveRecord
 * @package pvsaintpe\search\components
 */
class ActiveRecord extends BoostActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public $errorCode = null;

    /**
     * @return string
     */
    protected function getShortName()
    {
        $searchClass = new \ReflectionClass($this);
        return $searchClass->getShortName();
    }

    /**
     * @param string $attribute
     * @return string[]
     */
    public function getSimpleErrors($attribute = null)
    {
        if ($attribute) {
            return implode(' ', $this->getErrors($attribute));
        } else {
            return array_map(function ($errors) {
                return implode(' ', $errors);
            }, $this->getErrors());
        }
    }

    /**
     * @param array $filters
     * @param bool $withNotSet
     * @return array
     */
    public static function findForFilter($filters = [], $withNotSet = false)
    {
        $records = static::findFilterQuery($filters)->column();
        $messages = static::getMessages();
        $result = [];
        if ($withNotSet) {
            $result = [
                -1 => Yii::t('backend', 'Не задано'),
            ];
        }
        foreach ($records as $key => $name) {
            if (!empty($messages[$key])) {
                $result[$key] = $messages[$key];
            } else {
                $result[$key] = $name;
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     * @param array $filters
     * @return ActiveQuery
     */
    public static function findFilterQuery($filters = [])
    {
        $query = static::find()->listItems();
        if (sizeof($filters) > 0) {
            foreach ($filters as $key => $value) {
                $query->andFilterWhere([$query->a($key) => $value]);
            }
        }
        return $query;
    }

    /**
     * @param string $name
     */
    public function clearAttribute($name)
    {
        if ($this->isAttributeChanged($name)) {
            $this->setAttribute($name, $this->getOldAttribute($name));
        }
    }

    /**
     * @param string $permissionName
     * @param array $params
     * @param bool $allowCaching
     * @return bool
     */
    public function webUserCan($permissionName, $params = [], $allowCaching = true)
    {
        if (!$this->getIsNewRecord()) {
            $params = array_merge($params, $this->getPrimaryKey(true));
        }
        $params['model'] = $this;
        return Yii::$app->getUser()->can($permissionName, $params, $allowCaching);
    }

    /**
     * @throws Exception
     */
    public function softDelete()
    {
        if (!$this->hasProperty('deleted')) {
            throw new Exception('Method is unavailable.');
        }

        $this->setAttribute('deleted', 1);
        if (!$this->save()) {
            throw $this->newException();
        }
    }

    /**
     * @return ActiveQuery
     */
    public function getQuery()
    {
        return static::find();
    }

    /**
     * Save model hardly
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function hardSave($runValidation = true, $attributeNames = null) {
        if (!$this->save($runValidation, $attributeNames)) {
            throw $this->newException();
        }
        return true;
    }

    /**
     * @return array
     */
    public function getFileAttributes()
    {
        return [];
    }

    /**
     * @param array $values
     * @param bool $safeOnly
     */
    public function setAttributes($values, $safeOnly = true)
    {
        if ($this->getFileAttributes()) {
            foreach ($values as $name => $value) {
                if (in_array($name, $this->getFileAttributes()) && !is_int($value)) {
                    unset($values[$name]);
                }
            }
        }
        parent::setAttributes($values, $safeOnly);
    }
}
