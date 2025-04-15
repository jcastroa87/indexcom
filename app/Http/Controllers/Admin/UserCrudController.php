<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('user', 'users');
    }

    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('email');
        CRUD::column('is_active')->type('boolean');
        CRUD::column('api_key');
        CRUD::column('created_at');
        CRUD::column('updated_at');

        // Add a custom button to generate API key
        // CRUD::button('generate_api_key')
        //     ->stack('line')
        //     ->content('<i class="la la-key"></i> Generate API Key')
        //     ->class('btn btn-sm btn-primary')
        //     ->url(function($crud, $button, $entry) {
        //         return backpack_url('user/' . $entry->id . '/generate-api-key');
        //     });
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => 'required|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        CRUD::field('name');
        CRUD::field('email')->type('email');
        CRUD::field('password')->type('password');
        CRUD::field('is_active')->type('boolean')->default(true);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        CRUD::setValidation([
            'name' => 'required|min:2',
            'email' => 'required|email|unique:users,email,' . CRUD::getCurrentEntryId(),
            'password' => 'nullable|min:8',
        ]);

        // Make password optional in update
        CRUD::field('password')->type('password')->hint('Leave empty to keep current password');
    }

    /**
     * Generate an API key for the user.
     */
    public function generateApiKey($id)
    {
        $user = User::findOrFail($id);
        $user->generateApiKey();

        \Alert::success('API key generated successfully.')->flash();

        return redirect()->back();
    }
}
