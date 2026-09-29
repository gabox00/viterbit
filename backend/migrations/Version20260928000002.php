<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260928000002 extends AbstractMigration
{
    private const array JOB_POSITIONS = [
        [
            'hash' => '0199a0e0-0000-7000-8000-000000000001',
            'title' => 'Senior Backend Engineer (PHP)',
            'description' => 'Design and evolve our modular monolith. You will own bounded contexts end to end, from domain modelling to asynchronous integrations, and mentor other engineers on clean architecture and testing.',
            'required_skills' => ['PHP', 'Symfony', 'DDD', 'RabbitMQ', 'SQL', 'Docker', 'PHPUnit'],
        ],
        [
            'hash' => '0199a0e0-0000-7000-8000-000000000002',
            'title' => 'Frontend Engineer (React)',
            'description' => 'Build fast, accessible interfaces for recruiters and candidates. You will turn product ideas into small reusable components and keep the UI consistent, typed and well tested.',
            'required_skills' => ['React', 'TypeScript', 'Tailwind', 'Vite', 'Vitest', 'Playwright', 'Accessibility'],
        ],
        [
            'hash' => '0199a0e0-0000-7000-8000-000000000003',
            'title' => 'QA Automation Engineer',
            'description' => 'Own the quality strategy of the product. You will design automated test suites across the stack and make sure every release is safe to ship.',
            'required_skills' => ['Playwright', 'Cypress', 'PHPUnit', 'CI/CD', 'API testing', 'TypeScript'],
        ],
        [
            'hash' => '0199a0e0-0000-7000-8000-000000000004',
            'title' => 'DevOps Engineer',
            'description' => 'Keep our platform reliable and observable. You will automate infrastructure, improve deployment pipelines and help teams run their services with confidence.',
            'required_skills' => ['Docker', 'Kubernetes', 'Terraform', 'AWS', 'CI/CD', 'Linux', 'Prometheus'],
        ],
    ];

    public function getDescription(): string
    {
        return 'Seed position catalog';
    }

    public function up(Schema $schema): void
    {
        foreach (self::JOB_POSITIONS as $jobPosition) {
            $this->addSql(
                'INSERT INTO job_position (hash, title, description, required_skills, created_at) VALUES (:hash, :title, :description, :required_skills, :created_at)',
                [
                    'hash' => $jobPosition['hash'],
                    'title' => $jobPosition['title'],
                    'description' => $jobPosition['description'],
                    'required_skills' => json_encode($jobPosition['required_skills'], JSON_THROW_ON_ERROR),
                    'created_at' => '2026-09-28 00:00:00.000000',
                ],
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM job_position');
    }
}
