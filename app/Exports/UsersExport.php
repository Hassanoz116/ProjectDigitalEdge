<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::with(['roles', 'products'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Role',
            'Verified',
            'Country',
            'City',
            'Products Count',
            'Created At',
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->phone ?? 'N/A',
            $user->roles->first() ? $user->roles->first()->name : 'N/A',
            $user->email_verified_at ? 'Yes' : 'No',
            $user->country ? $user->country->name : 'N/A',
            $user->city ? $user->city->name : 'N/A',
            $user->products->count(),
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
