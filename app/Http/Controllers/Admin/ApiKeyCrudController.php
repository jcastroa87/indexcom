<?php

namespace App\Http\Controllers\Admin;

use App\Models\ApiKey;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Class ApiKeyCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ApiKeyCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(ApiKey::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/api-key');
        CRUD::setEntityNameStrings('API key', 'API keys');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::column('user_id')
            ->type('relationship')
            ->entity('user')
            ->attribute('name')
            ->label('User');
        CRUD::column('name');
        CRUD::column('key')->limit(15);
        CRUD::column('is_active')->type('boolean');
        CRUD::column('expires_at')->type('datetime');
        CRUD::column('created_at')->type('datetime');

        // Filters
        CRUD::filter('is_active')
            ->type('simple')
            ->label('Active Keys Only')
            ->whenActive(function() {
                CRUD::addClause('where', 'is_active', 1);
            });
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::field('user_id')
            ->type('relationship')
            ->entity('user')
            ->attribute('name')
            ->inline_create(['entity' => 'user']);

        CRUD::field('name')
            ->type('text')
            ->label('Key Name')
            ->hint('A descriptive name for this API key (e.g., "Production App", "Testing")');

        CRUD::field('key')
            ->type('text')
            ->attributes(['readonly' => 'readonly'])
            ->value(Str::random(32))
            ->hint('This key will be used for API authentication');

        CRUD::field('is_active')
            ->type('checkbox')
            ->label('Active')
            ->default(true);

        CRUD::field('expires_at')
            ->type('datetime')
            ->label('Expiration Date')
            ->hint('Leave empty for no expiration')
            ->allows_null(true);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        // Prevent key modification on update
        CRUD::field('key')->attributes(['disabled' => 'disabled']);
    }

    /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $request = $this->crud->getRequest();

        // If key is empty or null, generate a new one
        if (!$request->has('key') || empty($request->key)) {
            $request->request->set('key', Str::random(32));
        }

        $this->crud->setRequest($request);

        return $this->traitStore();
    }
}
