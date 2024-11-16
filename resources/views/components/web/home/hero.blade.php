 <!-- Hero area S t a r t-->
 <section class="hero-padding-for-three video-overlay position-relative">
     <!-- Video -->

     <div class="hero-bg-video">
         <video class="hero-slider-video video-cover" 
         @if(!empty($component->media))
         poster="{{ asset('storage/' . $component->media ?? '') }}"
         @endif
             loop="" autoplay="" muted="">
             @if(!empty($component->media))
             <source src="{{ asset('storage/' . $component->media ?? '') }}" type="video/mp4">
                @endif
         </video>
     </div>
     <div class="container">
         <div class="row align-items-center justify-content-between g-4">
             <div class="col-xl-12">
                 <div class="hero-caption-three position-relative z-3">
                     <h4 class="title wow fadeInUp" data-wow-delay="0.0s"
                         style="visibility: visible; animation-delay: 0s; animation-name: fadeInUp;">
                         {{ $component->content['h4'][app()->getLocale()] ?? '' }}
                     </h4>
                     <p class="pera wow fadeInUp" data-wow-delay="0.1s"
                         style="visibility: visible; animation-delay: 0.1s; animation-name: fadeInUp;">
                         {{ $component->title[app()->getLocale()] ?? '' }}
                     </p>
                 </div>
                 <div class="hero-footer position-relative z-3 wow fadeInUp" data-wow-delay="0.3s"
                     style="visibility: visible; animation-delay: 0.3s; animation-name: fadeInUp;">
                     <div class="all-user">
                         @for ($i = 1; $i < 3; $i++)
                             <div class="happy-user">
                                 <img src="assets/images/hero/{{ $i }}.jpg" alt="{{ $i }}">
                             </div>
                         @endfor
                         <div class="happy-user-count">
                             <p class="user-count">5k+</p>
                         </div>
                         <p class="pera">{{ __('Happy Partners') }}👋</p>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </section>
 <!--/ End-of Hero-->
