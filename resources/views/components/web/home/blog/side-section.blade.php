<div class="row g-4">
    @foreach ($blogs as $blog)
        <div class="col-xl-4 col-lg-4 col-sm-6">
            <article class="news-card-two">
                <figure class="news-banner-two imgEffect">
                    <a href="news-details.html">
                        @php 
                        $img = !empty($blog->media[0]) ? $blog->media[0] : ''
                        @endphp
                        <img src="{{ asset('storage/' . $img) }}"
                            alt="{{ $blog->title[app()->getLocale()] ?? '' }}">
                    </a>
                </figure>
                <div class="news-content">
                    <div class="heading">
                        <span class="heading-pera">{{ $blog->title[app()->getLocale()] ?? '' }}</span>
                    </div>
                    <h4 class="title line-clamp-2">
                        <a href="news-details.html">The World is a Book and Those Who do not Travel Read
                            Only
                            One Page.</a>
                    </h4>
                    <div class="news-info">
                        <div class="d-flex gap-10 align-items-center">
                            <div class="all-user">
                                <div class="happy-user">
                                    <img src="assets/images/hero/user-1.jpeg" alt="image">
                                </div>
                                <div class="happy-user">
                                    <img src="assets/images/hero/user-2.png" alt="image">
                                </div>
                                <div class="happy-user">
                                    <img src="assets/images/hero/user-3.png" alt="image">
                                </div>
                                <div class="happy-user">
                                    <img src="assets/images/hero/user-4.jpeg" alt="image">
                                </div>
                            </div>
                        </div>
                        <p class="time">published {{ $blog->created_at }}</p>
                    </div>
                </div>
            </article>
        </div>
    @endforeach

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
