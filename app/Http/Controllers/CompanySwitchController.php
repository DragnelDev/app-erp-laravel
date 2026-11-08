<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetCurrentCompany;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanySwitchController extends Controller
{
    public function __invoke(Request $request, Company $company): RedirectResponse
    {
        abort_unless(
            $request->user()->companies()->whereKey($company->id)->where('companies.is_active', true)->exists(),
            403
        );

        $request->session()->put(SetCurrentCompany::SESSION_KEY, $company->id);

        return redirect()->back();
    }
}
