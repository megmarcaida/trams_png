<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Imports\HeadingRowExtractor;
use Maatwebsite\Excel\Reader;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Reader::listen(BeforeImport::class, function (BeforeImport $event) {

            $concernable = $event->getConcernable();

            if ($concernable->verifyHeader) {

                //Extracting and "truncating" header from Excel's active worksheet (assuming import is only single worksheet)
                $header = array_filter(HeadingRowExtractor::extract($event->getDelegate()->getDelegate()->getActiveSheet(), $concernable));

               //Checking if headers match and throwing ValidationException otherwise
                throw_unless($header === $concernable->header, ValidationException::withMessages(['message' => trans('validation.unknown_file_template')]));
            }

            //This is where GasStationImport's $truncate and $model come into play. You can put your logic here. 
            if($concernable->truncate) {
                $concernable->model::truncate();
            }
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
