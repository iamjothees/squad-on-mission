<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Services\ClientService;

class ClientController extends Controller
{
    protected ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index()
    {
        $clients = $this->clientService->getAllClients();
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $this->clientService->createClient($validated);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        if ($client->id === 1) {
            return redirect()->route('clients.index')->with('error', 'The primary SELF client cannot be modified.');
        }
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        if ($client->id === 1) {
            return redirect()->route('clients.index')->with('error', 'The primary SELF client cannot be modified.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $this->clientService->updateClient($client, $validated);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        try {
            $this->clientService->deleteClient($client);
            return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('clients.index')->with('error', $e->getMessage());
        }
    }
}
