## SWAGGER support for Phalcon

### What it does?

This bundle/plugin parses your source code and generates swagger documentation for your API.

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
                getenv('SWAGGER_JSON_URI') => 'indexAction',
                '/docs' => 'docsAction',
            ],
        ],
    ],
```
The first route is the route of the json response, which swagger needs in order to render the documentation. 
This URL maps to a controller which scans your project for annotations.

The second route is the route where you want to be able to access the documentation.

I am using an env file for my configurations, therefore the first route is a parameter in my env file. 
The reason for this is that the URL for the json response needs to be registered in the DI as well.
#### DI
The bundle is expecting an entry with the name swagger in your di container.

Here I am loading the configuration from an .env file:

```
'swagger' => [
        'path' => APP_PATH . '/src',
        'host' => getenv('SWAGGER_HOST'),
        'schemes' => explode(',', getenv('SWAGGER_SCHEMES')),
        'basePath' => getenv('SWAGGER_BASEPATH'),
        'version' => getenv('SWAGGER_VERSION'),
        'title' => getenv('SWAGGER_TITLE'),
        'description' => getenv('SWAGGER_DESCRIPTION'),
        'email' => getenv('SWAGGER_EMAIL'),
        'jsonUri' => getenv('SWAGGER_JSON_URI'),
        'exclude' => explode(',', getenv('SWAGGER_EXCLUDE')),
    ],
```

And here I am registering the di:

```
    /**
     * Configure Swagger
     */
    protected function initDocs()
    {
        /** @var PhConfig $config */
        $config = $this->diContainer->getShared('config');
        $this->diContainer->setShared(
            'swagger',
            function () use ($config) {
                return $config->get('swagger')->toArray();
            }

        );
    }
```

Now, it is up to you how you get the swagger entry into your di container. 
You can just go and register all the values statically.

These values are expected and mandatory:
- path - a path to your source dir, which should be scanned, ideally ```APP_PATH. '/src'```
- host - your domain name e.g. my-awesome-api.com
- schemes [array] - schemes which are supported e.g. http,https(this must be an array)
- basePath - url base  path e.g. /
- version - your API version e.g. 0.0.1
- title - title of your application
- description  - description of your application
- email - contact email
- jsonUri - the url you configured for the json response e.g. /swagger-json

These values are optional:
- exclude [string|array] - a path(s) to exclude from scanning, ex. ```APP_PATH. '/src/path_to_exclude/'``` or ```[APP_PATH. '/src/path_to_exclude_1/', APP_PATH. '/src/path_to_exclude_2/']```


### Usage 

The usage is the same as with the standard Swagger library, see https://github.com/zircote/swagger-php for more info.

I am using just a basic configuration for Swagger but if you would like to extend it, use ignored folders etc. 
I recommend to have an annotation on your base controller like this:

```
/**
 * @SWG\Swagger(
 *     schemes={"http","https"},
 *     host="api.host.com",
 *     basePath="/",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="This is my website cool API",
 *         description="Api description...",
 *         termsOfService="",
 *         @SWG\Contact(
 *             email="contact@mysite.com"
 *         ),
 *         @SWG\License(
 *             name="Private License",
 *             url="URL to the license"
 *         )
 *     ),
 *     @SWG\ExternalDocumentation(
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
 * @SWG\Definition(required={"email", "name", "password"}, type="object", @SWG\Xml(name="User"))
 */
class User extends Model
{
    /**
     * @SWG\Property(name="id", type="string", description="UUID")
     * @var int
     */
    public $id;

    /**
     * @SWG\Property(name="name", type="string")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(name="email", type="string")
     * @var string
     */
    public $email;

    /**
     * @SWG\Property(name="password", type="string")
     * @var string
     */
    public $password;
}
```

And an example controller for login:

```
/**
     *
     * @SWG\POST(
     *   path="/login",
     *   summary="Login",
     *   produces={"application/json"},
     *     @SWG\Parameter(
     *     in="formData",
     *     type="string",
     *     name="email",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/User")
     *   ),
     *     @SWG\Parameter(
     *     in="formData",
     *     type="string",
     *     name="password",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/User")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Returns a JWT token for authorization"
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Not found User, Invalid password"
     *   ),
     *   @SWG\Response(
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

