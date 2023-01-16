<?php

namespace App\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RonasIT\Support\Tests\TestCase as BaseTestCase;
use RonasIT\Support\AutoDoc\Tests\AutoDocTestCaseTrait;

abstract class TestCase extends BaseTestCase
{
    use AutoDocTestCaseTrait;

    protected bool $forceExportMode = false;

    protected static array $jsonFields = [];

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->loadEnvironmentFrom('.env.testing');
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    public function tearDown(): void
    {
        $this->saveDocumentation();

        parent::tearDown();
    }

    public function assertEqualsFixture(string $fixture, $data, bool $exportMode = false): void
    {
        if ($exportMode || $this->forceExportMode) {
            $this->exportJson($fixture, $data);
        }

        $this->assertEquals($this->getJsonFixture($fixture), $data);
    }

    protected function getChanges(string $table, Collection $originData): array
    {
        $updatedData = $this->getDataSet($table);

        $updatedRecords = [];
        $deletedRecords = [];

        $originData->each(function ($originItem) use (&$updatedData, &$updatedRecords, &$deletedRecords) {
            $updatedItemIndex = $updatedData->search(function ($updatedItem) use ($originItem) {
                return $updatedItem['id'] === $originItem['id'];
            });

            if ($updatedItemIndex === false) {
                $deletedRecords[] = $originItem;
            } else {
                $updatedItem = $updatedData->get($updatedItemIndex);
                $changes = array_diff_assoc($updatedItem, $originItem);

                if (!empty($changes)) {
                    $updatedRecords[] = array_merge(['id' => $originItem['id']], $changes);
                }

                $updatedData->forget($updatedItemIndex);
            }
        });

        return [
            'updated' => $this->prepareChanges($table, $updatedRecords),
            'created' => $this->prepareChanges($table, $updatedData->values()->toArray()),
            'deleted' => $this->prepareChanges($table, $deletedRecords)
        ];
    }

    protected function prepareChanges(string $table, array $changes): array
    {
        $jsonFields = Arr::get(self::$jsonFields, $table);

        if (empty($jsonFields)) {
            return $changes;
        }

        return array_map(function ($item) use ($jsonFields) {
            foreach ($jsonFields as $jsonField) {
                if (Arr::has($item, $jsonField)) {
                    $item[$jsonField] = json_decode($item[$jsonField], true);
                }
            }

            return $item;
        }, $changes);
    }

    protected function getDataSet(string $table, string $orderField = 'id', array $where = []): Collection
    {
        return DB::table($table)
            ->where($where)
            ->orderBy($orderField)
            ->get()
            ->map(function ($record) {
                return (array) $record;
            });
    }

    public function assertChangesEqualsFixture(string $table, string $fixture, Collection $originData, bool $exportMode = false)
    {
        $this->getJsonFields($table);

        $changes = $this->getChanges($table, $originData);

        $this->assertEqualsFixture($fixture, $changes, $exportMode);
    }

    protected function getJsonFields(string $table)
    {
        if (!isset(self::$jsonFields[$table])) {
            self::$jsonFields[$table] = [];

            $fields = Schema::getColumnListing($table);

            foreach ($fields as $field) {
                $type = Schema::getColumnType($table, $field);

                if (($type === 'json') || ($type === 'jsonb')) {
                    self::$jsonFields[$table][] = $field;
                }
            }
        }
    }

    public function assertNoChanges(string $table, Collection $originData)
    {
        $changes = $this->getChanges($table, $originData);

        $this->assertEquals([
            'updated' => [],
            'created' => [],
            'deleted' => []
        ], $changes);
    }
}
