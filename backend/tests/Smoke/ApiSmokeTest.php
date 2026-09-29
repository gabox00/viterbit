<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Tests\Functional\ApiTestCase;
use App\Tests\Mother\JobApplicationMother;
use PHPUnit\Framework\Attributes\TestWith;

final class ApiSmokeTest extends ApiTestCase
{
    #[TestWith(['/api/health'])]
    #[TestWith(['/api/v1/job-positions'])]
    #[TestWith(['/api/v1/job-positions/'.JobApplicationMother::BACKEND_JOB_POSITION_HASH])]
    #[TestWith(['/api/v1/job-applications'])]
    public function testEndpointRespondsSuccessfullyWithJson(string $uri): void
    {
        $this->client->request('GET', $uri);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testApplicationDetailRespondsSuccessfully(): void
    {
        $hash = $this->submitApplication();

        $this->client->request('GET', '/api/v1/job-applications/'.$hash);

        self::assertResponseIsSuccessful();
    }
}
