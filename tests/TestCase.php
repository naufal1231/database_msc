<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Database\Events\ConnectionEstablished::class,
            function ($event) {
                if ($event->connection->getDriverName() === 'sqlite') {
                    $event->connection->getPdo()->sqliteCreateFunction('YEAR', function ($date) {
                        return $date ? (int) date('Y', strtotime($date)) : null;
                    }, 1, \PDO::SQLITE_DETERMINISTIC);
                }
            }
        );

        try {
            $db = $app->make('db');
            if ($db->connection()->getDriverName() === 'sqlite') {
                $db->connection()->getPdo()->sqliteCreateFunction('YEAR', function ($date) {
                    return $date ? (int) date('Y', strtotime($date)) : null;
                }, 1, \PDO::SQLITE_DETERMINISTIC);
            }
        } catch (\Throwable $e) {
        }

        return $app;
    }

    public function createSuperAdmin(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->superAdmin()->create($attributes);
    }

    public function createAdmin(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->admin()->create($attributes);
    }

    public function createParent(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->parentUser()->create($attributes);
    }

    public function actingAsSuperAdmin(?\App\Models\User $user = null): static
    {
        return $this->actingAs($user ?? $this->createSuperAdmin());
    }

    public function actingAsAdmin(?\App\Models\User $user = null): static
    {
        return $this->actingAs($user ?? $this->createAdmin());
    }

    public function actingAsParent(?\App\Models\User $user = null): static
    {
        return $this->actingAs($user ?? $this->createParent());
    }
}
