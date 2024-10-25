<div class="package-card">
    <div class="package-img imgEffect4">
        <a href="tour-details.html">
            <img src="{{ fakeImages($pet->images) }}" alt="">
        </a>
        <div class="image-badge">
            <p class="pera">{{ $pet->race->name ?? '' }}</p>
        </div>
    </div>
    <div class="package-content">
        <h4 class="area-name">
            <a href="tour-details.html">{{ $pet->name ?? '' }}</a>
        </h4>
        <span class="area-name">
            <a href="tour-details.html">{{ $pet->owner->name ?? '' }}</a>
        </span>
        <div class="location">
            <i class="ri-map-pin-line"></i>
            <div class="name">Bangkok, Thailand</div>
        </div>
        <div class="packages-person">
            <div class="count">
                <i class="ri-time-line name"></i>
                <p class="pera">{{ lastActivity($pet->owner?->sessions?->last()?->last_activity ?? 0) }}</p>
            </div>
            <div class="count">
                <i class="{{ sexIcon($pet->sexe) }}"></i>
                <p class="pera">{{ $pet->species->name ?? '' }}</p>
            </div>
        </div>
        <div class="price-review text-center">
            {{-- <div class="d-flex gap-10">
                <p class="light-pera">Age</p>
                <p class="pera">{{ $pet->age }} year(s)</p>
            </div> --}}
            <div class="rating">
                <button class="btn-secondary-sm"><i class="ri-message-3-line"></i></button>
            </div>
        </div>
    </div>
</div>
