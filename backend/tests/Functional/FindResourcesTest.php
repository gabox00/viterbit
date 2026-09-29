<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Mother\JobApplicationMother;

final class FindResourcesTest extends ApiTestCase
{
    public function testReturnsTheFullApplicationDetail(): void
    {
        $hash = $this->submitApplication();

        $detail = $this->getJson('/api/v1/job-applications/'.$hash);

        self::assertSame(
            ['hash', 'job_position_hash', 'candidate_full_name', 'candidate_email', 'candidate_phone', 'notes', 'cv_text', 'status', 'ai_summary', 'ai_score', 'applied_at', 'enriched_at'],
            array_keys($detail),
        );
        self::assertSame(JobApplicationMother::CV_TEXT, $detail['cv_text']);
    }

    public function testReturns404ForUnknownApplications(): void
    {
        $this->client->request('GET', '/api/v1/job-applications/0199a0e0-1111-7000-8000-000000000009');

        self::assertResponseStatusCodeSame(404);
        self::assertSame('Job application "0199a0e0-1111-7000-8000-000000000009" not found.', $this->responseJson()['error']);
    }

    public function testReturnsAJobPositionWithSnakeCaseFields(): void
    {
        $jobPosition = $this->getJson('/api/v1/job-positions/'.JobApplicationMother::BACKEND_JOB_POSITION_HASH);

        self::assertSame(['hash', 'title', 'description', 'required_skills'], array_keys($jobPosition));
        self::assertSame('Senior Backend Engineer (PHP)', $jobPosition['title']);
    }

    public function testReturns404ForUnknownJobPositions(): void
    {
        $this->client->request('GET', '/api/v1/job-positions/0199a0e0-0000-7000-8000-000000000099');

        self::assertResponseStatusCodeSame(404);
    }
}
