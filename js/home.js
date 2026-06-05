// ========== DOM & INIT ==========
const header = document.getElementById('header');
const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('navMenu');

document.addEventListener('DOMContentLoaded', function () {
  setupNavigation();
  setupScrollEffects();
<<<<<<< HEAD
<<<<<<< HEAD
  // El carrusel ahora es nativo de Bootstrap 4, no necesita JS manual
=======
<<<<<<< HEAD
  // El carrusel ahora es nativo de Bootstrap 4, no necesita JS manual
=======
  setupCarousel();
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
  // El carrusel ahora es nativo de Bootstrap 4, no necesita JS manual
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
  setupSpecialtyCards();
  setupAnimations();
  setupCounters();
  setupContactForm();
});

// ========== NAVIGATION ==========
function setupNavigation() {
  if (hamburger) {
    hamburger.addEventListener('click', function () {
      hamburger.classList.toggle('active');
      navMenu.classList.toggle('active');
    });
  }

  document.querySelectorAll('.nav-link').forEach(function (link) {
    link.addEventListener('click', function () {
      if (hamburger) hamburger.classList.remove('active');
      if (navMenu) navMenu.classList.remove('active');
    });
  });

  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      var target = document.querySelector(this.getAttribute('href'));
      if (target) {
        var offset = target.offsetTop - 80;
        window.scrollTo({ top: offset, behavior: 'smooth' });
      }
    });
  });
}

function scrollToSection(id) {
  var el = document.getElementById(id);
  if (el) {
    var offset = el.offsetTop - 80;
    window.scrollTo({ top: offset, behavior: 'smooth' });
  }
}

// ========== SCROLL EFFECTS ==========
function setupScrollEffects() {
  window.addEventListener('scroll', function () {
    var scrollY = window.pageYOffset;
    if (header) {
      header.classList.toggle('scrolled', scrollY > 20);
    }
  });
}

<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
// ========== CAROUSEL ==========
function setupCarousel() {
  var track = document.querySelector('.carousel-track');
  var slides = document.querySelectorAll('.carousel-slide');
  var prevBtn = document.getElementById('prevBtn');
  var nextBtn = document.getElementById('nextBtn');
  var indicators = document.querySelectorAll('.indicator');

  if (!track || !slides.length) return;

  var currentSlide = 0;
  var totalSlides = slides.length;
  var autoPlayInterval;

  function updateCarousel() {
    track.style.transform = 'translateX(-' + (currentSlide * 100) + '%)';
    indicators.forEach(function (ind, i) {
      ind.classList.toggle('active', i === currentSlide);
    });
  }

  function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    updateCarousel();
    resetAutoPlay();
  }

  function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    updateCarousel();
    resetAutoPlay();
  }

  function goToSlide(i) {
    currentSlide = i;
    updateCarousel();
    resetAutoPlay();
  }

  function startAutoPlay() { autoPlayInterval = setInterval(nextSlide, 5000); }
  function resetAutoPlay() { clearInterval(autoPlayInterval); startAutoPlay(); }

  if (nextBtn) nextBtn.addEventListener('click', nextSlide);
  if (prevBtn) prevBtn.addEventListener('click', prevSlide);

  indicators.forEach(function (ind, i) {
    ind.addEventListener('click', function () { goToSlide(i); });
  });

  // Touch support
  var touchStartX = 0;
  track.addEventListener('touchstart', function (e) { touchStartX = e.changedTouches[0].screenX; });
  track.addEventListener('touchend', function (e) {
    var diff = touchStartX - e.changedTouches[0].screenX;
    if (Math.abs(diff) > 50) { diff > 0 ? nextSlide() : prevSlide(); }
  });

  track.addEventListener('mouseenter', function () { clearInterval(autoPlayInterval); });
  track.addEventListener('mouseleave', function () { startAutoPlay(); });

  document.addEventListener('visibilitychange', function () {
    document.hidden ? clearInterval(autoPlayInterval) : startAutoPlay();
  });

  updateCarousel();
  startAutoPlay();
}

>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
// ========== SPECIALTY CARDS ==========
function setupSpecialtyCards() {
  var specialtyInfo = {
    general: {
      title: 'Medicina General',
      description: 'Atención integral para adultos y niños, diagnóstico y tratamiento de enfermedades comunes.',
      services: ['Consulta general', 'Chequeos preventivos', 'Vacunación', 'Exámenes de rutina']
    },
    cardiologia: {
      title: 'Cardiología',
      description: 'Especializada en diagnóstico y tratamiento de enfermedades del corazón.',
      services: ['Electrocardiograma', 'Ecocardiograma', 'Prueba de esfuerzo', 'Cateterismo']
    },
    neumonologia: {
      title: 'Neumología',
      description: 'Tratamiento de enfermedades respiratorias y pulmonares.',
      services: ['Espirometría', 'Broncoscopía', 'Pruebas de alergia', 'Tratamiento del asma']
    },
    psicologia: {
      title: 'Psicología',
      description: 'Apoyo emocional y mental para mejorar tu bienestar.',
      services: ['Terapia individual', 'Terapia de pareja', 'Psicoterapia', 'Evaluación psicológica']
    },
    pediatria: {
      title: 'Pediatría',
      description: 'Cuidado especializado para niños desde el nacimiento hasta la adolescencia.',
      services: ['Control de crecimiento', 'Vacunación infantil', 'Chequeos pediátricos', 'Urgencias']
    }
  };

  document.querySelectorAll('.specialty-card').forEach(function (card) {
    card.addEventListener('click', function () {
      var key = this.dataset.specialty;
      var info = specialtyInfo[key];
      if (info) {
        var body = document.getElementById('specialtyModalBody');
        body.innerHTML = '<h3>' + info.title + '</h3><p>' + info.description + '</p><h4>Servicios:</h4><ul>' +
          info.services.map(function (s) { return '<li>' + s + '</li>'; }).join('') + '</ul>';
        document.getElementById('specialtyModal').classList.add('show');
      }
    });
    card.setAttribute('role', 'button');
    card.setAttribute('tabindex', '0');
  });
}

function closeSpecialtyModal() {
  document.getElementById('specialtyModal').classList.remove('show');
}

<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
// Close modal on outside click or Escape
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
window.addEventListener('click', function (e) {
  var modal = document.getElementById('specialtyModal');
  if (e.target === modal) closeSpecialtyModal();
});

document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') closeSpecialtyModal();
});

// ========== SCROLL ANIMATIONS ==========
function setupAnimations() {
  var els = document.querySelectorAll('.about-container, .locations-container, .contact-container, .access-container, .location-card, .role-card');
  els.forEach(function (el) { el.classList.add('animate-on-scroll'); });

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

  els.forEach(function (el) { observer.observe(el); });
}

// ========== STAT COUNTERS ==========
function setupCounters() {
  var counters = document.querySelectorAll('.stat-number[data-count]');
  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        var el = entry.target;
        var target = parseInt(el.dataset.count);
        var current = 0;
        var step = Math.max(1, Math.floor(target / 40));
        var timer = setInterval(function () {
          current += step;
          if (current >= target) { current = target; clearInterval(timer); }
          el.textContent = current;
        }, 40);
        observer.unobserve(el);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach(function (c) { observer.observe(c); });
}

// ========== LOGIN SYSTEM ==========
var currentRole = '';

function showView(viewId) {
  document.querySelectorAll('.view-section').forEach(function (s) { s.classList.remove('active'); });
  document.getElementById(viewId).classList.add('active');
  var loginError = document.getElementById('login-error');
  if (loginError) loginError.classList.remove('show');
}

function openLogin(role, iconClass) {
    currentRole = role;
    document.getElementById('login-rol').value = role;
    document.getElementById('login-user').value = '';
    document.getElementById('login-pass').value = '';

    var badge = document.getElementById('login-badge');
    badge.innerHTML = '<i class="fa-solid ' + iconClass + '"></i> Perfil: ' + role;

    var footer = document.getElementById('signup-redirect');
    if (role === 'administrador' || role === 'asistente') {
        footer.innerHTML = '<i class="fa-solid fa-circle-info"></i> Cuentas administrativas protegidas. Solicita accesos con TI.';
    } else {
        footer.innerHTML = '¿Eres nuevo? <a href="' + APP_URL + '/registro/' + role + '">Regístrate aquí</a>';
    }

    showView('login-view');

<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
    // Scroll al login
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    setTimeout(function () {
        var el = document.getElementById('login-view');
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 100);
}

// AJAX Login
$(document).ready(function () {
    $('#form-login').submit(function (e) {
        e.preventDefault();
        var loginError = document.getElementById('login-error');
        var loginErrorMsg = document.getElementById('login-error-msg');
        var submitBtn = document.getElementById('login-submit-btn');

        loginError.classList.remove('show');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';

        $.ajax({
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            url: APP_URL + '/login',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> ¡Acceso concedido!';
                    window.location.href = APP_URL + '/' + response.redirect;
                } else {
<<<<<<< HEAD
=======
<<<<<<< HEAD
            url: APP_URL + '/login',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> ¡Acceso concedido!';
                    window.location.href = APP_URL + '/' + response.redirect;
                } else {
=======
            url: APP_URL + '/login',  // ✅ Verifica que APP_URL esté definida correctamente
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
           success: function (response) {
    if (response.success) {
        submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> ¡Acceso concedido!';
        // --- CÓDIGO CORREGIDO ---
        // response.redirect ya tiene el formato "panel/paciente"
        window.location.href = APP_URL + '/' + response.redirect;
    } else {
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    loginErrorMsg.textContent = response.error || 'Cédula o contraseña incorrecta';
                    loginError.classList.add('show');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Autenticar Entrada';
                }
            },
            error: function () {
                loginErrorMsg.textContent = 'Error de conexión con el servidor';
                loginError.classList.add('show');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Autenticar Entrada';
            }
        });
    });
<<<<<<< HEAD
<<<<<<< HEAD
});
=======
<<<<<<< HEAD
});
=======
});;
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
});
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0

// ========== MAP ==========
function showMap(location) {
  var urls = {
    'caracas': 'https://maps.google.com/?q=Caracas,Venezuela',
    'caracas-este': 'https://maps.google.com/?q=Caracas+Este,Venezuela',
    'maracaibo': 'https://maps.google.com/?q=Maracaibo,Zulia,Venezuela'
  };
  if (urls[location]) window.open(urls[location], '_blank');
}

// ========== CONTACT FORM ==========
function setupContactForm() {
  var form = document.getElementById('contactForm');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var btn = form.querySelector('.submit-btn');
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
      btn.disabled = true;

      setTimeout(function () {
        btn.innerHTML = '<i class="fas fa-check"></i> ¡Mensaje enviado!';
        btn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
        form.reset();
        setTimeout(function () {
          btn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar Mensaje';
          btn.style.background = '';
          btn.disabled = false;
        }, 3000);
      }, 1500);
    });
  }
<<<<<<< HEAD
<<<<<<< HEAD
}
=======
<<<<<<< HEAD
}
=======
}
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
}
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
