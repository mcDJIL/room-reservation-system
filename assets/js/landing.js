document.addEventListener('DOMContentLoaded', function () {
  var navbar = document.querySelector('.landing-navbar');
  if (!navbar) {
    return;
  }

  var homeLink = navbar.querySelector('.nav-link[href$="index.php"]');
  var navLinks = Array.prototype.slice.call(navbar.querySelectorAll('.nav-link[href^="#"]'));
  var sections = navLinks.map(function (link) {
    var id = link.getAttribute('href').slice(1);
    return document.getElementById(id);
  }).filter(Boolean);

  if (!sections.length) {
    return;
  }

  function setActiveById(sectionId) {
    if (homeLink) {
      homeLink.classList.remove('active');
      homeLink.removeAttribute('aria-current');
    }

    navLinks.forEach(function (link) {
      var isActive = link.getAttribute('href') === '#' + sectionId;
      link.classList.toggle('active', isActive);
      if (isActive) {
        link.setAttribute('aria-current', 'true');
      } else {
        link.removeAttribute('aria-current');
      }
    });
  }

  function setActiveHome() {
    navLinks.forEach(function (link) {
      link.classList.remove('active');
      link.removeAttribute('aria-current');
    });

    if (homeLink) {
      homeLink.classList.add('active');
      homeLink.setAttribute('aria-current', 'true');
    }
  }

  function getCurrentSection() {
    var offset = navbar.offsetHeight + 16;
    var firstSectionTop = sections[0].getBoundingClientRect().top;

    if (firstSectionTop > offset) {
      return null;
    }

    var currentSection = null;

    sections.forEach(function (section) {
      var rect = section.getBoundingClientRect();
      if (rect.top - offset <= 0) {
        currentSection = section;
      }
    });

    return currentSection;
  }

  function updateActiveFromScroll() {
    var currentSection = getCurrentSection();
    if (currentSection && currentSection.id) {
      setActiveById(currentSection.id);
    } else {
      setActiveHome();
    }
  }

  navLinks.forEach(function (link) {
    link.addEventListener('click', function () {
      window.requestAnimationFrame(updateActiveFromScroll);
    });
  });

  var scrollTimeout = null;
  window.addEventListener('scroll', function () {
    if (scrollTimeout) {
      window.cancelAnimationFrame(scrollTimeout);
    }
    scrollTimeout = window.requestAnimationFrame(updateActiveFromScroll);
  }, { passive: true });

  window.addEventListener('hashchange', function () {
    var hash = window.location.hash.replace('#', '');
    if (hash) {
      window.requestAnimationFrame(updateActiveFromScroll);
    }
  });

  if (window.location.hash) {
    window.setTimeout(updateActiveFromScroll, 0);
  } else {
    updateActiveFromScroll();
  }
});