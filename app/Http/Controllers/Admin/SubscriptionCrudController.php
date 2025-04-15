<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SubscriptionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SubscriptionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SubscriptionCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Subscription::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/subscription');
        CRUD::setEntityNameStrings('subscription', 'subscriptions');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // Define the columns for the list view
        CRUD::column('id');
        CRUD::column('user_id')->type('relationship')->entity('user')->attribute('name')->label('User');
        CRUD::column('plan_id')->type('relationship')->entity('plan')->attribute('name')->label('Plan');
        CRUD::column('status')->type('text');
        CRUD::column('starts_at')->type('datetime');
        CRUD::column('ends_at')->type('datetime');
        CRUD::column('api_requests_today')->type('number')->label('API Requests Today');
        CRUD::column('created_at')->type('datetime');

        // Add filters
        CRUD::filter('status')
            ->type('dropdown')
            ->label('Status')
            ->values([
                'active' => 'Active',
                'canceled' => 'Canceled',
                'expired' => 'Expired',
            ]);

        // CRUD::filter('plan_id')
        //     ->type('select2')
        //     ->label('Plan')
        //     ->entity('plan')
        //     ->attribute('name')
        //     ->multiple();
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(SubscriptionRequest::class);

        // Define form fields
        CRUD::field('user_id')
            ->type('relationship')
            ->entity('user')
            ->attribute('name')
            ->inline_create(['entity' => 'user']);

        CRUD::field('plan_id')
            ->type('relationship')
            ->entity('plan')
            ->attribute('name')
            ->inline_create(['entity' => 'plan']);

        CRUD::field('status')
            ->type('select_from_array')
            ->options([
                'active' => 'Active',
                'canceled' => 'Canceled',
                'expired' => 'Expired',
            ])
            ->default('active');

        CRUD::field('starts_at')
            ->type('datetime')
            ->default(now());

        CRUD::field('ends_at')
            ->type('datetime')
            ->allows_null(true);

        CRUD::field('metadata')
            ->type('textarea')
            ->attributes(['placeholder' => '{"key": "value"}'])
            ->label('Additional Metadata (JSON)');

        CRUD::field('api_requests_today')
            ->type('number')
            ->default(0)
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('api_requests_reset_date')
            ->type('date')
            ->default(now()->addDay()->startOfDay())
            ->wrapper(['class' => 'form-group col-md-6']);
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
