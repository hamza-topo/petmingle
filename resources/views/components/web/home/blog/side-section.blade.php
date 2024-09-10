<div class="row g-4 mb-60">
    <div class="col-xl-7 col-lg-7">
        <div class="tab-content" id="v-pills-tabContent-two">
            <div class="tab-pane fade show active" id="pills-news-one" role="tabpanel" aria-labelledby="pills-news-one">
                <div class="about-banner imgEffect4">
                    <img id="news-image" src="assets/images/news/news-banner.png" alt="news-image">
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-5 col-lg-5">
        <div class="all-contents" id="v-pills-tab-two" role="tablist" aria-orientation="vertical">
        @forelse ( $current_news as $new)
        <x-web.home.blog.side-item :new="$new"/>
        @empty
            
        @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const newsItems = document.querySelectorAll('.news-content');
        newsItems.forEach(item => {
            item.addEventListener('mouseover', function() {
                const imageUrl = this.getAttribute('data-image');
                document.getElementById('news-image').src = imageUrl;
                const targetTab = document.querySelector(this.getAttribute('data-bs-target'));
                const tabContent = document.getElementById('v-pills-tabContent-two');
                tabContent.querySelector('.active').classList.remove('show', 'active');
                targetTab.classList.add('show', 'active');
            });
        });
    });
</script>
@endpush
