<?php

namespace App\Helpers\ApiResponse;

class ErrorResult extends Result
{
    public function __construct(?string $message, bool $isOk = false, int $code = 500)
    {
        parent::__construct();
        $this->isOk = $isOk;
        $this->message = $message ?? __('messages.task_does_not_complete_successfully');
        $this->code = $code;

    }
}
