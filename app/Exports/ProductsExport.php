<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::with('user')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Title (EN)',
            'Title (AR)',
            'Description (EN)',
            'Description (AR)',
            'Price',
            'Slug',
            'Assigned User',
            'User Email',
            'Created At',
        ];
    }

    /**
     * @param Product $product
     * @return array
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->title_en,
            $product->title_ar,
            $product->description_en,
            $product->description_ar,
            $product->price,
            $product->slug,
            $product->user ? $product->user->name : 'Not Assigned',
            $product->user ? $product->user->email : '',
            $product->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
