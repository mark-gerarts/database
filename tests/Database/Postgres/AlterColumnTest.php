<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Database\Tests\Postgres;

use Spiral\Database\Exception\StatementException;

class AlterColumnTest extends \Spiral\Database\Tests\AlterColumnTest
{
    const DRIVER = 'postgres';

    public function testNativeEnums()
    {
        $driver = $this->getDriver();
        try {
            $driver->execute("CREATE TYPE mood AS ENUM ('sad', 'ok', 'happy');");
        } catch (StatementException $e) {
        }

        try {
            $driver->execute("CREATE TABLE person (
    name text,
    current_mood mood
);");
        } catch (StatementException $e) {
        }

        $schema = $driver->getSchema("person");
        $this->assertSame('enum', $schema->column('current_mood')->getAbstractType());
        $this->assertSame(['sad', 'ok', 'happy'], $schema->column('current_mood')->getEnumValues());

        // convert to internal type
        $schema->save();

        $schema = $driver->getSchema("person");
        $schema->column('current_mood')->enum(['angry', 'happy']);
        $schema->save();

        $this->assertSameAsInDB($schema);

        $driver->execute('DROP TABLE person');
        $driver->execute('DROP TYPE mood');
    }
}
