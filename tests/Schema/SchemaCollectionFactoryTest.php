<?php
namespace Shin1x1\LaravelTableAdmin\Test\Schema;

use Illuminate\Database\Connection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\Schema\Blueprint;
use PDO;
use Shin1x1\LaravelTableAdmin\Schema\SchemaCollectionFactory;

/**
 * Class SchemaCollectionFactoryTest
 * @package Shin1x1\LaravelTableAdmin\Test\Schema
 */
class SchemaCollectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SchemaCollectionFactory
     */
    protected $sut;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * setUp
     */
    public function setUp()
    {
        $pdo = new PDO('pgsql:dbname=app_test', 'vagrant', 'app_test');
        $this->connection = new PostgresConnection($pdo);

        $this->connection->getSchemaBuilder()->dropIfExists('riders');
        $this->connection->getSchemaBuilder()->dropIfExists('classes');

        $this->connection->getSchemaBuilder()->create('classes', function(Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name');
        });

        $this->connection->table('classes')->insert([
            ['id' => '1', 'name' => 'class1'],
            ['id' => '2', 'name' => 'class2'],
        ]);

        $this->connection->getSchemaBuilder()->create('riders', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('class_id')->index();
            $table->foreign('class_id')->references('id')->on('classes')->onUpdate('cascade');
            $table->string('name');
        });

        $this->sut = new SchemaCollectionFactory($this->connection);
    }

    public function tearDown()
    {
        $this->connection->getSchemaBuilder()->dropIfExists('riders');
        $this->connection->getSchemaBuilder()->dropIfExists('classes');
    }

    /**
     * @test
     */
    public function tableNothing(){
        $schemas = $this->sut->factory('nothing');

        $this->assertEquals(0, $schemas->count());
    }

    /**
     * @test
     */
    public function tableHasIdAndNameColumn(){
        $schemas = $this->sut->factory('classes');

        $this->assertEquals(2, $schemas->count());
        $this->assertInstanceOf('\Shin1x1\LaravelTableAdmin\Schema\SchemaText', $schemas->get(0));
        $this->assertEquals('id', $schemas->get(0)->getName());
        $this->assertInstanceOf('\Shin1x1\LaravelTableAdmin\Schema\SchemaText', $schemas->get(1));
        $this->assertEquals('name', $schemas->get(1)->getName());
    }

    /**
     * @test
     */
    public function tableHasForiegnKeyColumn(){
        $schemas = $this->sut->factory('riders');

        $this->assertEquals(3, $schemas->count());

        $column = $schemas->get(0);
        $this->assertInstanceOf('\Shin1x1\LaravelTableAdmin\Schema\SchemaLabel', $column);
        $this->assertEquals('id', $column->getName());

        $column = $schemas->get(1);
        $this->assertInstanceOf('\Shin1x1\LaravelTableAdmin\Schema\SchemaSelect', $column);
        $this->assertEquals('class_id', $column->getName());
        $expected = [
            '1' => 'class1',
            '2' => 'class2',
        ];
        $this->assertEquals($expected, $column->getSelectList());

        $column = $schemas->get(2);
        $this->assertInstanceOf('\Shin1x1\LaravelTableAdmin\Schema\SchemaText', $column);
        $this->assertEquals('name', $column->getName());
    }
}