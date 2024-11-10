<div class="row p-2 g-4">
    @foreach ($blogs as $blog)
        <div class="col-xl-4 col-lg-4 col-sm-6">
            <article class="news-card-two">
                <figure class="news-banner-two imgEffect">
                    <a
                        href="{{ route('blogs.read', $blog->slug[app()->getLocale()] ?? slugify($blog->title[app()->getLocale()] ?? '')) }}">
                        @php
                            $img = !empty($blog->media[0]) ? $blog->media[0] : '';
                        @endphp
                        <img style="height:120px" src="{{ asset('storage/' . $img) }}"
                            alt="{{ $blog->title[app()->getLocale()] ?? '' }}">
                    </a>
                </figure>
                <div class="news-content">
                    <div class="heading">
                        <span class="heading-pera">{{ $blog->title[app()->getLocale()] ?? '' }}</span>
                    </div>
                    <h4 class="title line-clamp-2">
                        <a
                            href="{{ route('blogs.read', $blog->slug[app()->getLocale()] ?? slugify($blog->title[app()->getLocale()] ?? '')) }}">{!! generateTextPreview($blog->content[app()->getLocale()] ?? '') !!}</a>
                    </h4>
                    <div class="news-info">
                        <a
                            href="{{ route('blogs.read', $blog->slug[app()->getLocale()] ?? slugify($blog->title[app()->getLocale()] ?? '')) }}">
                            <div class="d-flex gap-10 align-items-center">
                                <div class="all-user">
                                    @php
                                        $avatar = json_decode($blog->author->avatar ?? '[]', true);
                                        $avatar = $avatar[0] ?? '';
                                    @endphp
                                    <div class="happy-user">
                                        <img src="{{ asset('storage/' . $avatar) }}"
                                            alt="{{ __('Author') . $blog->author->name ?? '' }}">
                                    </div>
                                </div>
                                <p class="time" style="margin:4px">{{ $blog->author->name ?? '' }}</p>

                            </div>
                        </a>
                    </div>
                </div>
            </article>
        </div>
    @endforeach

    {{-- <div class="col-12 text-center">
        <div class="section-button d-inline-block">
            <a href="javascript:void(0)">
                <div class="btn-primary-icon-sm">
                    <i class="ri-loader-2-line"></i>
                    <p class="pera">Loading</p>
                </div>
            </a>
        </div>
    </div> --}}
</div>
