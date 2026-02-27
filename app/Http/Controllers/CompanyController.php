<?php

namespace App\Http\Controllers;

use App\Exports\CompaniesExport;
use App\Models\Company;
use App\Services\MapyCzGeocodingService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CompaniesImport;

class CompanyController extends Controller
{
    protected $geocodingService;

    public function __construct(MapyCzGeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::all();
        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'company_id' => 'required|unique:companies',
            'street' => 'required',
            'city' => 'required',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'color' => 'nullable|string',
        ]);

        $data = $request->all();

        // Auto-compute coordinates
        $address = $data['street'] . ', ' . $data['city'];
        $coordinates = $this->geocodingService->getCoordinates($address);

        if ($coordinates) {
            $data['latitude'] = $coordinates['latitude'];
            $data['longitude'] = $coordinates['longitude'];
        }

        Company::create($data);

        return redirect()->route('companies.index')
            ->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'title' => 'required',
            'company_id' => 'required|unique:companies,company_id,' . $company->id,
            'street' => 'required',
            'city' => 'required',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'color' => 'nullable|string',
        ]);

        $data = $request->all();

        // Check if address changed or coordinates are missing
        if ($company->street !== $data['street'] || $company->city !== $data['city'] || empty($company->latitude) || empty($company->longitude)) {
             $address = $data['street'] . ', ' . $data['city'];
             $coordinates = $this->geocodingService->getCoordinates($address);

             if ($coordinates) {
                 $data['latitude'] = $coordinates['latitude'];
                 $data['longitude'] = $coordinates['longitude'];
             }
        }

        $company->update($data);

        return redirect()->route('companies.index')
            ->with('success', 'Company updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully');
    }

    /**
     * Show the import form.
     */
    public function import()
    {
        return view('companies.import');
    }

    /**
     * Process the imported Excel file.
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new CompaniesImport($this->geocodingService);
            Excel::import($import, $request->file('file'));

            $results = $import->getResults();

            return redirect()->route('companies.index')
                ->with('success', "Import completed! {$results['success']} companies imported successfully." . 
                    ($results['skipped'] > 0 ? " {$results['skipped']} rows skipped due to errors." : ""));
        } catch (\Exception $e) {
            return redirect()->route('companies.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new CompaniesExport(), 'companies.xlsx');
    }
}
