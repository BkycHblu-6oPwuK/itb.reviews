<?php
namespace Beeralex\Reviews\Contracts;

interface FileUploaderContract
{
    /** @param array $files $_FILES */
    public function upload(array $files): array;
}