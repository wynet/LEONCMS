<?php
declare(strict_types=1);

namespace app\exception;

use think\Exception;

class ApiException extends Exception
{
    protected $apiData = [];
    
    public function __construct(string $message = "", int $code = 400, $data = [])
    {
        $this->message = $message;
        $this->code = $code;
        $this->apiData = $data;
        parent::__construct($message, $code);
    }
    
    public function getApiData()
    {
        return $this->apiData;
    }
} 