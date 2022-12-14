<?php

namespace App\Http\Controllers;

use App\Data\EditOrganizationData;
use App\Data\OrganizationData;
use App\Data\SearchData;
use App\Data\StoreOrganizationData;
use App\Models\Organization;
use Hybridly\Contracts\HybridResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class OrganizationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchData $data): HybridResponse
    {
        /** @var \App\Models\User */
        $user = Auth::authenticate();

        return hybridly('organizations.index', [
            'filters' => $data,
            'organizations' => OrganizationData::collection(
                $user->account
                    ->organizations()
                    ->orderBy('name')
                    ->filter($data)
                    ->paginate(10)
                    ->withQueryString(),
            ),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): HybridResponse
    {
        return hybridly('organizations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganizationData $data): RedirectResponse
    {
        /** @var \App\Models\User */
        $user = Auth::authenticate();

        $user->account->organizations()->create($data->toArray());

        return to_route('organizations.index')->with(
            'success',
            __('organizations.create.successFlash'),
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization): HybridResponse
    {
        return hybridly('organizations.edit', [
            'organization' => EditOrganizationData::from(
                $organization->load('contacts'),
            ),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        StoreOrganizationData $data,
        Organization $organization,
    ): RedirectResponse {
        $organization->update($data->toArray());

        return to_route('organizations.index')->with(
            'success',
            __('organizations.edit.successFlash'),
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        $organization->delete();

        return to_route('organizations.index')->with(
            'success',
            __('organizations.delete.successFlash'),
        );
    }

    /**
     * Restore the specified resource.
     */
    public function restore(Organization $organization): RedirectResponse
    {
        $organization->restore();

        return to_route('organizations.index')->with(
            'success',
            __('organizations.restore.successFlash'),
        );
    }
}
