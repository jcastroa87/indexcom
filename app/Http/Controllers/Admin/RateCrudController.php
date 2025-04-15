<?php

namespace App\Http\Controllers\Admin;

use App\Models\Index;
use App\Models\Rate;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;

class RateCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(Rate::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/rate');
        CRUD::setEntityNameStrings('rate', 'rates');
    }

    protected function setupListOperation()
    {
        // Add a filter dropdown for indices
        CRUD::filter('index_id')
            ->type('select2')
            ->label('Index')
            ->values(function () {
                return Index::pluck('name', 'id')->toArray();
            })
            ->whenActive(function ($value) {
                CRUD::addClause('where', 'index_id', $value);
            });

        CRUD::column('index_id')
            ->label('Index')
            ->type('relationship')
            ->attribute('name');
        CRUD::column('date')->type('date');
        CRUD::column('value');
        CRUD::column('is_manual')->type('boolean');
        CRUD::column('created_at');
        CRUD::column('updated_at');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'index_id' => 'required|exists:indices,id',
            'date' => 'required|date',
            'value' => 'required|numeric',
        ]);

        CRUD::field('index_id')
            ->label('Index')
            ->type('relationship')
            ->attribute('name');
        CRUD::field('date')
            ->type('date')
            ->default(Carbon::today()->format('Y-m-d'));
        CRUD::field('value')->type('number')->attributes(['step' => '0.000001']);
        CRUD::field('is_manual')
            ->type('boolean')
            ->default(true)
            ->label('Manual Entry')
            ->hint('This is automatically set to true for admin entries');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
