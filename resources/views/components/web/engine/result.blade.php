<div class="all-tour-list">
    @if (!empty($pets))
        <div class="row g-4">
            @foreach ($pets as $pet)
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <x-web.engine.item :pet="$pet" />
                </div>
            @endforeach
        </div>
    @else
        <div class="row">
            <div class="col-12 text-center">
                <div class="section-button d-inline-block">
                    <a href="javascript:void(0)">
                        <div class="btn-primary-icon-sm">
                            <i class="ri-loader-2-line"></i>
                            <p class="pera">Loading</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
