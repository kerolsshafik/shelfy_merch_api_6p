<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CompressInvoiceImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $imagePath;

    public function __construct($imagePath)
    {
        $this->imagePath = $imagePath;
    }

    public function handle()
    {
        // استدعاء الميثود من الكلاس اللي فيه
        app()->call('App\Http\Controllers\API\InvoiceController@reduceImageSizeNative', [
            'filePath' => $this->imagePath,
            'quality' => 40
        ]);
    }
}

