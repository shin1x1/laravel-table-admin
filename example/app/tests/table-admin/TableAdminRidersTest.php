<?php

class TableAdminRidersTest extends TestCase
{
    /**
     * @setUp
     */
    public function setUp()
    {
        parent::setUp();
        Artisan::call('db:seed');
    }

    /**
     * @test
     */
    public function index()
    {
        $response = $this->client->request('GET', '/crud/riders');
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals('riders', $response->filter('h2')->text());

        /** @var \Illuminate\View\View  $view */
        $view = $this->client->getResponse()->getOriginalContent();

        /** @var \Illuminate\Support\Collection $columns */
        $columns = $view->columns;
        $this->assertEquals(6, $columns->count());
        $this->assertEquals('class_id', $columns->get(1)->getName());
        $this->assertEquals(false, $columns->get(1)->isLabel());
        $this->assertEquals(true, $columns->get(1)->isSelect());
        $this->assertEquals(DB::table('classes')->orderBy('id')->lists('name', 'id'), $columns->get(1)->getSelectList());
    }
}