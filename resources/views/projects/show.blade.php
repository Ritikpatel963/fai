<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $project->meta_title ?: ($project->title . ' - Project Details | Future Agri Infra') }}</title>
  @if($project->meta_description)
    <meta name="description" content="{{ $project->meta_description }}">
  @endif
  @if($project->meta_keywords)
    <meta name="keywords" content="{{ $project->meta_keywords }}">
  @endif

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Core Theme Stylesheets -->
  <link rel="stylesheet" href="https://fai.codespine.in/style.css?v=3.3">
  <link rel="stylesheet" href="https://fai.codespine.in/resstyle.css?v=2.4">
</head>

<body class="protected-page">
  <div class="afm-scope" id="afmScope">

    <div class="afm-scrim" id="afmScrim"></div>

    {{-- Top Navigation --}}
    <nav class="afm-navbar afm-navbar--solid" aria-label="Primary">
      <a href="{{ url('/') }}" class="afm-brand">
        <span class="afm-brand__mark">
          <img src="https://fai.codespine.in/images/logo.png" alt="Future Agri Infra logo" style="height: 54px; width: auto; object-fit: contain;">
        </span>
      </a>

      <div class="afm-navbar__actions">
        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $project->contact_phone ?? '+917879183931') }}" class="afm-btn-primary fm-cta-btn">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
          </svg>
          <span>Contact Us</span>
        </a>
      </div>
    </nav>

    {{-- Hero Section --}}
    <section class="fm-section fmab" id="about-us">
      <div class="fmab__media">
        <img src="{{ $project->banner_image ?: 'https://fai.codespine.in/images/banner3.jpeg' }}"
             alt="{{ $project->title }}"
             class="fmab__img"
             loading="eager">
      </div>

      <div class="fmab__inner">
        <div class="fmab__content">
          <span class="fmab-eyebrow">{{ $project->subtitle ?: 'Protected Farming' }}<i class="fmab-eyebrow__line"></i></span>

          <h1 class="fm-heading fmab__title">
            {{ $project->title }} <br><span class="fmdm__accent">Case Study</span>
          </h1>

          @if($project->excerpt)
            <p class="fm-text fmab__text">
              {{ $project->excerpt }}
            </p>
          @endif

          <a href="#project-case-study" class="fm-cta-btn">
            View Project Details
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </a>
        </div>
      </div>
    </section>

    {{-- Main Project Details & Facts --}}
    <main>
      <section class="fmpd" id="project-case-study" data-fmpd>
        <div class="fmpd__inner">
          <div class="fmpd__layout">

            {{-- LEFT: Gallery & Narrative --}}
            <div class="fmpd__main">

              @php
                $gallery = $project->gallery_images ?? [];
                // If featured_image exists and is not already in gallery, prepend it
                if (!empty($project->featured_image) && !in_array($project->featured_image, $gallery)) {
                    array_unshift($gallery, $project->featured_image);
                }
                $mainImg = !empty($gallery[0]) ? $gallery[0] : ($project->featured_image ?: 'https://fai.codespine.in/images/proj-gerbera.jpg');
              @endphp

              {{-- Interactive Image Gallery --}}
              <div class="fmpd-gallery" data-fmpd-gallery>
                <div class="fmpd-gallery__main-wrap">
                  <img src="{{ $mainImg }}" alt="{{ $project->title }}" class="fmpd-gallery__main-img" data-fmpd-main-img width="1200" height="750" loading="eager">
                </div>

                @if(count($gallery) > 1)
                  <div class="fmpd-gallery__thumbs">
                    @foreach($gallery as $index => $img)
                      <button type="button" class="fmpd-gallery__thumb {{ $index === 0 ? 'is-active' : '' }}" data-fmpd-thumb="{{ $img }}" aria-label="View photo {{ $index + 1 }}">
                        <img src="{{ $img }}" alt="{{ $project->title }} photo {{ $index + 1 }}" loading="lazy" width="200" height="125">
                      </button>
                    @endforeach
                  </div>
                @endif
              </div>

              {{-- About This Project --}}
              <article class="fmpd-about">
                <h2 class="fm-heading fmpd-about__title">About This Project</h2>
                <div class="fmpd-about__text">
                  @if($project->content)
                    {!! $project->content !!}
                  @else
                    <p class="fm-text">{{ $project->excerpt }}</p>
                  @endif
                </div>
              </article>

            </div>

            {{-- RIGHT: Sidebar Cards --}}
            <aside class="fmpd__sidebar">

              {{-- Card 1: Project Facts --}}
              <div class="fmpd-facts-card">
                <div class="fmpd-facts-card__header">
                  <h3 class="fmpd-facts-card__title">Project Facts</h3>
                  <span class="fmpd-facts-card__badge">{{ $project->project_status ?: 'Completed' }}</span>
                </div>

                <ul class="fmpd-facts-list">
                  @if($project->location)
                  <li class="fmpd-fact">
                    <div class="fmpd-fact__icon" aria-hidden="true">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                        <circle cx="12" cy="10" r="3" />
                      </svg>
                    </div>
                    <div class="fmpd-fact__content">
                      <span class="fmpd-fact__label">Location</span>
                      <strong class="fmpd-fact__val">{{ $project->location }}</strong>
                    </div>
                  </li>
                  @endif

                  @if($project->client_name)
                  <li class="fmpd-fact">
                    <div class="fmpd-fact__icon" aria-hidden="true">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                      </svg>
                    </div>
                    <div class="fmpd-fact__content">
                      <span class="fmpd-fact__label">Farm / Owner</span>
                      <strong class="fmpd-fact__val">{{ $project->client_name }}</strong>
                    </div>
                  </li>
                  @endif

                  @if($project->crop)
                  <li class="fmpd-fact">
                    <div class="fmpd-fact__icon" aria-hidden="true">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2a10 10 0 0 1 10 10c0 5.523-4.477 10-10 10S2 17.523 2 12A10 10 0 0 1 12 2z" opacity="0.15" />
                        <path d="M12 22v-9" />
                        <path d="M12 13a5 5 0 0 0 5-5c0-3-5-6-5-6s-5 3-5 6a5 5 0 0 0 5 5z" />
                      </svg>
                    </div>
                    <div class="fmpd-fact__content">
                      <span class="fmpd-fact__label">Crop</span>
                      <strong class="fmpd-fact__val">{{ $project->crop }}</strong>
                    </div>
                  </li>
                  @endif

                  @if($project->project_type)
                  <li class="fmpd-fact">
                    <div class="fmpd-fact__icon" aria-hidden="true">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 2 7 12 12 22 7 12 2" />
                        <polyline points="2 17 12 22 22 17" />
                        <polyline points="2 12 12 17 22 12" />
                      </svg>
                    </div>
                    <div class="fmpd-fact__content">
                      <span class="fmpd-fact__label">Project Type</span>
                      <strong class="fmpd-fact__val">{{ $project->project_type }}</strong>
                    </div>
                  </li>
                  @endif

                  @if($project->area)
                  <li class="fmpd-fact">
                    <div class="fmpd-fact__icon" aria-hidden="true">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.3 8.7 8.7 21.3a1 1 0 0 1-1.4 0l-6-6a1 1 0 0 1 0-1.4L13.9 1.3a1 1 0 0 1 1.4 0l6 6a1 1 0 0 1 0 1.4z" />
                        <line x1="7.5" y1="10.5" x2="10.5" y2="7.5" />
                        <line x1="10.5" y1="13.5" x2="13.5" y2="10.5" />
                        <line x1="13.5" y1="16.5" x2="16.5" y2="13.5" />
                      </svg>
                    </div>
                    <div class="fmpd-fact__content">
                      <span class="fmpd-fact__label">Area</span>
                      <strong class="fmpd-fact__val">{{ $project->area }}</strong>
                    </div>
                  </li>
                  @endif

                  @if($project->completed_at)
                  <li class="fmpd-fact">
                    <div class="fmpd-fact__icon" aria-hidden="true">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                      </svg>
                    </div>
                    <div class="fmpd-fact__content">
                      <span class="fmpd-fact__label">Completed On</span>
                      <strong class="fmpd-fact__val">{{ $project->completed_at->format('F d, Y') }}</strong>
                    </div>
                  </li>
                  @endif
                </ul>
              </div>

              {{-- Card 2: CTA Card --}}
              <div class="fmpd-cta-card">
                <h3 class="fmpd-cta-card__title">Want a project like this?</h3>
                <p class="fmpd-cta-card__desc">Get a free site visit and bankable estimate for your land.</p>
                <div class="fmpd-cta-card__actions">
                  @php
                    $phone = $project->contact_phone ?: '+91 7879183931';
                    $phoneRaw = preg_replace('/[^0-9+]/', '', $phone);
                    $waNumber = $project->contact_whatsapp ?: '917879183931';
                    $waClean = preg_replace('/[^0-9]/', '', $waNumber);
                  @endphp
                  <a href="tel:{{ $phoneRaw }}" class="fmpd-cta-btn fmpd-cta-btn--call">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                    </svg>
                    <span>Call {{ $phone }}</span>
                  </a>
                  <a href="https://wa.me/{{ $waClean }}" target="_blank" rel="noopener noreferrer" class="fmpd-cta-btn fmpd-cta-btn--whatsapp">
                    <span>WhatsApp Us</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                  </a>
                </div>
              </div>

            </aside>

          </div>
        </div>
      </section>
    </main>

    {{-- Footer --}}
    <footer class="afm-ft">
      <div class="afm-ft__bottom">
        <div class="afm-ft__bottom-container">
          <div class="afm-ft__copy">
            Copyright &copy; {{ date('Y') }} Future Agri Infra, All rights reserved.
          </div>
        </div>
      </div>
    </footer>
  </div>

  <script>
    // Gallery interaction logic
    document.addEventListener('DOMContentLoaded', function () {
      var gallery = document.querySelector('[data-fmpd-gallery]');
      if (!gallery) return;

      var mainImg = gallery.querySelector('[data-fmpd-main-img]');
      var thumbs  = Array.prototype.slice.call(gallery.querySelectorAll('[data-fmpd-thumb]'));
      if (!mainImg || !thumbs.length) return;

      thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
          var src = thumb.getAttribute('data-fmpd-thumb');
          if (!src || thumb.classList.contains('is-active')) return;

          thumbs.forEach(function (t) { t.classList.remove('is-active'); });
          thumb.classList.add('is-active');

          mainImg.style.opacity = '0.3';
          setTimeout(function () {
            mainImg.src = src;
            var thumbImg = thumb.querySelector('img');
            if (thumbImg && thumbImg.alt) {
              mainImg.alt = thumbImg.alt;
            }
            mainImg.style.opacity = '1';
          }, 120);
        });
      });
    });
  </script>
</body>
</html>

