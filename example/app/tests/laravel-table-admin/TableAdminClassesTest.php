<?php

class TableAdminClassesTest extends TestCase
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
        $response = $this->client->request('GET', '/crud/classes');
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals('classes', $response->filter('h2')->text());

        /** @var \Illuminate\View\View  $view */
        $view = $this->client->getResponse()->getOriginalContent();

        $this->assertEquals('laravel-table-admin::index', $view->getName());

        $this->assertEquals('/crud/classes/create', $view->newUrl);
        $this->assertEquals('/crud/classes/', $view->editUrl);
        $this->assertEquals('/crud/classes/', $view->deleteUrl);
        $this->assertEquals('classes', $view->table);

        /** @var \Illuminate\Support\Collection $items */
        $items = $view->paginator->getCollection();
        $this->assertEquals(3, $items->count());
        $this->assertEquals('3,2,1', $items->implode('id', ','));

        /** @var \Illuminate\Support\Collection $columns */
        $columns = $view->columns;
        $this->assertEquals(2, $columns->count());
        $this->assertEquals('id', $columns->get(0)->getName());
        $this->assertEquals(false, $columns->get(0)->isLabel());
        $this->assertEquals('name', $columns->get(1)->getName());
        $this->assertEquals(false, $columns->get(1)->isLabel());
    }

    /**
     * @test
     */
    public function create()
    {
        $response = $this->client->request('GET', '/crud/classes/create');
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals('classes', $response->filter('h2')->text());

        /** @var \Illuminate\View\View  $view */
        $view = $this->client->getResponse()->getOriginalContent();

        $this->assertEquals('laravel-table-admin::form', $view->getName());

        $this->assertEquals('/crud/classes', $view->backUrl);
        $this->assertEquals('/crud/classes', $view->storeUrl);
        $this->assertEquals('/crud/classes/', $view->updateUrl);
        $this->assertEquals('classes', $view->table);

        $this->assertNull($view->data);
    }

    /**
     * @test
     */
    public function store()
    {
        $data = [
            'id' => '4',
            'name' => 'new',
        ];
        $this->client->request('POST', '/crud/classes', $data);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals('http://localhost/crud/classes', $this->client->getResponse()->headers->get('location'));

        $this->assertEquals(4, DB::table('classes')->count());

        $data = DB::table('classes')->orderBy('id', 'DESC')->first();
        $this->assertEquals(['id' => '4', 'name' => 'new'], (array)$data);
    }

    /**
     * @test
     */
    public function store_validation_error()
    {
        $data = [
        ];
        $this->client->request('POST', '/crud/classes', $data);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals('http://localhost/crud/classes/create', $this->client->getResponse()->headers->get('location'));

        $this->assertEquals(3, DB::table('classes')->count());
    }

    /**
     * @test
     */
    public function edit()
    {
        $response = $this->client->request('GET', '/crud/classes/1');
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals('classes', $response->filter('h2')->text());

        /** @var \Illuminate\View\View  $view */
        $view = $this->client->getResponse()->getOriginalContent();

        $this->assertEquals('laravel-table-admin::form', $view->getName());

        $this->assertEquals('/crud/classes', $view->backUrl);
        $this->assertEquals('/crud/classes', $view->storeUrl);
        $this->assertEquals('/crud/classes/1', $view->updateUrl);
        $this->assertEquals('classes', $view->table);

        $this->assertEquals(['id' => 1, 'name' => 'MotoGP'], (array)$view->data);
    }

    /**
     * @test
     */
    public function update()
    {
        $data = [
            'id' => '1',
            'name' => 'update',
        ];
        $this->client->request('PUT', '/crud/classes/1', $data);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals('http://localhost/crud/classes', $this->client->getResponse()->headers->get('location'));

        $this->assertEquals(3, DB::table('classes')->count());

        $data = DB::table('classes')->where('id', 1)->first(['name']);
        $this->assertEquals(['name' => 'update'], (array)$data);
    }

    /**
     * @test
     */
    public function update_validation_error()
    {
        $data = [
        ];
        $this->client->request('PUT', '/crud/classes/1', $data);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals('http://localhost/crud/classes/1', $this->client->getResponse()->headers->get('location'));

        $this->assertEquals(3, DB::table('classes')->count());
    }

    /**
     * @test
     */
    public function update_input_id()
    {
        $data = [
            'id' => '4',
            'name' => 'update',
        ];
        $this->client->request('PUT', '/crud/classes/1', $data);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals('http://localhost/crud/classes', $this->client->getResponse()->headers->get('location'));

        $this->assertEquals(3, DB::table('classes')->count());

        $data = DB::table('classes')->where('id', 4)->first(['name']);
        $this->assertEquals(['name' => 'update'], (array)$data);
    }

    /**
     * @test
     */
    public function delete()
    {
        $data = [
            'id' => '4',
            'name' => 'new',
        ];
        $this->client->request('POST', '/crud/classes', $data);
        $this->assertTrue(DB::table('classes')->where('id', 4)->exists());

        $this->client->request('DELETE', '/crud/classes/4');
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals('http://localhost/crud/classes', $this->client->getResponse()->headers->get('location'));

        $this->assertFalse(DB::table('classes')->where('id', 4)->exists());
    }

    /**
     * @test
     */
    public function delete_db_error()
    {
        $this->client->request('DELETE', '/crud/classes/1');
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals('http://localhost/crud/classes', $this->client->getResponse()->headers->get('location'));

        $expected = ['type' => 'danger', 'text' => 'delete_error'];
        $this->assertEquals($expected, Session::get('message'));

        $this->assertEquals(3, DB::table('classes')->count());
    }
}