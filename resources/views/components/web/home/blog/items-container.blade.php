<div class="row g-4">
    @forelse ($news as $new)
    <x-web.home.blog.item :new="$new"/>
    @empty
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
    @endforelse
</div>