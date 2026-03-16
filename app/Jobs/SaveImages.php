<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\InvoiceImage;

class SaveImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $imagePaths;
    protected $invoiceId;

    public function __construct(array $imagePaths, $invoiceId)
    {
        $this->imagePaths = $imagePaths;
        $this->invoiceId = $invoiceId;
    }
    public function saveImage($image,$folder){
        $file_extention=$image->getClientOriginalExtension();
        $file_name=time(). uniqid() .'.'.$file_extention;
        $path = asset($folder.'/'. $file_name);
        $image->move($folder, $file_name);
        return $path;

    }
    public function handle()
    {
        foreach ($this->imagePaths as $path) {
            $fullImagePath = public_path('invoices/' . basename($path));

            CompressInvoiceImage::dispatch($fullImagePath);

            InvoiceImage::create([
                'invoice_id' => $this->invoiceId,
                'image' => $path,
            ]);
        }
    }
}

