@extends('layouts.app')
@section('meta')
    <x-web.layout.meta :seo="$seo" />
@endsection

@section('title')
    {{ $seo->title[app()->getLocale()] ?? config('app.name', 'Petmingle') }}
@endsection
@section('main')
    <x-web.layout.bread-crumb />
    <!-- Any Question Area S t a r t -->
    <section class="question-area section-padding">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <!-- Section Tittle -->
                    <div class="section-tittle mb-50">
                        <h2 class="title font-700">Any Questions</h2>
                        <p class="pera">When deciding which charity to donate to, it's important to do your search
                            and find one
                            that aligns with your values and interests.</p>
                    </div>
                    <div class="accordion" id="accordionExample">
                        <!-- Single -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Have you
                                    weighed the potential risks and
                                    benefits?</button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">When deciding which charity to donate to, it's important
                                    to do your
                                    search.</div>
                            </div>
                        </div>
                        <!-- Single -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed additional-styles" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                                    aria-controls="collapseTwo">How will you gather
                                    feedback from stakeholders</button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">When deciding which charity to donate to, it's important
                                    to do your
                                    search.</div>
                            </div>
                        </div>
                        <!-- Single -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed additional-styles" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false"
                                    aria-controls="collapseThree">There any
                                    sustainability or ethical to take into account?</button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">When deciding which charity to donate to, it's important
                                    to do your
                                    search.</div>
                            </div>
                        </div>
                        <!-- Single -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed additional-styles" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false"
                                    aria-controls="collapseFour">There any
                                    sustainability or ethical to take into account?</button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">When deciding which charity to donate to, it's important
                                    to do your
                                    search.</div>
                            </div>
                        </div>
                        <!-- Single -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFive">
                                <button class="accordion-button collapsed additional-styles" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false"
                                    aria-controls="collapseFive">There any
                                    Lorem ipsum dolor Nibh pellentesque</button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">When deciding which charity to donate to, it's important
                                    to do your
                                    search.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5">
                    <img class="w-100 d-none d-lg-block tilt-effect radius-10" src="assets/images/gallery/faq.jpg"
                        alt="image">
                </div>
            </div>
        </div>
    </section>
    <!--/ End-of Question Area -->

    <!-- FAQs S t r t -->
    <div class="faqs-area bottom-padding">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <!-- Single -->
                    <div class="single-terms mb-30">
                        <h5 class="title font-600">dolor sit amet consectetur</h5>
                        <p class="pera mb-20">Lorem ipsum dolor sit amet consectetur. Nibh pellentesque vel sed
                            malesuada morbi
                            lobortis habitant vel. Nisi auctor id fusce nulla leo adipiscing a eu. Quam facilisis
                            senectus mi diam.
                            Elementum euismod aliquet at elit. Commodo facilisi arcu tincidunt cras elit dapibus
                            vestibulum. Ipsum
                            ornare eleifend</p>
                        <!-- Single Listing -->
                        <ul class="experience listing listing2">
                            <li class="single-list">
                                <i class="ri-shield-check-line"></i>
                                <p class="pera">amet consectetur. Nibh pellentesque</p>
                            </li>
                            <li class="single-list">
                                <i class="ri-shield-check-line"></i>
                                <p class="pera">dolor sit amet consectetur. Nibh pellentesque</p>
                            </li>
                            <li class="single-list">
                                <i class="ri-shield-check-line"></i>
                                <p class="pera">Nibh pellentesque</p>
                            </li>
                            <li class="single-list">
                                <i class="ri-shield-check-line"></i>
                                <p class="pera">Lorem ipsum dolor Nibh pellentesque</p>
                            </li>
                            <li class="single-list">
                                <i class="ri-shield-check-line"></i>
                                <p class="pera">ipsum dolor sit amet consectetur. Nibh pellentesque</p>
                            </li>
                        </ul>
                    </div>
                    <!-- Single -->
                    <div class="single-terms mb-30">
                        <h5 class="title font-600">Lorem ipsum dolor</h5>
                        <p class="pera mb-20">Lorem ipsum dolor sit amet consectetur. Nibh pellentesque vel sed
                            malesuada morbi
                            lobortis habitant vel. Nisi auctor id fusce nulla leo adipiscing a eu. Quam facilisis
                            senectus mi diam.
                            Elementum euismod aliquet at elit. Commodo facilisi arcu tincidunt cras elit dapibus
                            vestibulum. Ipsum
                            ornare eleifend at orci vel turpis. Tincidunt massa sagittis est scelerisque risus vel
                            urna. Fermentum
                            molestie turpis sed pellentesque enim risus pellentesque enim. Aliquam amet pharetra
                            massa</p>
                        <p class="pera mb-20">Arcu et justo quis aenean sed. Sollicitudin eget mus semper vitae nibh
                            eget tortor
                            commodo. Cursus vel scelerisque ut at. Lacus orci vel dolor eget velit aliquet. Sagittis
                            laoreet non sed
                            mattis tristique a ut. Volutpat consequat.</p>
                    </div>
                    <!-- Single -->
                    <div class="single-terms mb-0">
                        <h5 class="title font-600">Acknowledgement</h5>
                        <p class="pera mb-20">BY USING SERVICE OR OTHER SERVICES PROVIDED BY US, YOU ACKNOWLEDGE
                            THAT YOU HAVE
                            READ THESE TERMS OF SERVICE AND AGREE TO BE BOUND BY THEM.</p>
                    </div>
                    <!-- Single -->
                    <div class="single-terms mb-0">
                        <h5 class="title font-600">Contact Us</h5>
                        <p class="pera mb-20 text-normal">Email: <a href="#">{{ config('app.mail_contact_us') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ End-of FAQs-->
@endsection
