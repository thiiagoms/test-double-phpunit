<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    public function testApiDeveREtornarArrayDeLeiloes(): void
    {
        $response = file_get_contents('http://localhost:8000');

        $this->assertStringContainsString('200 OK', $http_response_header[0]);
        $this->assertIsArray(json_decode($response));
    }
}