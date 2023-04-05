<?php

namespace App\Exports;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    private $productsData;

    /**
     * Write code on Method
     *
     * @return response()
     */

    public function __construct($productsData)
    {
        $this->productsData = $productsData;
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {  
        return collect($this->productsData);
    }

     /**
     * Here you select the row that you want in the file
     *
     * @return response()
     */

    public function map($product): array
    {   
        $store_name = isset($product->Productstore->name) ? $product->Productstore->name : "";
        $product_image = isset($product->image->image_path) ? Storage::disk(config('app_settings.filesystem_disk.value'))->url($product->image->image_path) : "";
        $status = ($product->status == 1) ? 'Available' : 'Not Available';
        $addonData = '';
        // if($product->productAddons) {
        //     $addonName = [];
        //     foreach($product->productAddons as $padd) {
        //         $addonName[] = $padd->productAddonOption->name. ' = '. $padd->productAddonOption->addon_type;
        //     }
        //     $addonData = ($addonName) ? implode(", ", $addonName) : '';
        // }

        if($product->productAddons) {
            $addonName = [];

            $addonName = $product->productAddons->map(function($arr) {									
                return $arr->productAddonOption->name. '='.$arr->productAddonOption->addon_type;
            });

            $addonData = ($addonName) ? implode(", ", $addonName->toArray()) : '';
        }

        return [
            $product_image,
            $product->name,
            $product->productCategories->name,
            $product->sku,
            $product->price,
            $product->price_sale,
            $product->sale_start_date,
            $product->sale_end_date,
            $store_name,
            $addonData,
            $status,
            $product->created_at,
        ];
    }

    /**
     * Here you select the header that you want in the file
     *
     * @return response()
     */

    public function headings(): array
    {
        return [
            'Product Image',
            'Product Name',
            'Category',
            'Sku',
            'Price',
            'Price Sale',
            'Sale Start Date',
            'Sale End Date',
            'Store',
            'Addon Options',
            'Status',
            'Created At',
        ];
    }
}
