
 {{-- @author Youssef Tamri <yousseftam100@gamil.com> 
 we can use this data table in in all our models --}}

@props([
    'routeCreate',        // Route for the crete button
    'items',              // The collection of items (species  , pets or other models)
    'columns',            // An array of column names
    'actionRoutes',       // An array of action routes for edit and show button
    'actionDeleteRoutes', // An array of action routes for delete and restore button
    'showTrashed' => session('show_trashed', false), // State of the show trashed checkbox
])

<div class="row mt-100">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <a href="{{ $routeCreate }}" type="button" class="btn btn-success btn-sm">
                            <i class="ri ri-add-line"></i> <!-- Remix Icon for add -->
                        </a>
                    </div>
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="showTrashed" 
                               onclick="toggleTrashed()" {{ $showTrashed ? 'checked' : '' }}>
                        <label class="form-check-label" for="showTrashed">Show Trashed</label>
                    </div>                          
                </div>

                <div class="table-responsive"> 
                    <table class="table mt-3" style="border-collapse: collapse;"> 
                        <thead>
                            <tr>
                                @foreach ($columns as $column)
                                    <th scope="col" style="text-align: left; padding: 10px;">{{ ucfirst($column) }}</th> 
                                @endforeach
                                <th scope="col" align="right" style="padding: 10px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    @foreach ($columns as $column)
                                        <td style="padding: 10px;">{{ $item[$column] }}</td> 
                                    @endforeach
                                    <td align="right" class="d-flex justify-content-end" style="padding: 10px;">
                                        @if ($item->trashed())
                                            <form action="{{ route($actionDeleteRoutes['restore'], $item->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-warning me-2" onclick="return confirm('Are you sure you want to restore this item?')">
                                                    <i class="ri ri-restart-line"></i>
                                                </button>
                                            </form>
                                        @else
                                            @foreach ($actionRoutes as $action)
                                                <a href="{{ route($action['route'], $item->id) }}" type="button" class="{{ $action['class'] }} me-2">
                                                    <i class="{{ $action['icon'] }}"></i>
                                                </a>
                                            @endforeach
                                            <form action="{{ route($actionDeleteRoutes['destroy'], $item->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="ri ri-delete-bin-5-line"></i> 
                                                </button>
                                            </form>
                                        @endif
                                    </td>                                 
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $items->links('pagination::bootstrap-4') }} 
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function toggleTrashed() {
        const checkbox = document.getElementById('showTrashed');
        const showTrashed = checkbox.checked ? 1 : 0; 
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.trash.toggle') }}`;
        form.style.display = 'none';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        const trashedInput = document.createElement('input');
        trashedInput.type = 'hidden';
        trashedInput.name = 'trashed';
        trashedInput.value = showTrashed;
        form.appendChild(trashedInput);

        document.body.appendChild(form);
        form.submit();
    }
</script>



