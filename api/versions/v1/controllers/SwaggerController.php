<?php

namespace api\versions\v1\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

/**
 * @SWG\Swagger(
 *     basePath="/v1",
 *     produces={"application/json"},
 *     consumes={"application/json"},
 *     @SWG\Info(version="1.0", title="Swagger - Developer API Documentation"),
 * )
 *
 * @SWG\SecurityScheme(
 *    securityDefinition="Bearer",
 *    type="apiKey",
 *    in="header",
 *    name="Authorization"
 * )
 */

class SwaggerController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        /* @var $response yii\web\Response */
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_HTML;

        return [
            'docs' => [
                'class' => 'yii2mod\swagger\SwaggerUIRenderer',
                'restUrl' => Url::to(['swagger/json-schema']),
            ],
            'json-schema' => [
                'class' => 'yii2mod\swagger\OpenAPIRenderer',
                // Тhe list of directories that contains the swagger annotations.
                'scanDir' => [
                    Yii::getAlias('@api')
                ],
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /** Oauth START **/
    /**
     * @SWG\Post(path="/oauth/token",
     *     tags={"Oauth2"},
     *     summary="Retrieves user Bearer token.",
     * 		@SWG\Parameter(
     * 			name="body",
     * 			in="body",
     * 			@SWG\Schema(
     *              @SWG\Property(property="grant_type", type="string", default="password"),
     *              @SWG\Property(property="client_id", type="string"),
     *              @SWG\Property(property="client_secret", type="string"),
     *              @SWG\Property(property="username", type="string"),
     *              @SWG\Property(property="password", type="string"),
     *          ),
     *		),
     *     @SWG\Response(
     *         response = 200,
     *         description = "Get Bearer token",
     *         @SWG\Schema(
     *              @SWG\Property(property="access_token", type="string"),
     *              @SWG\Property(property="expires_in", type="integer"),
     *              @SWG\Property(property="token_type", type="string", default="Bearer"),
     *              @SWG\Property(property="scope", type="string", default=null),
     *              @SWG\Property(property="refresh_token", type="string"),
     *          )
     *     ),
     *     @SWG\Response(
     *         response = 401,
     *         description = "Unauthorized"
     *     ),
     *     @SWG\Response(
     *         response = 404,
     *         description = "Not Found"
     *     ),
     * )
     */

    /**
     * @SWG\Post(path="/oauth/revoke",
     *     tags={"Oauth2"},
     *     summary="Revokes Bearer token.",
     * 		@SWG\Parameter(
     * 			name="body",
     * 			in="body",
     * 			@SWG\Schema(
     *              @SWG\Property(
     *                  property="token",
     *                  type="string",
     *              )
     *          ),
     *		),
     *     @SWG\Response(
     *         response = 200,
     *         description = "Revoke Bearer token",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="revoked",
     *                  type="string",
     *                  default="true"
     *              )
     *         )
     *     )
     * )
     *
     */
    /** Oauth END */

    /** User START **/
    /**
     * @SWG\Post(path="/user/register",
     *     tags={"User"},
     *     summary="Creates new user.",
     *     @SWG\Parameter(
     * 			name="body",
     * 			in="body",
     * 			@SWG\Schema(
     *              @SWG\Property(property="email", type="string", default="admin@example.com"),
     *              @SWG\Property(property="username", type="string", default="admin"),
     *              @SWG\Property(property="first_name", type="string", default="John"),
     *              @SWG\Property(property="last_name", type="string", default="Doe"),
     *              @SWG\Property(property="address", type="string", default="20 Cooper Square"),
     *              @SWG\Property(property="city", type="string", default="New York"),
     *              @SWG\Property(property="country", type="string", default="USA"),
     *              @SWG\Property(property="zip", type="string", default="10003"),
     *              @SWG\Property(property="password", type="string"),
     *              @SWG\Property(property="password_repeat", type="string"),
     *          ),
     *	   ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "Create user.",
     *         @SWG\Schema(ref = "#/definitions/User")
     *     ),
     *     @SWG\Response(
     *         response = 401,
     *         description = "Unauthorized"
     *     ),
     *     @SWG\Response(
     *         response = 404,
     *         description = "Not Found"
     *     ),
     * )
     */

    /**
     * @SWG\Get(path="/user/info",
     *     tags={"User"},
     *     summary="Retrieves user info of a logged in user.",
     *     security={{"Bearer":{}}},
     *     @SWG\Response(
     *         response = 200,
     *         description = "Get user info",
     *         @SWG\Schema(ref = "#/definitions/User")
     *     ),
     *     @SWG\Response(
     *         response = 401,
     *         description = "Unauthorized"
     *     ),
     *     @SWG\Response(
     *         response = 404,
     *         description = "Not Found"
     *     ),
     * )
     *
     * @throws \Throwable
     */

    /**
     * @SWG\Put(path="/user/update-info",
     *     tags={"User"},
     *     summary="Updates info of a logged in user.",
     *     security={{"Bearer":{}}},
     *     @SWG\Parameter(
     * 			name="body",
     * 			in="body",
     * 			@SWG\Schema(
     *              @SWG\Property(property="email", type="string", default="admin@example.com"),
     *              @SWG\Property(property="username", type="string", default="admin"),
     *              @SWG\Property(property="first_name", type="string", default="John"),
     *              @SWG\Property(property="last_name", type="string", default="Doe"),
     *              @SWG\Property(property="address", type="string", default="20 Cooper Square"),
     *              @SWG\Property(property="city", type="string", default="New York"),
     *              @SWG\Property(property="country", type="string", default="USA"),
     *              @SWG\Property(property="zip", type="string", default="10003"),
     *          ),
     *	   ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "Update user info",
     *         @SWG\Schema(ref = "#/definitions/User")
     *     ),
     *     @SWG\Response(
     *         response = 401,
     *         description = "Unauthorized"
     *     ),
     *     @SWG\Response(
     *         response = 404,
     *         description = "Not Found"
     *     ),
     * )
     */
    /** User END **/
}
