<?php

use Beeralex\Reviews\ComponentParams;

return [
	'controllers' => [
		'value' => [
			'defaultNamespace' => '\\Beeralex\\Reviews\\Controllers',
		],
		'readonly' => true,
	],
	'services' => [
		'value' => [
			\Beeralex\Reviews\Import\ImportFrom2Gis::class => [
				'className' => \Beeralex\Reviews\Import\ImportFrom2Gis::class,
				'constructorParams' => static function (){
					$options = \Beeralex\Reviews\Options::getInstance();
					return [new \Beeralex\Reviews\Services\ReviewsService(new ComponentParams()), $options->twoGisBranches, $options->twoGisKey];
				}
			],
			\Beeralex\Reviews\Contracts\CreatorContract::class => [
				'className' => Beeralex\Reviews\Services\ReviewCreatorService::class,
			],
			\Beeralex\Reviews\Contracts\FileUploaderContract::class => [
				'className' => Beeralex\Reviews\Services\UploadService::class,
			],
		],
		'readonly' => true,
	],
];
