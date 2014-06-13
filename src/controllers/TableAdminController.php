<?php
namespace Shin1x1\LaravelTableAdmin\Controller;

use Exception;
use Controller;
use Illuminate\Support\Collection;
use Config;
use DB;
use HTML;
use Input;
use Redirect;
use Request;
use Route;
use Session;
use Shin1x1\LaravelTableAdmin\Column\ColumnCollection;
use Shin1x1\LaravelTableAdmin\Column\ColumnCollectionFactory;
use Shin1x1\LaravelTableAdmin\Column\ColumnInterface;
use Validator;
use View;
use Shin1x1\LaravelTableAdmin\TableAdmin;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TableAdminController extends Controller
{
    /**
     * @var ColumnCollection|ColumnInterface[]
     */
    protected $columns;

    /**
     * @var TableAdmin
     */
    protected $service;

    /**
     *
     */
    public function __construct()
    {
        Html::macro('transform', function($key) {
            $translator = app('translator');

            // app/lang/{locale}/validation.php
            $fullKey = 'validation.attributes.' . $key;
            if ($translator->has($fullKey)) {
                return $translator->get($fullKey);
            }
            // app/lang/{locale}/laravel-table-admin.php
            $fullKey = TableAdmin::PACKAGE_NAME . '.' .$key;
            if ($translator->has($fullKey)) {
                return $translator->get($fullKey);
            }
            // /path/to/package/lang/{locale}/laravel-table-admin.php
            $fullKey = TableAdmin::PACKAGE_NAME . '::lang.' . $key;
            if ($translator->has($fullKey)) {
                return $translator->get($fullKey);
            }

            return $key;
        });

        View::share('message', Session::get('message'));
    }

    /**
     * @param string $table
     * @return \Illuminate\View\View
     */
    public function index($table)
    {
        $this->buildInstances($table);

        return View::make($this->getView('index'))
            ->with('columns', $this->columns)
            ->with('paginator', $this->service->index())
            ->with('newUrl', $this->getUrl('create', $table))
            ->with('editUrl', $this->getUrl('edit', $table))
            ->with('deleteUrl', $this->getUrl('destroy', $table))
            ->with('table', $table)
            ;
    }

    /**
     * @param string $table
     * @return \Illuminate\View\View
     */
    public function create($table)
    {
        return $this->form($table);
    }

    /**
     * @param string $table
     * @param integer $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($table, $id)
    {
        return $this->form($table, $id);
    }

    /**
     * @param string $table
     * @param integer $id
     * @return \Illuminate\View\View
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function form($table, $id = null)
    {
        $this->buildInstances($table);

        if ($id) {
            $data = $this->service->read($id);
            if (empty($data)) {
                throw new NotFoundHttpException();
            }
        } else {
            $data = null;
        }

        return View::make($this->getView('form'))
            ->with('columns', $this->columns)
            ->with('backUrl', $this->getUrl('index', $table, $id))
            ->with('storeUrl', $this->getUrl('store', $table, $id))
            ->with('updateUrl', $this->getUrl('update', $table, $id))
            ->with('data', $data)
            ->with('table', $table);
    }

    /**
     * @param string $table
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($table)
    {
        return $this->register($table);
    }

    /**
     * @param string $table
     * @param integer $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($table, $id)
    {
        return $this->register($table, $id);
    }

    /**
     * @param string $table
     * @param integer $id
     * */
    protected function register($table, $id = null)
    {
        $this->buildInstances($table);

        $inputs = $this->service->getRegisterValues(Input::all());

        $rules = $this->columns->getValidateRules();
        $validator = Validator::make($inputs, $rules->toArray());
        if ($validator->fails()) {
            return Redirect::to($this->getUrl('form', $table, $id))->withErrors($validator->errors())->withInput();
        }

        try {
            $this->service->register($inputs, $id);

            if ($id) {
                $text = 'updated';
            } else {
                $text = 'created';
            }

            $message = ['type' => 'success', 'text' => $text];

            return Redirect::to($this->getUrl('index', $table))->with('message', $message);

        } catch (Exception $e) {
            $message = ['type' => 'danger', 'text' => 'error'];
            return Redirect::to(Request::getUri())->with('message', $message)->withInput();
        }
    }

    /**
     * @param string $table
     * @param integer $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($table, $id)
    {
        $this->buildInstances($table);

        try {
            $this->service->delete($id);
            $message = ['type' => 'success', 'text' => 'deleted'];
        } catch (Exception $e) {
            $message = ['type' => 'danger', 'text' => 'delete_error'];
        }

        return Redirect::to($this->getUrl('index', $table))->with('message', $message);
    }

    /**
     * @param string $table
     * @return TableAdmin
     */
    protected function buildInstances($table)
    {
        $connection = DB::connection();
        $this->columns = (new ColumnCollectionFactory($connection, $table))->factory($table);
        $this->service = new TableAdmin($connection, $this->columns, $table);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getView($name)
    {
        $view = Config::get(TableAdmin::PACKAGE_NAME . '::view.prefix') . $name;

        return $view;
    }

    /**
     * @param string $action
     * @param string $table
     * @param integer $id
     * @return string
     */
    protected function getUrl($action, $table, $id = null)
    {
        $base = Route::current()->getPrefix() . '/' . $table;

        if ($action == 'form') {
            $action = $id ? 'update' : 'create';
        }

        switch ($action) {
            case 'index': return $base;
            case 'create': return $base . '/create';
            case 'store': return $base;
            case 'edit':
            case 'update':
            case 'destroy':
                return $base . '/' . $id;
            default:
                return $base;
        }
    }
}
