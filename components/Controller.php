<?php

namespace pvsaintpe\search\components;

use pvsaintpe\search\interfaces\SearchInterface;
use pvsaintpe\search\traits\SearchTrait;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller as WebController;
use yii\web\Application as WebApplication;
use Yii;
use yii\web\Response;

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
     * @inheritdoc
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        if (is_string($result) && Yii::$app->getRequest()->getIsAjax()
            && (Yii::$app->getResponse()->format == Response::FORMAT_HTML)
        ) {
            $view = $this->getView();
            if ($view->title) {
                $title = Json::htmlEncode($view->title);
                $result .= <<<JS
<script>
jQuery('#main-modal').find('.modal-title').html({$title});
jQuery('#main-modal').find('.box').removeClass().children().removeClass();
</script>
JS;
            }
        }
        return $result;
    }

    /**
     * @param null|string $searchClass
     * @return ActiveRecord|SearchInterface
     */
    public function getSearchModel($searchClass = null)
    {
        if (!$searchClass) {
            $searchClass = $this->searchClass;
        }

        /** @var ActiveRecord|SearchInterface|SearchTrait $searchModel */
        $searchModel = new $searchClass();
        $searchModel->load($this->getParams());

        $searchModel->setView($this->getView());
        $searchModel->setViewPath('/' . $this->module->id . '/' . $this->id . '/view/');
        $searchModel->setUpdatePath('/' . $this->module->id . '/' . $this->id . '/update/');
        $searchModel->setPermissionPrefix($this->getPermissionPrefix());

        return $searchModel;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    protected function getParams()
    {
        $params = Yii::$app->request->getBodyParams();
        if ($params) {
            Yii::$app->session->set('bodyParams', $params);
        }
        $params = ArrayHelper::merge($params, Yii::$app->request->queryParams);
        if (isset($params['params']) && is_string($params['params'])) {
            $params = ArrayHelper::merge($params, unserialize($params['params']));
        }
        return $params;
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
        return parent::render($view, array_merge([
            'disableAttributes' => [],
            'permissionPrefix' => $this->getPermissionPrefix()
        ], $params));
    }

    /**
     *
     * @param string $view
     * @param array $params
     * @return mixed
     */
    public function renderAjax($view, $params = array())
    {
        return parent::renderAjax($view, array_merge([
            'disableAttributes' => [],
            'permissionPrefix' => $this->getPermissionPrefix()
        ], $params));
    }

    /**
     * @param $view
     * @param array $params
     * @return mixed
     */
    public function renderWithAjax($view, $params = array())
    {
        if (Yii::$app->request->getIsAjax()) {
            return $this->renderAjax($view, $params);
        } else {
            return $this->render($view, $params);
        }
    }
}
