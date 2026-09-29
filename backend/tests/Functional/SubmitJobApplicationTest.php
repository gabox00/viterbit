<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\JobApplication\Domain\Event\JobApplicationSubmitted;
use App\Tests\Double\UnavailableTransport;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

final class SubmitJobApplicationTest extends ApiTestCase
{
    public function testCreatesAReceivedApplicationWithAppliedAt(): void
    {
        $hash = $this->submitApplication();

        $detail = $this->getJson('/api/v1/job-applications/'.$hash);

        self::assertResponseIsSuccessful();
        self::assertSame('received', $detail['status']);
        self::assertSame('ada@example.com', $detail['candidate_email']);
        self::assertIsString($detail['applied_at']);
        self::assertNotFalse(\DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $detail['applied_at']));
        self::assertNull($detail['ai_summary']);
        self::assertNull($detail['ai_score']);
    }

    public function testEnqueuesTheEnrichment(): void
    {
        $hash = $this->submitApplication();

        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.enrich_cv_on_job_application_submitted');
        $sent = $transport->getSent();

        self::assertCount(1, $sent);
        self::assertInstanceOf(JobApplicationSubmitted::class, $sent[0]->getMessage());
        self::assertSame($hash, $sent[0]->getMessage()->jobApplicationHash);
    }

    public function testDoesNotStoreTheApplicationWhenTheEventCannotBePublished(): void
    {
        self::getContainer()->set('messenger.transport.enrich_cv_on_job_application_submitted', new UnavailableTransport());

        $this->postJson('/api/v1/job-applications', self::validPayload());

        self::assertResponseStatusCodeSame(500);
        self::assertSame(0, $this->entityManager()->getConnection()->fetchOne('SELECT COUNT(*) FROM job_application'));
    }

    public function testStoresBlankOptionalFieldsAsNull(): void
    {
        $hash = $this->submitApplication(['candidate_phone' => '', 'notes' => '   ']);

        $detail = $this->getJson('/api/v1/job-applications/'.$hash);

        self::assertNull($detail['candidate_phone']);
        self::assertNull($detail['notes']);
    }

    public function testRejectsInvalidPayloadsWithFieldErrors(): void
    {
        $this->postJson('/api/v1/job-applications', [
            'job_position_hash' => 'not-a-uuid',
            'candidate_full_name' => '',
            'candidate_email' => 'not-an-email',
            'cv_text' => 'too short',
        ]);

        self::assertResponseStatusCodeSame(422);
        $errors = $this->responseJson()['errors'];
        self::assertIsArray($errors);
        self::assertEqualsCanonicalizing(['job_position_hash', 'candidate_full_name', 'candidate_email', 'cv_text'], array_keys($errors));
    }

    public function testRejectsUnknownJobPositions(): void
    {
        $this->postJson('/api/v1/job-applications', array_merge(self::validPayload(), ['job_position_hash' => '0199a0e0-0000-7000-8000-000000000099']));

        self::assertResponseStatusCodeSame(422);
        self::assertSame('Job position "0199a0e0-0000-7000-8000-000000000099" does not exist.', $this->responseJson()['error']);
    }
}
