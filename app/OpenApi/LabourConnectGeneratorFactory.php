<?php

namespace App\OpenApi;

use L5Swagger\CustomGeneratorInterface;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Generator as OpenApiGenerator;

/**
 * Custom OpenAPI generator factory for l5-swagger.
 *
 * l5-swagger's default analyser only registers the PHP 8 attribute factory.
 * LabourConnect documents its API using docblock annotations (@OA\...), so we
 * build a generator whose analyser includes BOTH the docblock factory and the
 * attribute factory — matching swagger-php's own defaults.
 */
class LabourConnectGeneratorFactory implements CustomGeneratorInterface
{
    public function create(): OpenApiGenerator
    {
        $generator = new OpenApiGenerator();

        $generator->setAnalyser(new ReflectionAnalyser([
            new AttributeAnnotationFactory(),
            new DocBlockAnnotationFactory(),
        ]));

        return $generator;
    }
}
