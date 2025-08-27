<?php

namespace App\Tests;

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpWord\IOFactory;
use ReflectionClass;
use ReflectionMethod;
use RonasIT\Support\Tests\TestCase as BaseTestCase;
use RonasIT\Support\AutoDoc\Tests\AutoDocTestCaseTrait;

abstract class TestCase extends BaseTestCase
{
    use AutoDocTestCaseTrait;

    protected bool $forceExportMode = false;

    protected static array $jsonFields = [];

    protected ?string $testCaseName = null;
    protected array $requiredOriginStates = [];
    protected array $testCaseOriginStates = [];

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

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow($this->testNow);

        $this->setTestCase();
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

    public function assertEqualsTextFixture(string $fixture, string $data, bool $exportMode = false): void
    {
        if ($exportMode || $this->forceExportMode) {
            $this->exportContent($data, $fixture);
        }

        $this->assertEquals($this->getFixture($fixture), $data);
    }

    public function getFixturePath(string $fixtureName): string
    {
        $reflectionClass = new ReflectionClass($this);

        $classDir = dirname($reflectionClass->getFileName());
        $className = $reflectionClass->getShortName();

        return "{$classDir}/fixtures/{$className}/{$this->testCaseName}/{$fixtureName}";
    }

    public function getPhpWordFileText(string $path): string
    {
        $phpWord = IOFactory::load($path);

        $content = '';

        foreach($phpWord->getSections() as $section) {
            foreach($section->getElements() as $element) {
                if (method_exists($element, 'getElements')) {
                    foreach($element->getElements() as $childElement) {
                        if (method_exists($childElement, 'getText')) {
                            $content .= $childElement->getText() . ' ';
                        }
                        else if (method_exists($childElement, 'getContent')) {
                            $content .= $childElement->getContent() . ' ';
                        }
                    }
                }
            }
        }

        return $content;
    }

    protected function setTestCase()
    {
        $reflection = new ReflectionMethod(get_class($this) . "::" . $this->getName(false));
        $docComment = $reflection->getDocComment();

        if (preg_match('/@testCase\s+([a-zA-Z0-9_]+)/', $docComment, $matches)) {
            $testCaseName = $matches[1];
        } elseif (preg_match('/@providedTestCase/', $docComment)) {
            $testCaseName = Arr::last($this->getProvidedData());
        }

        if (isset($testCaseName)) {
            $this->loadSpecificTestDump("/{$testCaseName}/dump.sql");

            $this->testCaseName = $testCaseName;
        }

        $this->loadOriginStates();
    }

    protected function loadOriginStates()
    {
        if (!isset($this->testCaseOriginStates[$this->testCaseName])) {
            foreach ($this->requiredOriginStates as $table) {
                $this->testCaseOriginStates[$this->testCaseName][$table] = $this->getDataSet($table);
            }
        }
    }

    protected function getOriginState(string $table): Collection
    {
        return $this->testCaseOriginStates[$this->testCaseName][$table];
    }

    protected function loadSpecificTestDump(?string $dumpFile = null)
    {
        if (is_null($dumpFile)) {
            $dumpFile = 'dump.sql';
            $clearDb = true;
        } else {
            $clearDb = false;
        }

        $dump = $this->getFixture($dumpFile, false);

        if (empty($dump)) {
            return;
        }

        $dump = preg_replace('/--.*/', '', $dump);

        if ($clearDb) {
            $databaseTables = $this->getTables();
            $scheme = config('database.default');

            $this->clearDatabase($scheme, $databaseTables, array_merge($this->postgisTables, $this->truncateExceptTables));
        }

        DB::unprepared($dump);

        if (config('database.default') === 'pgsql') {
            $this->prepareSequences($this->getTables());
        }
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

    public function assertChangesEqualsFixture(string $table, string $fixture = null, Collection $originData = null, bool $exportMode = false)
    {
        $this->getJsonFields($table);

        $fixture = $fixture ?? "{$table}__state.json";
        $originData = $originData ?? $this->getOriginState($table);

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

    public function assertNoChanges(string $table, Collection $originData = null)
    {
        $originData = $originData ?? $this->getOriginState($table);

        $changes = $this->getChanges($table, $originData);

        $this->assertEquals([
            'updated' => [],
            'created' => [],
            'deleted' => []
        ], $changes);
    }

    protected function loadTestDump(): void
    {
        $databaseTables = $this->getTables();
        $scheme = config('database.default');

        $this->clearDatabase($scheme, $databaseTables, array_merge($this->postgisTables, $this->truncateExceptTables));

        $dump = $this->getFixture('dump.sql', false);

        if (empty($dump)) {
            return;
        }

        DB::unprepared($dump);
    }

    protected function actingAsById(int $userId): self
    {
        $user = User::find($userId);

        return $this->actingAs($user);
    }
}
