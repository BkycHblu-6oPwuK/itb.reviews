<?php

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
					return [new \Itb\Reviews\Services\ReviewsService($options), $options->getTwoGisBranches(), $options->getTwoGisKey()];
				}
			],
			\Itb\Reviews\Contracts\CreatorContract::class => [
				'className' => Itb\Reviews\Services\ReviewCreatorService::class,
				'constructorParams' => static function (){
					return [\Itb\Reviews\Options::getInstance()];
				}
			],
			\Itb\Reviews\Contracts\FileUploaderContract::class => [
				'className' => Itb\Reviews\Services\UploadService::class,
			],
		],
		'readonly' => true,
	],
];
