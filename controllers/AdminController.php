<?php
/**
 * AdminController.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @package p2made/yii2-p2y2-users
 * @license MIT
 */

namespace p2m\users\controllers;

use Yii;
use p2m\users\models\User;
use p2m\users\models\UserToken;
use p2m\users\models\UserAuth;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * class p2m\users\controllers\AdminController
 *
 * AdminController implements the CRUD actions for User model.
 */
class AdminController extends \yii\web\Controller
{
	/**
	 * @var \p2m\users\modules\UsersModule
	 * @inheritdoc
	 */
	public $module;

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		// check for admin permission (`tbl_role.can_admin`)
		// note: check for Yii::$app->user first because it doesn't exist in console commands (throws exception)
		if (!empty(Yii::$app->user) && !Yii::$app->user->can("admin")) {
			throw new ForbiddenHttpException('You are not allowed to perform this action.');
		}

		parent::init();
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

	/**
	 * List all User models
	 * @return mixed
	 */
	public function actionIndex()
	{
		/** @var \p2m\users\models\search\UserSearch $searchModel */
		$searchModel = $this->module->model("UserSearch");
		$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

		return $this->render('index', compact('searchModel', 'dataProvider'));
	}

	/**
	 * Display a single User model
	 * @param string $id
	 * @return mixed
	 */
	public function actionView($id)
	{
		return $this->render('view', [
			'user' => $this->findModel($id),
		]);
	}

	/**
	 * Create a new User model. If creation is successful, the browser will
	 * be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		/** @var \p2m\users\models\User $user */
		/** @var \p2m\users\models\Profile $profile */

		$user = $this->module->model("User");
		$user->setScenario("admin");
		$profile = $this->module->model("Profile");

		$post = Yii::$app->request->post();
		$userLoaded = $user->load($post);
		$profile->load($post);

		// validate for ajax request
		if (Yii::$app->request->isAjax) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($user, $profile);
		}

		if ($userLoaded && $user->validate() && $profile->validate()) {
			$user->save(false);
			$profile->setUser($user->id)->save(false);
			return $this->redirect(['view', 'id' => $user->id]);
		}

		// render
		return $this->render('create', compact('user', 'profile'));
	}

	/**
	 * Update an existing User model. If update is successful, the browser
	 * will be redirected to the 'view' page.
	 * @param string $id
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		// set up user and profile
		$user = $this->findModel($id);
		$user->setScenario("admin");
		$profile = $user->profile;

		$post = Yii::$app->request->post();
		$userLoaded = $user->load($post);
		$profile->load($post);

		// validate for ajax request
		if (Yii::$app->request->isAjax) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($user, $profile);
		}

		// load post data and validate
		if ($userLoaded && $user->validate() && $profile->validate()) {
			$user->save(false);
			$profile->setUser($user->id)->save(false);
			return $this->redirect(['view', 'id' => $user->id]);
		}

		// render
		return $this->render('update', compact('user', 'profile'));
	}

	/**
	 * Delete an existing User model. If deletion is successful, the browser
	 * will be redirected to the 'index' page.
	 * @param string $id
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		// delete profile and userTokens first to handle foreign key constraint
		$user = $this->findModel($id);
		$profile = $user->profile;
		UserToken::deleteAll(['user_id' => $user->id]);
		UserAuth::deleteAll(['user_id' => $user->id]);
		$profile->delete();
		$user->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Find the User model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param string $id
	 * @return User the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		/** @var \p2m\users\models\User $user */
		$user = $this->module->model("User");
		$user = $user::findOne($id);
		if ($user) {
			return $user;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
?>


