<?php

namespace App\Http\Controllers\Admin;

use App\Models\Index;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

class IndexCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(Index::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/index');
        CRUD::setEntityNameStrings('index', 'indices');
    }

    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('slug');
        CRUD::column('description');
        CRUD::column('is_active')->type('boolean');
        CRUD::column('last_fetch_at');
        CRUD::column('fetch_frequency')->label('Fetch Frequency (minutes)');
        CRUD::column('created_at');
        CRUD::column('updated_at');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => 'required|min:2',
            'slug' => 'required|unique:indices,slug',
            'fetch_frequency' => 'required|integer|min:5',
        ]);

        CRUD::field('name');
        CRUD::field('slug');
        CRUD::field('description')->type('textarea');
        CRUD::field('source_api_url')->type('url');
        CRUD::field('source_api_key');
        CRUD::field('source_api_path');
        CRUD::field('is_active')->type('boolean');
        CRUD::field('fetch_frequency')
            ->label('Fetch Frequency (minutes)')
            ->type('number')
            ->default(60);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        CRUD::setValidation([
            'name' => 'required|min:2',
            'slug' => 'required|unique:indices,slug,' . CRUD::getCurrentEntryId(),
            'fetch_frequency' => 'required|integer|min:5',
        ]);
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();

        // Add a custom button to view latest rates
        CRUD::button('view_rates')
            ->stack('line')
            ->stack('top')
            ->content('<i class="la la-chart-line"></i> View Rates')
            ->class('btn btn-sm btn-link')
            ->url(function ($crud, $button, $entry) {
                return backpack_url('rate?index_id=' . $entry->id);
            });
    }
}
