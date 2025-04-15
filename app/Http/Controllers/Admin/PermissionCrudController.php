<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PermissionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PermissionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
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
        CRUD::setModel(Permission::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/permission');
        CRUD::setEntityNameStrings('permission', 'permissions');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::column('name');
        CRUD::column('guard_name')->default('web');
        CRUD::column('created_at')->type('datetime');
        CRUD::column('updated_at')->type('datetime');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::field('name');
        CRUD::field('guard_name')->default('web');

        // Optionally, add a field for roles if you have that relationship
        if (method_exists(Permission::class, 'roles')) {
            CRUD::field('roles')
                ->type('relationship')
                ->entity('roles')
                ->attribute('name')
                ->multiple(true);
        }
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
