<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPFramework\Migrator;
use PHPFramework\Migration;

// ─── Тестовые классы миграций ────────────────────────────────────────────────

class CreateTestTable extends Migration
{
    public function up(): void
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS test_migrator_table (
                id   INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL
            )
        ");
    }

    public function down(): void
    {
        $this->execute("DROP TABLE IF EXISTS test_migrator_table");
    }
}

class AddColumnToTestTable extends Migration
{
    public function up(): void
    {
        $this->execute("ALTER TABLE test_migrator_table ADD COLUMN email TEXT");
    }

    public function down(): void
    {
        // SQLite не поддерживает DROP COLUMN до 3.35, поэтому просто удалим таблицу
        $this->execute("DROP TABLE IF EXISTS test_migrator_table");
    }
}

// ─── Тестовый Migrator через SQLite in-memory ─────────────────────────────────

class MigratorTest extends TestCase
{
    protected \PDO $pdo;
    protected string $tmpDir;
    protected Migrator $migrator;

    protected function setUp(): void
    {
        parent::setUp();

        // In-memory SQLite вместо MySQL — никаких внешних зависимостей
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Временная папка с файлами миграций
        $this->tmpDir = sys_get_temp_dir() . '/phpfw_migrations_' . uniqid('', true);
        mkdir($this->tmpDir, 0755, true);

        $this->migrator = new class($this->pdo, $this->tmpDir) extends Migrator {
            // Переопределяем ensureMigrationsTable для SQLite
            protected function ensureMigrationsTable(): void
            {
                $this->pdo->exec('
                    CREATE TABLE IF NOT EXISTS migrations (
                        id        INTEGER PRIMARY KEY AUTOINCREMENT,
                        migration TEXT NOT NULL,
                        batch     INTEGER NOT NULL
                    )
                ');
            }
        };
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Удаляем временные файлы
        foreach (glob($this->tmpDir . '/*.php') as $f) {
            unlink($f);
        }
        rmdir($this->tmpDir);
    }

    // ── helpers ──────────────────────────────────────────────────────────────

    /**
     * Создать фиктивный файл миграции, который просто включает нужный класс
     */
    private function writeMigrationFile(string $filename, string $className): void
    {
        file_put_contents(
            $this->tmpDir . '/' . $filename . '.php',
            "<?php\nrequire_once '" . __FILE__ . "';\n"
        );
    }

    // ── tests ─────────────────────────────────────────────────────────────────

    public function testFilenameToClassName(): void
    {
        $this->assertEquals(
            'CreateUsersTable',
            Migrator::filenameToClassName('2024_01_01_000000_create_users_table')
        );

        $this->assertEquals(
            'AddEmailToUsers',
            Migrator::filenameToClassName('2024_06_15_120000_add_email_to_users')
        );
    }

    public function testRunReturnsPendingMigrations(): void
    {
        $file1 = '2024_01_01_000000_create_test_table';
        $file2 = '2024_01_02_000000_add_column_to_test_table';

        $this->writeMigrationFile($file1, 'CreateTestTable');
        $this->writeMigrationFile($file2, 'AddColumnToTestTable');

        $ran = $this->migrator->run();

        $this->assertCount(2, $ran);
        $this->assertContains($file1, $ran);
        $this->assertContains($file2, $ran);
    }

    public function testRunSkipsAlreadyRanMigrations(): void
    {
        $file = '2024_01_01_000000_create_test_table';
        $this->writeMigrationFile($file, 'CreateTestTable');

        $this->migrator->run();
        $second = $this->migrator->run();

        $this->assertEmpty($second, 'Already-ran migrations must be skipped');
    }

    public function testRollbackReversesLastBatch(): void
    {
        $file1 = '2024_01_01_000000_create_test_table';
        $file2 = '2024_01_02_000000_add_column_to_test_table';

        $this->writeMigrationFile($file1, 'CreateTestTable');
        $this->writeMigrationFile($file2, 'AddColumnToTestTable');

        $this->migrator->run(); // batch 1 — оба файла

        $rolled = $this->migrator->rollback();

        $this->assertCount(2, $rolled);
        $this->assertContains($file1, $rolled);
        $this->assertContains($file2, $rolled);
    }

    public function testRollbackOnlyLastBatch(): void
    {
        $file1 = '2024_01_01_000000_create_test_table';
        $this->writeMigrationFile($file1, 'CreateTestTable');
        $this->migrator->run(); // batch 1

        $file2 = '2024_01_02_000000_add_column_to_test_table';
        $this->writeMigrationFile($file2, 'AddColumnToTestTable');
        $this->migrator->run(); // batch 2

        $rolled = $this->migrator->rollback(); // только batch 2

        $this->assertCount(1, $rolled);
        $this->assertContains($file2, $rolled);

        // batch 1 остаётся в статусе "ran"
        $status = $this->migrator->status();
        $map    = array_column($status, null, 'migration');
        $this->assertTrue($map[$file1]['ran']);
        $this->assertFalse($map[$file2]['ran']);
    }

    public function testRollbackNothingWhenEmpty(): void
    {
        $rolled = $this->migrator->rollback();
        $this->assertEmpty($rolled);
    }

    public function testResetRollsBackAll(): void
    {
        $file1 = '2024_01_01_000000_create_test_table';
        $file2 = '2024_01_02_000000_add_column_to_test_table';

        $this->writeMigrationFile($file1, 'CreateTestTable');
        $this->migrator->run(); // batch 1

        $this->writeMigrationFile($file2, 'AddColumnToTestTable');
        $this->migrator->run(); // batch 2

        $rolled = $this->migrator->reset();

        $this->assertCount(2, $rolled);

        $status = $this->migrator->status();
        foreach ($status as $item) {
            $this->assertFalse($item['ran']);
        }
    }

    public function testStatusReturnsCorrectInfo(): void
    {
        $file1 = '2024_01_01_000000_create_test_table';
        $file2 = '2024_01_02_000000_add_column_to_test_table';

        $this->writeMigrationFile($file1, 'CreateTestTable');
        $this->writeMigrationFile($file2, 'AddColumnToTestTable');

        $this->migrator->run();

        $status = $this->migrator->status();
        $this->assertCount(2, $status);

        foreach ($status as $item) {
            $this->assertTrue($item['ran']);
            $this->assertEquals(1, $item['batch']);
        }
    }

    public function testStatusShowsPendingMigrations(): void
    {
        $file = '2024_01_01_000000_create_test_table';
        $this->writeMigrationFile($file, 'CreateTestTable');

        $status = $this->migrator->status();

        $this->assertCount(1, $status);
        $this->assertFalse($status[0]['ran']);
        $this->assertNull($status[0]['batch']);
    }

    public function testEmptyMigrationsDir(): void
    {
        $ran    = $this->migrator->run();
        $status = $this->migrator->status();

        $this->assertEmpty($ran);
        $this->assertEmpty($status);
    }
}