<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\CleansDatabase;
use App\Tests\Mother\JobApplicationMother;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ApiTestCase extends WebTestCase
{
    use CleansDatabase;

    protected KernelBrowser $client;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->cleanDatabase();
    }

    /** @param array<string, mixed> $payload */
    protected function postJson(string $uri, array $payload): void
    {
        $this->client->request('POST', $uri, server: ['CONTENT_TYPE' => 'application/json'], content: json_encode($payload, JSON_THROW_ON_ERROR));
    }

    /** @return array<mixed> */
    protected function getJson(string $uri): array
    {
        $this->client->request('GET', $uri);

        return $this->responseJson();
    }

    /** @return array<mixed> */
    protected function responseJson(): array
    {
        /** @var array<mixed> */
        return json_decode((string) $this->client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
    }

    /** @param array<string, mixed> $overrides */
    protected function submitApplication(array $overrides = []): string
    {
        $this->postJson('/api/v1/job-applications', array_merge(self::validPayload(), $overrides));
        self::assertResponseStatusCodeSame(201);

        $body = $this->responseJson();
        self::assertIsString($body['hash']);

        return $body['hash'];
    }

    /** @return array<string, mixed> */
    protected static function validPayload(): array
    {
        return [
            'job_position_hash' => JobApplicationMother::BACKEND_JOB_POSITION_HASH,
            'candidate_full_name' => 'Ada Lovelace',
            'candidate_email' => 'ada@example.com',
            'candidate_phone' => '+34 600 000 000',
            'notes' => 'Available immediately.',
            'cv_text' => JobApplicationMother::CV_TEXT,
        ];
    }
}
