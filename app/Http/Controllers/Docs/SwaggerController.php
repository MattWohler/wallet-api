<?php declare(strict_types=1);

namespace App\Http\Controllers\Docs;

use App\Services\SwaggerYmlLoader;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class SwaggerController extends BaseController
{
    public function getSwaggerJson(Request $request, SwaggerYmlLoader $swaggerYmlLoader)
    {
        $json = $swaggerYmlLoader->parse(resource_path('swagger.yml'));
        $options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

        if ($request->input('pretty', null) !== null) {
            $options |= JSON_PRETTY_PRINT;
        }

        return response()->json($json, 200, [], $options);
    }

    public function getSwaggerUi(Request $request)
    {
        return file_get_contents(resource_path('views/swagger-ui.html'));
    }

    public function getDocumentation(Request $request)
    {
        return file_get_contents(resource_path('views/doc.html'));
    }
}
