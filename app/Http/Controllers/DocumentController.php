<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\Contract;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'documentable_type' => 'required|in:container,contract',
            'documentable_id'   => 'required|integer',
            'type'              => 'required|in:bill_of_lading,commercial_invoice,packing_list,certificate_of_origin,other',
            'name'              => 'required|string|max:255',
            'file'              => 'required|file|max:20480',
            'notes'             => 'nullable|string',
        ]);

        $model = $request->documentable_type === 'container'
            ? Container::findOrFail($request->documentable_id)
            : Contract::findOrFail($request->documentable_id);

        $file = $request->file('file');
        $path = $file->store('documents', 'local');

        $model->documents()->create([
            'type'              => $request->type,
            'name'              => $request->name,
            'file_path'         => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_size'         => $file->getSize(),
            'notes'             => $request->notes,
        ]);

        return back()->with('success', 'Documento caricato con successo.');
    }

    public function download(Document $document)
    {
        return Storage::disk('local')->download($document->file_path, $document->original_filename);
    }

    public function destroy(Document $document)
    {
        Storage::disk('local')->delete($document->file_path);
        $document->delete();
        return back()->with('success', 'Documento eliminato.');
    }
}
