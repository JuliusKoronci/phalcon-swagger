## SWAGGER support for Phalcon

### What it does?

This bundle/plugin parses your source code and generates Openpi documentation for your API.

### How does it work?
The bundle has one controller with 2 actions. 

- The first action parses your source code and returns a json
- The second controller renders a view to display Swagger-UI

Swagger-UI uses CDN's for its assets, therefor no js or css files are included!

### Hot to install?

You can install the bundle via composer or just download the git repos and paste it into your project dir.

There are 2 important things which needs to be configured:
#### Routing
You must configure 2 routes in order to get it working:

```
    [
        'class' => \Igsem\Docs\Controllers\DocsController::class,
        'methods' => [
            'get' => [
                getenv('OPENAPI_JSON_URI') => 'indexAction',
                '/docs' => 'docsAction',
            ],
        ],
    ],
```
The first route is the route of the json response, which openapi needs in order to render the documentation. 
This URL maps to a controller which scans your project for annotations.

The second route is the route where you want to be able to access the documentation.

I am using an env file for my configurations, therefore the first route is a parameter in my env file. 
The reason for this is that the URL for the json response needs to be registered in the DI as well.
#### DI
The bundle is expecting an entry with the name openapi in your di container.

Here I am loading the configuration from an .env file:

```
'openapi' => [
    'path' => APP_PATH. '/src',
    'version' => getenv('OPENAPI_VERSION'),
    'title' => getenv('OPENAPI_TITLE'),
    'description' => getenv('OPENAPI_DESCRIPTION'),
    'email' => getenv('OPENAPI_EMAIL'),
    'jsonUri' => getenv('OPENAPI_JSON_URI'),
    'servers' => [
      "url" => getenv('OPENAPI_HOST'),
      "description" => getenv('OPENAPI_HOST_DESCRIPTION'),
      "variables" => [
        "basePath" => [
          "default" => getenv('OPENAPI_BASEPATH')
        ]
      ]
    ]
],
```

And here I am registering the di:

```
    /**
     * Configure Openapi
     */
    protected function initDocs()
    {
        /** @var PhConfig $config */
        $config = $this->diContainer->getShared('config');
        $this->diContainer->setShared(
            'openapi',
            function () use ($config) {
                return $config->get('openapi')->toArray();
            }

        );
    }
```

Now, it is up to you how you get the openapi entry into your di container. 
You can just go and register all the values statically.

These values are expected and mandatory:
- path - a path to your source dir, which should be scanned, ideally ```APP_PATH. '/src'```
- servers must be an array, it contains all servers. A server contain basePath and uri of the openapi
- version - your API version e.g. 0.0.1
- title - title of your application
- description  - description of your application
- email - contact email
- jsonUri - the url you configured for the json response e.g. /swagger-json

These values are optional:
- exclude [string|array] - a path(s) to exclude from scanning, ex. ```APP_PATH. '/src/path_to_exclude/'``` or ```[APP_PATH. '/src/path_to_exclude_1/', APP_PATH. '/src/path_to_exclude_2/']```


### Usage 

The usage is the same as with the standard Openapi library, see https://github.com/zircote/swagger-php for more info.

I am using just a basic configuration for Openapi but if you would like to extend it, use ignored folders etc. 
I recommend to have an annotation on your base controller like this:

```
/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="This is my website cool API",
 *         description="Api description...",
 *         termsOfService="",
 *         @OA\Contact(
 *             email="contact@mysite.com"
 *         ),
 *         @OA\License(
 *             name="Private License",
 *             url="URL to the license"
 *         )
 *     ),
 *    @OA\Server(
 *        description=""
 *        url="api.host.com"
 *    ),
 *     @OA\ExternalDocumentation(
 *         description="Find out more about my website",
 *         url="http..."
 *     )
 * )

 */

class BaseController
```

!Please note that the configuration is overwriting the annotation, therefore use this as an extend only!

![alt text](https://github.com/JuliusKoronci/phalcon-swagger/blob/master/screen.png "Screen of Swagger UI")

### Examples

Here is how to annotate models: 

```

/**
 * @OA\Schema(required={"email", "name", "password"}, @OA\Xml(name="User"))
 **/
 
class User extends Model
{
    /**
     * @OA\Property(name="id", type="string", description="UUID")
     * @var int
     */
    public $id;

    /**
     * @OA\Property(name="name", type="string")
     * @var string
     */
    public $name;

    /**
     * @OA\Property(name="email", type="string")
     * @var string
     */
    public $email;

    /**
     * @OA\Property(name="password", type="string")
     * @var string
     */
    public $password;
}
```

And an example controller for login:

```
/**
     * @OA\Post(
     *   path="/login",
     *   summary="Login",
     *     @OA\Parameter(
     *     in="query",
     *     name="email",
     *     required=true,
     *     @OA\Schema(ref="#/definitions/User")
     *   ),
     *     @OA\Parameter(
     *     in="query",
     *     name="password",
     *     required=true,
     *     @OA\Schema(ref="#/definitions/User")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Returns a JWT token for authorization",
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Not found User, Invalid password"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation of formData failed"
     *   )
     * )
     * @return string
     */
    public function loginAction()
    {
        ...
```

![alt text](https://github.com/JuliusKoronci/phalcon-swagger/blob/master/login.png "Screen of Swagger UI Login")

### What is missing

I came up with the library quiet fast and had no time to write tests or test it on more examples. 
The library should work and I am using it in my projects. Everyone is free to use or modify it as he sees fit. 
I will be more than happy to have some pull requests :) if someone is interested.

