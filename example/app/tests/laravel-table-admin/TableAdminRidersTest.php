<?php

class TableAdminRidersTest extends TestCase
{
    /**
     * @setUp
     */
    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
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

    /**
     * @test
     */
    public function update_db_error()
    {
        $data = [
            'class_id' => '100',
            'nationality_id' => '1',
            'name' => 'name',
            'no' => '1',
        ];
        $this->client->request('PUT', '/crud/riders/1', $data);
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $expected = ['type' => 'danger', 'text' => 'error'];
        $this->assertEquals($expected, Session::get('message'));

        $this->assertEquals('http://localhost/crud/riders/1', $this->client->getResponse()->headers->get('location'));

    }
}