// ==============================
// 1. DOM READY (Single Entry Point)
// ==============================
document.addEventListener("DOMContentLoaded", () => {
  initNiceSelect();
  initSwipers();
  initMobileMenu();
  initCounter();
  initTooltips();
  initAOS();
});


// ==============================
// 2. NICE SELECT (jQuery)
// ==============================
function initNiceSelect() {
  if (window.$ && $('.category-select').length) {
    $('.category-select').niceSelect();
  }
}


// ==============================
// 3. SWIPER INITIALIZER (Reusable)
// ==============================
function createSwiper(selector, slides992 = 1) {
  if (!document.querySelector(selector)) return;

  return new Swiper(selector, {
    loop: true,
    spaceBetween: 30,
    autoplay: {
      delay: 4000,
      disableOnInteraction: false,
    },
    pagination: {
      el: `${selector} .swiper-pagination`,
      clickable: true,
    },
    navigation: {
      nextEl: `${selector} .swiper-button-next`,
      prevEl: `${selector} .swiper-button-prev`,
    },
    breakpoints: {
      0: { slidesPerView: 1 },
      768: { slidesPerView: 2 },
      992: { slidesPerView: slides992 },
    },
  });
}

function initSwipers() {
  createSwiper(".mySwiper", 2);
  createSwiper(".mySwipers", 1);
  createSwiper(".mySwiperAbout", 1);

  // Trust Slider (custom config)
  if (document.querySelector(".trust-slider")) {
    new Swiper(".trust-slider", {
      loop: true,
      spaceBetween: 30,
      autoplay: {
        delay: 3000,
        disableOnInteraction: false,
      },
      pagination: {
        el: ".trusted-slider .swiper-pagination",
        clickable: true,
      },
      navigation: {
        nextEl: ".trusted-slider .swiper-button-next",
        prevEl: ".trusted-slider .swiper-button-prev",
      },
      breakpoints: {
        0: { slidesPerView: 1 },
        768: { slidesPerView: 2 },
        992: { slidesPerView: 3 },
      },
    });
  }
}


// ==============================
// 4. STICKY NAVBAR (jQuery)
// ==============================
$(window).on("scroll", function () {
  $(".sticky-nav").toggleClass(
    "menu_fixed animated fadeInDown",
    $(this).scrollTop() > 250
  );
});


// ==============================
// 5. MOBILE MENU
// ==============================
function initMobileMenu() {
  const openBtn = document.getElementById('menu-open');
  const closeBtn = document.getElementById('menu-close');
  const menu = document.getElementById('mobile-menu');

  if (!openBtn || !closeBtn || !menu) return;

  openBtn.addEventListener('click', () => {
    menu.classList.add('active');
    document.body.classList.add('no-scroll');
  });

  closeBtn.addEventListener('click', () => {
    menu.classList.remove('active');
    document.body.classList.remove('no-scroll');
  });
}


// ==============================
// 6. COUNTER ANIMATION
// ==============================
function initCounter() {
  const counters = document.querySelectorAll(".counter-value");
  const section = document.querySelector(".about-counter");

  if (!section || counters.length === 0) return;

  let started = false;

  const startCounting = () => {
    if (started) return;

    counters.forEach(counter => {
      const target = +counter.dataset.count;
      let current = 0;
      const increment = Math.ceil(target / 100);

      const update = () => {
        current += increment;
        if (current >= target) {
          counter.textContent = target;
        } else {
          counter.textContent = current;
          requestAnimationFrame(update);
        }
      };

      update();
    });

    started = true;
  };

  const observer = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting) {
      startCounting();
      observer.disconnect();
    }
  }, { threshold: 0.4 });

  observer.observe(section);
}


// ==============================
// 7. TOOLTIP (Bootstrap)
// ==============================
function initTooltips() {
  const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  tooltips.forEach(el => new bootstrap.Tooltip(el));
}


// ==============================
// 8. AOS INIT
// ==============================
function initAOS() {
  AOS.init({
    offset: 120,
    delay: 0,
    duration: 800,
    easing: 'ease',
    once: false,
    mirror: false,
  });
}