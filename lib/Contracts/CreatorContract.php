<?php

namespace Itb\Reviews\Contracts;

use Itb\Reviews\ComponentParams;

interface CreatorContract
{
    /**
     * @param array $form {eval: int, review: string, contact: string, user_name: string, offer: int, answer: string, platform: string, external_id: string, active: bool}
     * @param array $files from $_FILES
     */
    public function create(array $form, array $files, ComponentParams $params): int;
}
