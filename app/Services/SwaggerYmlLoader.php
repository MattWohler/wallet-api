<?php declare(strict_types=1);

namespace App\Services;

use Laravel\Lumen\Application;
use Symfony\Component\Yaml\Yaml;

class SwaggerYmlLoader
{
    /** @var Application */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param  string  $path
     * @return mixed
     */
    public function parse(string $path)
    {
        $json = Yaml::parse((string) file_get_contents($path), Yaml::PARSE_OBJECT | Yaml::PARSE_OBJECT_FOR_MAP);

        $json->host = config('app.url');
        $json->schemes[0] = config('app.scheme');

        return $json;
    }
}
