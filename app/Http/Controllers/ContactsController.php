<?php

namespace App\Http\Controllers;

use App\Data\ContactData;
use App\Data\EditContactData;
use App\Data\OrganizationData;
use App\Data\SearchData;
use App\Data\StoreContactData;
use App\Models\Contact;
use Hybridly\Contracts\HybridResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchData $data): HybridResponse
    {
        /** @var \App\Models\User */
        $user = Auth::authenticate();

        return hybridly('contacts.index', [
            'filters' => $data,
            'contacts' => ContactData::collection(
                $user->account
                    ->contacts()
                    ->with('organization')
                    ->orderByName()
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
        /** @var \App\Models\User */
        $user = Auth::authenticate();

        return hybridly('contacts.create', [
            'organizations' => OrganizationData::collection(
                $user->account->organizations->toArray(),
            ),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactData $data): RedirectResponse
    {
        /** @var \App\Models\User */
        $user = Auth::authenticate();

        $user->account->contacts()->create($data->toArray());

        return to_route('contacts.index')->with(
            'success',
            __('contacts.create.successFlash'),
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact): HybridResponse
    {
        /** @var \App\Models\User */
        $user = Auth::authenticate();

        return hybridly('contacts.edit', [
            'contact' => EditContactData::from($contact),
            'organizations' => OrganizationData::collection(
                $user->account->organizations->toArray(),
            ),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        StoreContactData $data,
        Contact $contact,
    ): RedirectResponse {
        $contact->update($data->toArray());

        return to_route('contacts.index')->with(
            'success',
            __('contacts.edit.successFlash'),
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();

        return to_route('contacts.index')->with(
            'success',
            __('contacts.delete.successFlash'),
        );
    }

    /**
     * Restore the specified resource.
     */
    public function restore(Contact $contact): RedirectResponse
    {
        $contact->restore();

        return to_route('contacts.index')->with(
            'success',
            __('contacts.restore.successFlash'),
        );
    }
}
