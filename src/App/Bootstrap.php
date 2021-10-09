<?php
/**
 * Created by PhpStorm.
 * User: Corentin
 * Date: 02/04/2019
 * Time: 09:38
 */

namespace App;

use App\Src\App;
use App\Src\ServiceContainer\ServiceContainer;
use Controllers\Usercontroller;
use Database\Database;
use Model\Actor\PostActor;
use Model\Actor\RepostActor;
use Model\Actor\UserActor;
use Model\Finder\ObjectFinder;
use Model\Finder\PostFinder;
use Model\Finder\RepostFinder;
use Model\Finder\UserFinder;
use App\Src\Response\Response;
use App\Src\Request\Request;

$app = new App(new ServiceContainer());

$app->setService('database', new Database(
	getenv('MYSQL_ADDON_HOST'),
	getenv('MYSQL_ADDON_DB'),
	getenv('MYSQL_ADDON_USER'),
	getenv('MYSQL_ADDON_PASSWORD'),
	getenv('MYSQL_ADDON_PORT')
	));

$app->setService('userFinder', new UserFinder($app));

$app->setService('postFinder', new PostFinder($app));

$app->setService('repostFinder', new RepostFinder($app));

$app->setService('userActor', new UserActor($app));

$app->setService('postActor', new PostActor($app));

$app->setService('repostActor', new RepostActor($app));

$app->setService('render', function(String $template, Array $params = []) {

    ob_start();
    include __DIR__ . '/../Views/' . $template . '.php';
    $content = ob_get_contents();
    ob_end_clean(); // Does not sent the content of the buffer to the user

    if($template === '404')
    {
        $response = new Response($content, 404, ["HTTP/1.0 404 Not Found"]);
        return $response;
    }

    return $content;
}
);

$app->setService('redirect', function($location) {
    header("Location: $location");
    die();
});

$routing = new Routing($app);

$routing->setup();

return $app;
