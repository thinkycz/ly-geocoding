<?php

namespace App\Exports;

use App\Models\Company;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CompaniesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return Company::query()->orderBy('id')->get();
    }

    public function headings(): array
    {
        return [
            'title',
            'company_id',
            'street',
            'city',
            'latitude',
            'longitude',
            'color',
        ];
    }

    public function map($row): array
    {
        $company = $row;

        return [
            $company->title,
            $company->company_id,
            $company->street,
            $company->city,
            $company->latitude,
            $company->longitude,
            $company->color ?? '#3FB1CE',
        ];
    }
}

