<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Shipment;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'documentable_type' => 'required|in:shipment,contract',
            'documentable_id'   => 'required|integer',
            'document_type'     => 'required|in:bill_of_lading,commercial_invoice,packing_list,certificate_of_origin,phytosanitary_certificate,insurance_certificate,quality_certificate,other',
            'name'              => 'required|string|max:255',
            'file'              => 'nullable|file|max:20480',
            'status'            => 'required|in:missing,received,under_review,approved,rejected',
            'notes'             => 'nullable|string',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('documents', 'public');
        }

        $documentableType = $validated['documentable_type'] === 'shipment'
            ? Shipment::class
            : Contract::class;

        Document::create([
            'documentable_type' => $documentableType,
            'documentable_id'   => $validated['documentable_id'],
            'document_type'     => $validated['document_type'],
            'name'              => $validated['name'],
            'file_path'         => $filePath,
            'status'            => $validated['status'],
            'notes'             => $validated['notes'] ?? null,
            'uploaded_by'       => auth()->id(),
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function download(Document $document)
    {
        if (!$document->file_path) {
            return back()->with('error', 'No file attached to this document.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File not found on server.');
        }

        return Storage::disk('public')->download($document->file_path, $document->name);
    }

    public function destroy(Document $document)
    {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Document deleted.');
    }
}
