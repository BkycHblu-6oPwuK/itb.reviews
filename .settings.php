<?php

use Itb\Reviews\ComponentParams;

return [
	'controllers' => [
		'value' => [
			'defaultNamespace' => '\\Itb\\Reviews\\Controllers',
		],
		'readonly' => true,
	],
	'services' => [
		'value' => [
			\Itb\Reviews\Import\ImportFrom2Gis::class => [
				'className' => \Itb\Reviews\Import\ImportFrom2Gis::class,
				'constructorParams' => static function (){
					$options = \Itb\Reviews\Options::getInstance();
					return [new \Itb\Reviews\Services\ReviewsService(new ComponentParams()), $options->twoGisBranches, $options->twoGisKey];
				}
			],
			\Itb\Reviews\Contracts\CreatorContract::class => [
				'className' => Itb\Reviews\Services\ReviewCreatorService::class,
			],
			\Itb\Reviews\Contracts\FileUploaderContract::class => [
				'className' => Itb\Reviews\Services\UploadService::class,
			],
		],
		'readonly' => true,
	],
];
