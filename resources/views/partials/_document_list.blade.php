@if($documents->isEmpty())
    <div class="text-muted text-center py-3">
        <i class="bi bi-folder2 fs-3 d-block mb-1"></i>
        Nessun documento caricato
    </div>
@else
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead><tr>
                <th>Tipo</th><th>Nome</th><th>File</th><th>Dimensione</th><th>Caricato</th><th></th>
            </tr></thead>
            <tbody>
                @foreach($documents as $doc)
                <tr>
                    <td>
                        @php
                            $critical = in_array($doc->type, ['bill_of_lading','commercial_invoice','packing_list','certificate_of_origin']);
                        @endphp
                        <span class="badge {{ $critical ? 'bg-primary' : 'bg-secondary' }} badge-status">
                            {{ $doc->type_label }}
                        </span>
                    </td>
                    <td>{{ $doc->name }}</td>
                    <td class="text-muted small">{{ $doc->original_filename }}</td>
                    <td class="text-muted small">{{ $doc->file_size_formatted }}</td>
                    <td class="text-muted small">{{ $doc->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('documents.download', $doc) }}" class="btn btn-outline-secondary btn-action">
                                <i class="bi bi-download"></i>
                            </a>
                            <form method="POST" action="{{ route('documents.destroy', $doc) }}" onsubmit="return confirm('Eliminare il documento?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-action"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
