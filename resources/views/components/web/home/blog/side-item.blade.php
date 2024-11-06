<div class="row g-4 mb-60">
    <div class="col-xl-7 col-lg-7">
        <div class="tab-content" id="v-pills-tabContent-two">
            <div class="tab-pane  fade show active" id="pills-news-one" role="tabpanel" aria-labelledby="pills-news-one">
                <div class="about-banner imgEffect4">
                    <img src="assets/images/news/news-banner.png" alt="travello">
                </div>
            </div>
            <div class="tab-pane fade" id="pills-news-two" role="tabpanel" aria-labelledby="pills-news-two">
                <div class="about-banner imgEffect4">
                    <img src="assets/images/news/banner-1.png" alt="travello">
                </div>
            </div>
            <div class="tab-pane fade" id="pills-news-three" role="tabpanel" aria-labelledby="pills-news-three">
                <div class="about-banner imgEffect4">
                    <img src="assets/images/news/banner-2.png" alt="travello">
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-5 col-lg-5">
        <div class="all-contents" id="v-pills-tab-two" role="tablist" aria-orientation="vertical">
            @foreach ($blogs->skip(1) as $blog)
                <div class="news-content active" id="pills-news-one-tab" data-bs-toggle="pill"
                    data-bs-target="#pills-news-one" role="tab" aria-controls="pills-news-one" aria-selected="true">
                    <h4 class="title">
                        <a href="javascript:void(0)">{{ $blog->title[app()->getLocale()] }}</a>
                    </h4>
                    <div class="news-info">
                        <div class="d-flex gap-10 align-items-center">
                            <div class="author-img">
                                @php
                                    $img = !empty($blog->media[0]) ? $blog->media[0] : '';
                                @endphp
                                <img src="{{ asset('storage/' . $img) }}" alt="{{config('app.name')}}">
                            </div>
                            <p class="name">{{ $blog->author->name ?? config('app.name') }}</p>
                        </div>
                        <div class="heading">
                            <span class="heading-pera">#cats</span>
                            <span class="heading-pera">#dogs</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
