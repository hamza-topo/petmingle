<section class="tour-list-section section-padding2">
    <div class="container">

        <div class="row g-4">
            <div class="col-xl-3">
                <x-web.engine.filter />
                <div class="cover"></div>
            </div>
            <div class="col-xl-9">
                <x-web.engine.order />
                <x-web.engine.result :pets="$pets" />
            </div>
        </div>
    </div>
</section>
