<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PlanRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PlanCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PlanCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Plan::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/plan');
        CRUD::setEntityNameStrings('plan', 'plans');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // Define what columns to show on the list view
        CRUD::column('name')->label('Plan Name');
        CRUD::column('slug');
        CRUD::column('price')->type('number')->prefix('$')->decimals(2);
        CRUD::column('request_limit')->label('Daily API Limit');
        CRUD::column('has_extended_data')->type('boolean')->label('Extended Access');
        CRUD::column('is_active')->type('boolean');
        CRUD::column('is_featured')->type('boolean');

        // Filters
        CRUD::filter('is_active')
            ->type('simple')
            ->label('Active Plans Only')
            ->whenActive(function() {
                CRUD::addClause('where', 'is_active', 1);
            });

        CRUD::filter('has_extended_data')
            ->type('simple')
            ->label('With Extended Data Access')
            ->whenActive(function() {
                CRUD::addClause('where', 'has_extended_data', 1);
            });
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PlanRequest::class);

        // Define form fields
        CRUD::field('name')->label('Plan Name')->type('text');
        CRUD::field('slug')->type('text')->hint('URL-friendly version of the name. It will be auto-generated if left empty.');
        CRUD::field('description')->type('textarea');
        CRUD::field('price')->type('number')->attributes(['step' => '0.01'])->prefix('$');
        CRUD::field('request_limit')->type('number')->label('Daily API Request Limit')->default(100);
        CRUD::field('has_extended_data')->type('checkbox')->label('Has Access to Extended Data');

        CRUD::field('features')->type('repeatable')->fields([
            [
                'name' => 'feature',
                'type' => 'text',
                'label' => 'Feature',
                'wrapper' => ['class' => 'form-group col-md-12'],
            ],
        ])->min(0)->max(10)->label('Plan Features');

        CRUD::field('is_active')->type('checkbox')->default(true);
        CRUD::field('is_featured')->type('checkbox')->default(false)->label('Featured Plan');
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
