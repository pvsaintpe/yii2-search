<?php

namespace pvsaintpe\search\components;

use pvsaintpe\search\interfaces\SearchInterface;
use yii\db\ActiveRecord;
use yii\web\Controller as WebController;
use yii\web\Application as WebApplication;
use Yii;

/**
 * Class Controller
 * @package pvsaintpe\search\components
 */
class Controller extends WebController
{
    protected $searchClass;

    /**
     * @var string|null
     */
    protected $rule = null;

    /**
     * @param null|string $searchClass
     * @return ActiveRecord|SearchInterface
     */
    public function getSearchModel($searchClass = null)
    {
        if (!$searchClass) {
            $searchClass = $this->searchClass;
        }
        /** @var ActiveRecord|SearchInterface $searchModel */
        $searchModel = new $searchClass();
        $searchModel->load(Yii::$app->request->queryParams);
        $searchModel->setView($this->getView());
        $searchModel->setViewPath('/' . $this->module->id . '/' . $this->id . '/view/');
        $searchModel->setUpdatePath('/' . $this->module->id . '/' . $this->id . '/update/');
        $searchModel->setPermissionPrefix($this->getPermissionPrefix());

        return $searchModel;
    }

    /**
     * @return string
     */
    public function getPermissionPrefix()
    {
        $permissionPrefix = $this->id . '/';
        if (!$this->module instanceof WebApplication) {
            $permissionPrefix = $this->module->id . '/' . $permissionPrefix;
        }
        return $permissionPrefix;
    }

    /**
     *
     * @param string $view
     * @param array $params
     * @return mixed
     */
    public function render($view, $params = array())
    {
        return parent::render($view, array_merge($params, [
            'disableAttributes' => [],
            'permissionPrefix' => $this->getPermissionPrefix()
        ]));
    }

    /**
     *
     * @param string $view
     * @param array $params
     * @return mixed
     */
    public function renderAjax($view, $params = array())
    {
        return parent::renderAjax($view, array_merge($params, [
            'disableAttributes' => [],
            'permissionPrefix' => $this->getPermissionPrefix()
        ]));
    }
}
