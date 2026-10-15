<?php

return [
    App\Providers\AppServiceProvider::class,

    // Feature modules
    App\Modules\Auth\Providers\AuthServiceProvider::class,
    App\Modules\Catalogue\Providers\CatalogueServiceProvider::class,
    App\Modules\Labour\Providers\LabourServiceProvider::class,
    App\Modules\Search\Providers\SearchServiceProvider::class,
    App\Modules\Customer\Providers\CustomerServiceProvider::class,
];
