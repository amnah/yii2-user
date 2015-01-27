<?php

namespace amnah\yii2\user\components;

use Yii;
use yii\authclient\OAuth2;
use yii\base\Exception;

/**
 * Reddit OAuth2
 *
 * @see https://github.com/reddit/reddit/wiki/OAuth2
 * @see https://www.reddit.com/dev/api/oauth
 */
class RedditAuth extends OAuth2
{
    /**
     * @inheritdoc
     */
    public $authUrl = 'https://ssl.reddit.com/api/v1/authorize';

    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://ssl.reddit.com/api/v1/access_token';

    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'https://oauth.reddit.com/api/v1';

    /**
     * @inheritdoc
     */
    public $scope = 'identity';

    /**
     * @var string Twitter duration - "temporary" or "permanent"
     * @see https://github.com/reddit/reddit/wiki/OAuth2#authorization
     */
    public $duration = 'temporary';

    /**
     * @var string Token state - anything you want. According to Reddit, you should use
     *             this to verify authorization requests by checking that it matches
     * @see https://github.com/reddit/reddit/wiki/OAuth2#authorization
     */
    public $state = 'UnZgq3GKBUqT@2Pyu5yZu!$VHJDf3FjD';

    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'reddit';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return 'Reddit';
    }

    /**
     * @inheritdoc
     */
    protected function defaultViewOptions()
    {
        return [
            'popupWidth' => 850,
            'popupHeight' => 400,
        ];
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return $this->api('me', 'GET');
    }

    /**
     * Add state and duration to $defaultParams
     *
     * @inheritdoc
     */
    public function buildAuthUrl(array $params = [])
    {
        $defaultParams = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->getReturnUrl(),
            'xoauth_displayname' => Yii::$app->name,
            'state' => $this->state,
            'duration' => $this->duration,
        ];
        if (!empty($this->scope)) {
            $defaultParams['scope'] = $this->scope;
        }

        return $this->composeUrl($this->authUrl, array_merge($defaultParams, $params));
    }

    /**
     * Add curl headers
     *
     * @inheritdoc
     */
    protected function composeRequestCurlOptions($method, $url, array $params)
    {
        $curlOptions = parent::composeRequestCurlOptions($method, $url, $params);

        // add HTTP Basic Authorization headers for getting access token
        if ($url == $this->tokenUrl) {
            $curlOptions[CURLOPT_HTTPHEADER][] = 'Authorization: Basic '. base64_encode("{$params["client_id"]}:{$params["client_secret"]}");
        }
        // add Bearer token for api requests
        elseif (strpos($url, $this->apiBaseUrl) !== false) {
            $curlOptions[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' . $params['access_token'];
        }
        return $curlOptions;
    }

    /**
     * Check matching "state" and remove from params so that return uri will match
     * the one set in the Reddit app
     *
     * @inheritdoc
     */
    protected function defaultReturnUrl()
    {
        $params = $_GET;

        // check for matching state
        if (!empty($params['state']) && $params['state'] !== $this->state) {
            throw new Exception("State does not match");
        }

        unset($params['code']);
        unset($params['state']);
        $params[0] = Yii::$app->controller->getRoute();

        return Yii::$app->getUrlManager()->createAbsoluteUrl($params);
    }
}
